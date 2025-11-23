<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Saldo;
use App\Models\SaldoTransfer;
use App\Models\Income;
use App\Models\Expend;
use App\Models\Invoice;
use App\Models\Modal;
use App\Models\Pemasukkan;
use App\Models\ExpenseCategory;
use App\Models\SaldoTopup;
use App\Models\SalaryPayment;
use App\Models\User;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SaldoExport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaldoManagementController extends Controller
{
    protected array $accounts = ['kasir','bank','tunai'];

    public function index(Request $request)
    {
        // Ensure base accounts exist
        foreach ($this->accounts as $acc) {
            Saldo::firstOrCreate(['account'=>$acc],['balance'=>0]);
        }

        // Compute Kasir balance from approved incomes minus all valid expenses (exclude modal)
        $approvedIncome = (float) Income::whereHas('sessionReport', function($q){ $q->where('status','approved'); })->sum('amount');
        $totalExpense = (float) Expend::sum('amount') + (float) SalaryPayment::sum('amount');
        $kasirTopups = (float) SaldoTopup::where('account','kasir')->sum('amount');
        $kasirComputed = $approvedIncome - $totalExpense + $kasirTopups;

        // Load stored balances for other accounts, override kasir with computed value
        $balances = Saldo::all()->keyBy('account');
        if (isset($balances['kasir'])) {
            $balances['kasir']->balance = $kasirComputed;
        }
        $saldoTotal = $balances->sum('balance');

        // Separate card for Modal/Asset value (not included in saldo total)
        $modalValue = (float) Modal::sum('harga');

        $transfers = SaldoTransfer::with(['user'])->orderByDesc('id')->paginate(20);
        $topups = SaldoTopup::with('user')->orderByDesc('id')->paginate(10);
        $users = User::orderBy('name')->get(['id','name']);

        return view('owner.saldo.index', [
            'balances' => $balances,
            'saldoTotal' => $saldoTotal,
            'transfers' => $transfers,
            'topups' => $topups,
            'modalValue' => $modalValue,
            'users' => $users,
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'source_account' => 'required|in:kasir,bank,tunai',
            'destination_account' => 'required|in:kasir,bank,tunai|different:source_account',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255'
        ]);
        $source = Saldo::where('account',$request->source_account)->firstOrFail();
        $dest = Saldo::where('account',$request->destination_account)->firstOrFail();
        $amount = (float) $request->amount;
        if ($amount > $source->balance) {
            return back()->with('error','Jumlah melebihi saldo sumber.');
        }

        $now = Carbon::now();
        $date = $now->toDateString();
        $time = $now->format('H:i:s');
        $session = $now->hour < 15 ? 'pagi' : 'sore';
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // Update balances
            $source->balance -= $amount;
            $source->save();
            $dest->balance += $amount;
            $dest->save();

            // Ensure transfer categories exist
            $incomeCategory = Pemasukkan::firstOrCreate(['nama_pemasukkan'=>'Transfer Masuk']);
            $expenseCategory = ExpenseCategory::firstOrCreate(['name'=>'Transfer Keluar'],[
                'type'=>'harian','created_by'=>$userId,'is_active'=>true
            ]);

            // Create expense (source)
            $expend = Expend::create([
                'user_id'=>$userId,
                'session'=>$session,
                'amount'=>$amount,
                'date'=>$date,
                'time'=>$now,
                'type'=>'harian',
                'category_id'=>$expenseCategory->id,
                'description'=>'Transfer '.$request->source_account.' -> '.$request->destination_account.' '.($request->note ?? ''),
            ]);
            $invoiceExpense = Invoice::create([
                'number'=>'INV-'.$now->format('Ymd-His').'-TXE-'.$userId,
                'type'=>'expense',
                'cashier_id'=>$userId,
                'payment_type'=>'cash',
                'date'=>$date,
                'time'=>$time,
                'subtotal'=>$amount,
                'tax'=>0,
                'total'=>$amount,
            ]);
            $expend->invoice_id = $invoiceExpense->id;
            $expend->save();

            // Create income (destination)
            $income = Income::create([
                'user_id'=>$userId,
                'pemasukkan_id'=>$incomeCategory->id,
                'session'=>$session,
                'amount'=>$amount,
                'qty'=>1,
                'unit_price'=>$amount,
                'date'=>$date,
                'time'=>$now,
                'description'=>'Transfer '.$request->source_account.' -> '.$request->destination_account.' '.($request->note ?? ''),
                'payment_type'=>'cash'
            ]);
            $invoiceIncome = Invoice::create([
                'number'=>'INV-'.$now->format('Ymd-His').'-TXI-'.$userId,
                'type'=>'income',
                'cashier_id'=>$userId,
                'payment_type'=>'cash',
                'date'=>$date,
                'time'=>$time,
                'subtotal'=>$amount,
                'tax'=>0,
                'total'=>$amount,
            ]);
            $income->invoice_id = $invoiceIncome->id;
            $income->save();

            // Record transfer
            SaldoTransfer::create([
                'source_account'=>$request->source_account,
                'destination_account'=>$request->destination_account,
                'amount'=>$amount,
                'user_id'=>$userId,
                'date'=>$date,
                'time'=>$time,
                'note'=>$request->note,
                'income_id'=>$income->id,
                'expend_id'=>$expend->id,
                'invoice_income_id'=>$invoiceIncome->id,
                'invoice_expend_id'=>$invoiceExpense->id,
            ]);

            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Gagal transfer: '.$e->getMessage());
        }
        return back()->with('success','Transfer berhasil disimpan.');
    }

    public function topupStore(Request $request)
    {
        $data = $request->validate([
            'account' => 'required|in:kasir,bank,tunai',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $now = Carbon::now();
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // Persist log
            $topup = SaldoTopup::create([
                'account' => $data['account'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'user_id' => $userId,
                'date' => $now->toDateString(),
                'time' => $now->format('H:i:s'),
            ]);

            // Apply to non-kasir accounts' stored balances. Kasir is computed; keep as log only.
            if (in_array($data['account'], ['bank','tunai'])) {
                $saldo = Saldo::firstOrCreate(['account' => $data['account']], ['balance' => 0]);
                $saldo->balance += (float) $data['amount'];
                $saldo->save();
            }

            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Gagal menambah saldo: '.$e->getMessage());
        }
        return back()->with('success','Saldo berhasil ditambahkan.');
    }

    public function topupUpdate(Request $request, SaldoTopup $topup)
    {
        $data = $request->validate([
            'account' => 'required|in:kasir,bank,tunai',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            if (in_array($topup->account, ['bank','tunai'])) {
                $saldo = Saldo::firstOrCreate(['account' => $topup->account], ['balance' => 0]);
                $saldo->balance -= (float) $topup->amount; // revert old
                $saldo->save();
            }

            $topup->account = $data['account'];
            $topup->amount = $data['amount'];
            $topup->note = $data['note'] ?? null;
            $topup->save();

            if (in_array($topup->account, ['bank','tunai'])) {
                $saldo = Saldo::firstOrCreate(['account' => $topup->account], ['balance' => 0]);
                $saldo->balance += (float) $topup->amount; // apply new
                $saldo->save();
            }

            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Gagal mengubah topup: '.$e->getMessage());
        }
        return back()->with('success','Topup berhasil diubah.');
    }

    public function topupDestroy(SaldoTopup $topup)
    {
        DB::beginTransaction();
        try {
            if (in_array($topup->account, ['bank','tunai'])) {
                $saldo = Saldo::firstOrCreate(['account' => $topup->account], ['balance' => 0]);
                $saldo->balance -= (float) $topup->amount;
                $saldo->save();
            }
            $topup->delete();
            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Gagal menghapus topup: '.$e->getMessage());
        }
        return back()->with('success','Topup berhasil dihapus.');
    }

    public function topupExport(Request $request)
    {
        $filename = 'saldo_topups_'.Carbon::now()->format('Ymd_His').'.csv';
        $rows = SaldoTopup::with('user')->orderBy('date')->orderBy('time')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal','Waktu','Akun','Jumlah','Catatan','User']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->date ? $r->date->format('Y-m-d') : '',
                    $r->time ? (is_string($r->time) ? $r->time : $r->time->format('H:i:s')) : '',
                    $r->account,
                    $r->amount,
                    $r->note,
                    $r->user->name ?? '-',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCustom(Request $request)
    {
        // permission gate
        $u = auth()->user();
        if (!($u && ($u->role==='owner' || (method_exists($u,'hasPermission') && $u->hasPermission('saldo.export'))))) {
            abort(403);
        }
        $request->validate([
            'dataset' => 'required|in:transfer,topup,both',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'format' => 'nullable|in:csv,pdf,xlsx',
            'user_id' => 'nullable|integer',
            'account' => 'nullable|in:kasir,bank,tunai',
        ]);

        $dataset = $request->input('dataset');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $format = $request->input('format','csv');
        $userId = $request->input('user_id');
        $accountFilter = $request->input('account');

        // Build datasets lazily with filters
        $transferQ = SaldoTransfer::with('user')->orderBy('date')->orderBy('time');
        $topupQ = SaldoTopup::with('user')->orderBy('date')->orderBy('time');
        if ($start && $end) { $transferQ->whereBetween('date', [$start,$end]); $topupQ->whereBetween('date', [$start,$end]); }
        if ($userId) { $transferQ->where('user_id',$userId); $topupQ->where('user_id',$userId); }
        if ($accountFilter) {
            $transferQ->where(function($q) use ($accountFilter){
                $q->where('source_account',$accountFilter)->orWhere('destination_account',$accountFilter);
            });
            $topupQ->where('account',$accountFilter);
        }

        if ($format === 'pdf') {
            $rowsT = ($dataset==='topup') ? collect() : $transferQ->get();
            $rowsU = ($dataset==='transfer') ? collect() : $topupQ->get();
            $pdf = PDF::loadView('owner.saldo.export-pdf', [
                'transfers' => $rowsT,
                'topups' => $rowsU,
                'start' => $start,
                'end' => $end,
                'dataset' => $dataset,
                'account' => $accountFilter,
                'user' => $userId ? User::find($userId) : null,
                'generatedAt' => Carbon::now()->format('d F Y H:i:s')
            ])->setPaper('a4','landscape');
            return $pdf->download('saldo-export-'.($dataset).'-'.Carbon::now()->format('Ymd_His').'.pdf');
        }

        if ($format === 'xlsx') {
            $rowsT = ($dataset==='topup') ? collect() : $transferQ->get();
            $rowsU = ($dataset==='transfer') ? collect() : $topupQ->get();
            return Excel::download(new SaldoExport($rowsT, $rowsU), 'saldo-export-'.$dataset.'-'.Carbon::now()->format('Ymd_His').'.xlsx');
        }

        // default CSV
        $filename = 'saldo_export_'.Carbon::now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($dataset, $transferQ, $topupQ) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tipe','Tanggal','Waktu','Sumber','Tujuan','Akun','Jumlah','Catatan','User','Invoice Income','Invoice Expense']);

            if ($dataset === 'transfer' || $dataset === 'both') {
                foreach ($transferQ->cursor() as $t) {
                    fputcsv($out, [
                        'transfer',
                        $t->date,
                        $t->time,
                        $t->source_account,
                        $t->destination_account,
                        '',
                        $t->amount,
                        $t->note,
                        optional($t->user)->name,
                        $t->invoice_income_id,
                        $t->invoice_expend_id,
                    ]);
                }
            }

            if ($dataset === 'topup' || $dataset === 'both') {
                foreach ($topupQ->cursor() as $r) {
                    fputcsv($out, [
                        'topup',
                        optional($r->date)->format('Y-m-d'),
                        is_string($r->time) ? $r->time : (optional($r->time)->format('H:i:s')),
                        '',
                        '',
                        $r->account,
                        $r->amount,
                        $r->note,
                        optional($r->user)->name,
                        '',
                        '',
                    ]);
                }
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
