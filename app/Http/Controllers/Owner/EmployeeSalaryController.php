<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\UserSalary;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;

class EmployeeSalaryController extends Controller
{
    private function authorizeOwnerOrPermission(string $perm)
    {
        $u = auth()->user();
        if (!($u && $u instanceof \App\Models\User)) abort(403);
        if ($u->role === 'owner') return true;
        if (method_exists($u,'hasPermission') && $u->hasPermission($perm)) return true;
        abort(403);
    }

    public function index(Request $request)
    {
        $this->authorizeOwnerOrPermission('salary.read');
        $month = (int)($request->get('month') ?: now()->month);
        $year = (int)($request->get('year') ?: now()->year);
        $employees = UserSalary::orderBy('name')->get();
        $payments = SalaryPayment::with('employee')
            ->where('month',$month)->where('year',$year)
            ->orderByDesc('paid_date')->get();
        return view('owner.employee-salary.index', compact('employees','payments','month','year'));
    }

    public function createEmployee()
    {
        $this->authorizeOwnerOrPermission('salary.create');
        $employee = new UserSalary();
        return view('owner.employee-salary.employee-form', compact('employee'));
    }

    public function storeEmployee(Request $request)
    {
        $this->authorizeOwnerOrPermission('salary.create');
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|max:100',
            'base_salary' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_permanent' => 'nullable|boolean',
        ]);
        // Force active on creation regardless of input
        $data['active'] = true;
        if ($request->boolean('is_permanent')) {
            $data['is_permanent'] = true;
            $data['end_date'] = null;
        }
        UserSalary::create($data);
        return redirect()->route('owner.employee-salary.index')->with('success','Karyawan ditambahkan.');
    }

    public function editEmployee(UserSalary $employee)
    {
        $this->authorizeOwnerOrPermission('salary.update');
        return view('owner.employee-salary.employee-form', compact('employee'));
    }

    public function updateEmployee(Request $request, UserSalary $employee)
    {
        $this->authorizeOwnerOrPermission('salary.update');
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|max:100',
            'base_salary' => 'nullable|numeric|min:0',
            'active' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_permanent' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active', $employee->active);
        if ($request->boolean('is_permanent')) {
            $data['is_permanent'] = true;
            $data['end_date'] = null;
        } else {
            $data['is_permanent'] = false;
        }
        // Auto-deactivate if end date has passed and not permanent
        if (!empty($data['end_date']) && !$data['is_permanent']) {
            try { $ed = \Carbon\Carbon::parse($data['end_date']); } catch (\Exception $e) { $ed = null; }
            if ($ed && $ed->isPast()) {
                $data['active'] = false;
            }
        }
        $employee->update($data);
        return redirect()->route('owner.employee-salary.index')->with('success','Karyawan diperbarui.');
    }

    public function destroyEmployee(UserSalary $employee)
    {
        $this->authorizeOwnerOrPermission('salary.delete');
        $employee->delete();
        return redirect()->route('owner.employee-salary.index')->with('success','Karyawan dihapus.');
    }

    public function createPayment()
    {
        $this->authorizeOwnerOrPermission('salary.create');
        $employees = UserSalary::where('active',true)
            ->where(function($q){
                $q->where('is_permanent', true)
                  ->orWhereNull('end_date')
                  ->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->orderBy('name')->get();
        $month = now()->month; $year = now()->year;
        return view('owner.employee-salary.payment-form', compact('employees','month','year'));
    }

    public function storePayment(Request $request)
    {
        $this->authorizeOwnerOrPermission('salary.create');
        $data = $request->validate([
            'user_salary_id' => 'required|exists:user_salaries,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'method' => 'required|in:cash,qris,transfer',
            'fund_source' => 'required|in:kasir,bank,tunai',
            'description' => 'nullable|string',
            'deduction_type' => 'nullable|in:percent,fixed',
            'deduction_value' => 'nullable|numeric|min:0',
            'deduction_desc' => 'nullable|string',
            'bonus_type' => 'nullable|in:percent,fixed',
            'bonus_value' => 'nullable|numeric|min:0',
            'bonus_desc' => 'nullable|string',
        ]);
        $employee = UserSalary::findOrFail($data['user_salary_id']);
        // Validate employee is currently active
        if (!$employee->is_currently_active) {
            return back()->withErrors(['user_salary_id' => 'Karyawan tidak aktif atau sudah melewati tanggal keluar.'])->withInput();
        }
        // Ensure one payment per employee per month/year
        $exists = SalaryPayment::where('user_salary_id',$data['user_salary_id'])
            ->where('month',$data['month'])->where('year',$data['year'])->exists();
        if ($exists) {
            return back()->withErrors(['user_salary_id' => 'Karyawan ini sudah menerima gaji untuk periode tersebut.'])->withInput();
        }
        $base = (float)($employee->base_salary ?? 0);

        $deduction = 0.0;
        if (!empty($data['deduction_type']) && !empty($data['deduction_value'])) {
            if ($data['deduction_type'] === 'percent') {
                $perc = min(max((float)$data['deduction_value'], 0), 100);
                $deduction = round($base * ($perc/100), 2);
            } else {
                $deduction = (float)$data['deduction_value'];
            }
        }

        $bonus = 0.0;
        if (!empty($data['bonus_type']) && !empty($data['bonus_value'])) {
            if ($data['bonus_type'] === 'percent') {
                $percB = min(max((float)$data['bonus_value'], 0), 100);
                $bonus = round($base * ($percB/100), 2);
            } else {
                $bonus = (float)$data['bonus_value'];
            }
        }

        // Always compute net based on base salary, deduction, and bonus
        $net = max(0, $base - $deduction + $bonus);

        $payload = array_merge($data, [
            'base_salary' => $base,
            'amount' => $net,
            'paid_date' => now(),
            'created_by' => auth()->id(),
        ]);
        \DB::transaction(function() use ($payload, $net) {
            $payment = SalaryPayment::create($payload);
            // Adjust saldo for bank/tunai immediately. Kasir saldo computed from reports so no direct write.
            if (in_array($payment->fund_source, ['bank','tunai'])) {
                $saldo = \App\Models\Saldo::firstOrCreate(['account'=>$payment->fund_source], ['balance'=>0]);
                $saldo->balance -= (float) $net;
                $saldo->save();
            }
        });
        return redirect()->route('owner.employee-salary.index',["month"=>$data['month'],"year"=>$data['year']])->with('success','Gaji dibayarkan.');
    }

    public function invoice(SalaryPayment $payment)
    {
        $this->authorizeOwnerOrPermission('salary.read');
        return view('owner.invoice-salary.show', compact('payment'));
    }

    public function invoicePrint(SalaryPayment $payment)
    {
        $this->authorizeOwnerOrPermission('salary.read');
        return view('owner.invoice-salary.print', compact('payment'));
    }
}
