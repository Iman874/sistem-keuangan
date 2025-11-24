<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Expend;
use App\Models\Pemasukkan;
use App\Models\ExpenseCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AnalysisController extends Controller
{
    public function finance(Request $request)
    {
        // Mode: kategori / pantau (default kategori)
        $mode = $request->input('mode','kategori');

        // Period: daily|weekly|monthly|yearly (default monthly = bulan ini)
        $period = $request->input('period','monthly');

        $now = Carbon::now();
        // Capture dynamic selectors
        $dayDate   = $request->input('day_date'); // for daily
        $weekYear  = (int)($request->input('week_year') ?: $now->year);
        $weekMonth = (int)($request->input('week_month') ?: $now->month);
        $weekIndex = (int)($request->input('week_index') ?: 1); // 1..N
        $monthYear = (int)($request->input('month_year') ?: $now->year);
        $monthMonth= (int)($request->input('month_month') ?: $now->month);
        $yearYear  = (int)($request->input('year_year') ?: $now->year);

        // Compute start/end based on period
        switch($period){
            case 'daily':
                $targetDay = $dayDate ? Carbon::parse($dayDate) : $now;
                $startDate = $targetDay->copy()->startOfDay();
                $endDate   = $targetDay->copy()->endOfDay();
                break;
            case 'weekly':
                $monthStart = Carbon::create($weekYear,$weekMonth,1)->startOfDay();
                $monthEnd   = $monthStart->copy()->endOfMonth();
                // Build array of week starts (Monday-based) within month
                $firstWeekStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
                $weeks = [];
                $cursor = $firstWeekStart->copy();
                $i = 1;
                while($cursor->lte($monthEnd)){
                    $wStart = $cursor->copy();
                    $wEnd   = $cursor->copy()->endOfWeek(Carbon::MONDAY);
                    // clamp to month bounds
                    if($wEnd->lt($monthStart)){ $cursor->addWeek(); continue; }
                    $clampedStart = $wStart->lt($monthStart)? $monthStart->copy(): $wStart;
                    $clampedEnd   = $wEnd->gt($monthEnd)? $monthEnd->copy(): $wEnd;
                    $weeks[$i] = ['start'=>$clampedStart,'end'=>$clampedEnd];
                    $i++; $cursor->addWeek();
                }
                // Ensure valid week index
                if(!array_key_exists($weekIndex,$weeks)){ $weekIndex = array_key_first($weeks); }
                $startDate = $weeks[$weekIndex]['start'];
                $endDate   = $weeks[$weekIndex]['end'];
                break;
            case 'yearly':
                $startDate = Carbon::create($yearYear,1,1)->startOfDay();
                $endDate   = $startDate->copy()->endOfYear();
                break;
            default: // monthly
                $startDate = Carbon::create($monthYear,$monthMonth,1)->startOfDay();
                $endDate   = $startDate->copy()->endOfMonth();
        }

        // Role filter (optional)
        $role = $request->input('role');

        // Global filter detail (from card)
        $filterRoles = $request->input('roles', []); // array
        $filterUsers = $request->input('users', []); // array
        $filterIncomeCats = $request->input('income_categories', []); // array
        $filterExpenseCats = $request->input('expense_categories', []); // array

        // Additional type filters
        $incomeType = $request->input('income_type'); // indoor|outdoor
        $expenseType = $request->input('expense_type'); // harian|bulanan

        // Dynamic category time series filters
        $dynActive = $request->boolean('dyn_active');
        $dynIncomeCat = $request->input('dyn_income_cat');
        $dynExpenseCat = $request->input('dyn_expense_cat');

        // ===== Income Category Aggregation =====
        $incomeQuery = Income::query()
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with(['category','user']);
        // Global filter detail
        if($filterUsers && count($filterUsers)>0){
            $incomeQuery->whereIn('user_id', $filterUsers);
        } else if($filterRoles && count($filterRoles)>0){
            $incomeQuery->whereHas('user', function($q) use ($filterRoles) {
                $q->whereIn('role', $filterRoles);
            });
        } else if($role){
            $incomeQuery->whereHas('user', fn($q)=>$q->where('role',$role));
        }
        if($incomeType){
            $incomeQuery->whereHas('category', fn($q)=>$q->where('type',$incomeType));
        }
        if($filterIncomeCats && count($filterIncomeCats)>0){
            $incomeQuery->whereIn('pemasukkan_id', $filterIncomeCats);
        }
        $rawIncome = $incomeQuery->select('pemasukkan_id', DB::raw('SUM(amount) as total'))
            ->groupBy('pemasukkan_id')->get();
        $incomeCatMap = Pemasukkan::pluck('nama_pemasukkan','id');
        $incomeCategories = [];
        $incomeValues = [];
        foreach($rawIncome as $row){
            if($row->pemasukkan_id === null){
                $incomeCategories[] = 'Lainnya';
            } else {
                $incomeCategories[] = $incomeCatMap[$row->pemasukkan_id] ?? 'Kategori Dihapus';
            }
            $incomeValues[] = (int)$row->total;
        }
        // Sort descending combine others beyond top 5
        $incomeCombined = collect($incomeCategories)->zip($incomeValues)->map(function($pair){return ['name'=>$pair[0],'value'=>$pair[1]];})->sortByDesc('value')->values();
        $incomeTop = $incomeCombined->take(5);
        $incomeOthersTotal = $incomeCombined->skip(5)->sum('value');
        if($incomeOthersTotal>0){
            $incomeTop->push(['name'=>'Kategori Lainnya','value'=>$incomeOthersTotal]);
        }

        $incomeChartLabels = $incomeTop->pluck('name');
        $incomeChartData = $incomeTop->pluck('value');

        // ===== Expense Category Aggregation =====
        $expenseQuery = Expend::query()
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with(['category','user']);
        // Global filter detail
        if($filterUsers && count($filterUsers)>0){
            $expenseQuery->whereIn('user_id', $filterUsers);
        } else if($filterRoles && count($filterRoles)>0){
            $expenseQuery->whereHas('user', function($q) use ($filterRoles) {
                $q->whereIn('role', $filterRoles);
            });
        } else if($role){
            $expenseQuery->whereHas('user', fn($q)=>$q->where('role',$role));
        }
        if($expenseType){
            $expenseQuery->whereHas('category', fn($q)=>$q->where('type',$expenseType));
        }
        if($filterExpenseCats && count($filterExpenseCats)>0){
            $expenseQuery->whereIn('category_id', $filterExpenseCats);
        }
        $rawExpense = $expenseQuery->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')->get();
        // ExpenseCategory column is 'name'
        $expenseCatMap = ExpenseCategory::pluck('name','id');
        $expenseCategoriesArr = [];
        $expenseValues = [];
        foreach($rawExpense as $row){
            if($row->category_id === null){
                $expenseCategoriesArr[] = 'Lainnya';
            } else {
                $expenseCategoriesArr[] = $expenseCatMap[$row->category_id] ?? 'Kategori Dihapus';
            }
            $expenseValues[] = (int)$row->total;
        }
        $expenseCombined = collect($expenseCategoriesArr)->zip($expenseValues)->map(function($pair){return ['name'=>$pair[0],'value'=>$pair[1]];})->sortByDesc('value')->values();
        $expenseTop = $expenseCombined->take(5);
        $expenseOthersTotal = $expenseCombined->skip(5)->sum('value');
        if($expenseOthersTotal>0){
            $expenseTop->push(['name'=>'Kategori Lainnya','value'=>$expenseOthersTotal]);
        }
        $expenseChartLabels = $expenseTop->pluck('name');
        $expenseChartData = $expenseTop->pluck('value');

        // Ringkasan untuk mode kategori
        $summaryIncome = $incomeCombined->map(function($row){
            return [
                'name' => $row['name'],
                'total' => $row['value'],
            ];
        });

        $summaryExpense = $expenseCombined->map(function($row){
            return [
                'name' => $row['name'],
                'total' => $row['value'],
            ];
        });

        $roles = User::select('role')->distinct()->pluck('role');
        // Ambil user per role
        $usersByRole = [];
        foreach($roles as $r){
            $usersByRole[$r] = User::where('role', $r)->orderBy('name')->get();
        }

        // Ambil user terpilih dari request (array id)
        $userSelected = $request->input('users', []);

        $presetCategoriesIncome = Pemasukkan::all();
        $presetCategoriesExpense = ExpenseCategory::all();

        // ===== Dynamic Time Series (Optional) =====
        $dynIncomeLabels = collect();
        $dynIncomeData = collect();
        $dynExpenseLabels = collect();
        $dynExpenseData = collect();
        if($dynActive){
            // Helper closure to build buckets
            $buildBuckets = function($period, Carbon $startDate, Carbon $endDate){
                switch($period){
                    case 'daily': // hours 0-23
                        return collect(range(0,23))->map(fn($h)=>['key'=>$h,'label'=>str_pad($h,2,'0',STR_PAD_LEFT)]);
                    case 'weekly': // days within range
                    case 'monthly': // days within range
                        $labels = collect();
                        $cursor = $startDate->copy();
                        while($cursor->lte($endDate)){
                            $labels->push(['key'=>$cursor->format('Y-m-d'),'label'=>$cursor->format('d')]);
                            $cursor->addDay();
                        }
                        return $labels;
                    case 'yearly': // months Jan-Dec
                        return collect(range(1,12))->map(fn($m)=>['key'=>$m,'label'=>Carbon::create(null,$m,1)->locale('id')->isoFormat('MMM')]);
                    default:
                        return collect();
                }
            };
            $incomeBuckets = $buildBuckets($period,$startDate,$endDate);
            $expenseBuckets = $incomeBuckets; // same structure

            // Fetch records for selected categories
            if($dynIncomeCat){
                $incomeRecords = Income::where('pemasukkan_id',$dynIncomeCat)
                    ->whereBetween('date',[$startDate->format('Y-m-d'),$endDate->format('Y-m-d')])
                    ->get(['date','amount']);
            } else { $incomeRecords = collect(); }
            if($dynExpenseCat){
                $expenseRecords = Expend::where('category_id',$dynExpenseCat)
                    ->whereBetween('date',[$startDate->format('Y-m-d'),$endDate->format('Y-m-d')])
                    ->get(['date','amount']);
            } else { $expenseRecords = collect(); }

            // Aggregate per bucket
            $incomeAgg = [];
            foreach($incomeBuckets as $b){ $incomeAgg[$b['key']] = 0; }
            foreach($incomeRecords as $rec){
                $dt = Carbon::parse($rec->date);
                switch($period){
                    case 'daily': $key = (int)$dt->format('H'); break;
                    case 'weekly':
                    case 'monthly': $key = $dt->format('Y-m-d'); break;
                    case 'yearly': $key = (int)$dt->format('n'); break;
                    default: $key=null; break;
                }
                if($key!==null && array_key_exists($key,$incomeAgg)){
                    $incomeAgg[$key] += (int)$rec->amount;
                }
            }
            $expenseAgg = [];
            foreach($expenseBuckets as $b){ $expenseAgg[$b['key']] = 0; }
            foreach($expenseRecords as $rec){
                $dt = Carbon::parse($rec->date);
                switch($period){
                    case 'daily': $key = (int)$dt->format('H'); break;
                    case 'weekly':
                    case 'monthly': $key = $dt->format('Y-m-d'); break;
                    case 'yearly': $key = (int)$dt->format('n'); break;
                    default: $key=null; break;
                }
                if($key!==null && array_key_exists($key,$expenseAgg)){
                    $expenseAgg[$key] += (int)$rec->amount;
                }
            }

            $dynIncomeLabels = $incomeBuckets->pluck('label');
            $dynIncomeData = collect($incomeAgg)->values();
            $dynExpenseLabels = $expenseBuckets->pluck('label');
            $dynExpenseData = collect($expenseAgg)->values();
        }

        // Resolve selected category names for headers
        $dynIncomeName = null;
        if($dynIncomeCat){
            $foundIncomeCat = $presetCategoriesIncome->firstWhere('id',(int)$dynIncomeCat);
            $dynIncomeName = $foundIncomeCat ? $foundIncomeCat->nama_pemasukkan : null;
        }
        $dynExpenseName = null;
        if($dynExpenseCat){
            $foundExpenseCat = $presetCategoriesExpense->firstWhere('id',(int)$dynExpenseCat);
            $dynExpenseName = $foundExpenseCat ? $foundExpenseCat->name : null;
        }

        // ===== Pantau Keuangan Mode Data =====
        $monitorTransactions = collect();
        $monitorIncomeCategories = collect();
        $monitorExpenseCategories = collect();
        if($mode==='pantau'){
            // Base queries reuse period filters & role/type filters
            $monitorIncomeQ = Income::query()->whereBetween('date',[$startDate->format('Y-m-d'),$endDate->format('Y-m-d')])->with(['category','user']);
            if($role){ $monitorIncomeQ->whereHas('user',fn($q)=>$q->where('role',$role)); }
            if($incomeType){ $monitorIncomeQ->whereHas('category',fn($q)=>$q->where('type',$incomeType)); }
            $monitorIncome = $monitorIncomeQ->get();

            $monitorExpenseQ = Expend::query()->whereBetween('date',[$startDate->format('Y-m-d'),$endDate->format('Y-m-d')])->with(['category','user']);
            if($role){ $monitorExpenseQ->whereHas('user',fn($q)=>$q->where('role',$role)); }
            if($expenseType){ $monitorExpenseQ->whereHas('category',fn($q)=>$q->where('type',$expenseType)); }
            $monitorExpense = $monitorExpenseQ->get();

            // Map to unified structure
            $monitorTransactions = $monitorIncome->map(function($row){
                return [
                    'type' => 'Pemasukkan',
                    'date' => $row->date,
                    'category' => $row->pemasukkan_id ? ($row->category? $row->category->nama_pemasukkan : 'Kategori Dihapus') : 'Lainnya',
                    'amount' => (int)$row->amount,
                    'user' => $row->user? $row->user->name : '-',
                    'role' => $row->user? $row->user->role : '-',
                ];
            })->merge(
                $monitorExpense->map(function($row){
                    return [
                        'type' => 'Pengeluaran',
                        'date' => $row->date,
                        'category' => $row->category_id ? ($row->category? $row->category->name : 'Kategori Dihapus') : 'Lainnya',
                        'amount' => (int)$row->amount,
                        'user' => $row->user? $row->user->name : '-',
                        'role' => $row->user? $row->user->role : '-',
                    ];
                })
            )->sortByDesc('date')->values();

            // Category totals
            $monitorIncomeCategories = $monitorIncome->groupBy('pemasukkan_id')->map(function($grp,$key) use ($incomeCatMap){
                $name = $key? ($incomeCatMap[$key] ?? 'Kategori Dihapus') : 'Lainnya';
                return ['name'=>$name,'total'=>$grp->sum('amount')];
            })->values()->sortByDesc('total');
            $monitorExpenseCategories = $monitorExpense->groupBy('category_id')->map(function($grp,$key) use ($expenseCatMap){
                $name = $key? ($expenseCatMap[$key] ?? 'Kategori Dihapus') : 'Lainnya';
                return ['name'=>$name,'total'=>$grp->sum('amount')];
            })->values()->sortByDesc('total');
        }

        // Auxiliary lists for UI
        $yearsList = collect(range($now->year, $now->year - 4))->values();
        $monthsList = collect(range(1,12))->map(function($m){ return ['num'=>$m,'label'=>Carbon::create(null,$m,1)->locale('id')->isoFormat('MMMM')]; });
        $weeksList = [];
        if($period==='weekly'){
            $monthStartTemp = Carbon::create($weekYear,$weekMonth,1);
            $monthEndTemp   = $monthStartTemp->copy()->endOfMonth();
            $firstWeekStartTemp = $monthStartTemp->copy()->startOfWeek(Carbon::MONDAY);
            $cursorTemp = $firstWeekStartTemp->copy();
            $iTemp=1;
            while($cursorTemp->lte($monthEndTemp)){
                $wStartT = $cursorTemp->copy();
                $wEndT   = $cursorTemp->copy()->endOfWeek(Carbon::MONDAY);
                if($wEndT->lt($monthStartTemp)){ $cursorTemp->addWeek(); continue; }
                $clampedStartT = $wStartT->lt($monthStartTemp)? $monthStartTemp->copy(): $wStartT;
                $clampedEndT   = $wEndT->gt($monthEndTemp)? $monthEndTemp->copy(): $wEndT;
                $weeksList[$iTemp] = $clampedStartT->format('d M') . ' - ' . $clampedEndT->format('d M');
                $iTemp++; $cursorTemp->addWeek();
            }
        }
        $filterSelections = $this->extractFilterSelections($request);


       return view('owner.analysis.finance', array_merge([
            'mode' => $mode,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'incomeChartLabels' => $incomeChartLabels,
            'incomeChartData' => $incomeChartData,
            'expenseChartLabels' => $expenseChartLabels,
            'expenseChartData' => $expenseChartData,
            'roles' => $roles,
            'roleSelected' => $role,
            'usersByRole' => $usersByRole,
            'userSelected' => $userSelected,
            'presetCategoriesIncome' => $presetCategoriesIncome,
            'presetCategoriesExpense' => $presetCategoriesExpense,
            'incomeType' => $incomeType,
            'expenseType' => $expenseType,
            'dayDate' => $dayDate,
            'weekYear' => $weekYear,
            'weekMonth' => $weekMonth,
            'weekIndex' => $weekIndex,
            'monthYear' => $monthYear,
            'monthMonth' => $monthMonth,
            'yearYear' => $yearYear,
            'yearsList' => $yearsList,
            'monthsList' => $monthsList,
            'weeksList' => $weeksList,
            'dynActive' => $dynActive,
            'dynIncomeCat' => $dynIncomeCat,
            'dynExpenseCat' => $dynExpenseCat,
            'dynIncomeLabels' => $dynIncomeLabels,
            'dynIncomeData' => $dynIncomeData,
            'dynExpenseLabels' => $dynExpenseLabels,
            'dynExpenseData' => $dynExpenseData,
            'dynIncomeName' => $dynIncomeName,
            'dynExpenseName' => $dynExpenseName,
            'monitorTransactions' => $monitorTransactions,
            'monitorIncomeCategories' => $monitorIncomeCategories,
            'monitorExpenseCategories' => $monitorExpenseCategories,
            'summaryIncome' => $summaryIncome,
            'summaryExpense' => $summaryExpense,

        ], $filterSelections));

    }

    public function financeExport(Request $request)
    {
        $request->validate([
            'dataset' => 'required|in:income,expense,both',
            'range' => 'required|in:daily,weekly,monthly,yearly',
            'format' => 'required|in:csv,pdf,xlsx',
            'date' => 'nullable|date',
            'week_year' => 'nullable|integer',
            'week_month' => 'nullable|integer',
            'week_index' => 'nullable|integer',
            'month_year' => 'nullable|integer',
            'month_month' => 'nullable|integer',
            'year_year' => 'nullable|integer',
            'income_categories' => 'nullable|array',
            'income_categories.*' => 'integer',
            'expense_categories' => 'nullable|array',
            'expense_categories.*' => 'integer',
            'role' => 'nullable|string',
        ]);

        $range = $request->input('range');
        $now = Carbon::now();
        switch($range){
            case 'daily':
                $target = $request->input('date') ? Carbon::parse($request->input('date')) : $now;
                $startDate = $target->copy()->startOfDay();
                $endDate = $target->copy()->endOfDay();
                break;
            case 'weekly':
                $weekYear = (int)($request->input('week_year') ?: $now->year);
                $weekMonth = (int)($request->input('week_month') ?: $now->month);
                $weekIndex = (int)($request->input('week_index') ?: 1);
                $monthStart = Carbon::create($weekYear,$weekMonth,1)->startOfDay();
                $monthEnd = $monthStart->copy()->endOfMonth();
                $firstWeekStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
                $weeks=[]; $cursor=$firstWeekStart->copy(); $i=1;
                while($cursor->lte($monthEnd)){
                    $wStart=$cursor->copy(); $wEnd=$cursor->copy()->endOfWeek(Carbon::MONDAY);
                    if($wEnd->lt($monthStart)){ $cursor->addWeek(); continue; }
                    $clampedStart=$wStart->lt($monthStart)? $monthStart->copy(): $wStart;
                    $clampedEnd=$wEnd->gt($monthEnd)? $monthEnd->copy(): $wEnd;
                    $weeks[$i]=['start'=>$clampedStart,'end'=>$clampedEnd]; $i++; $cursor->addWeek();
                }
                if(!isset($weeks[$weekIndex])){ $weekIndex = array_key_first($weeks); }
                $startDate = $weeks[$weekIndex]['start'];
                $endDate = $weeks[$weekIndex]['end'];
                break;
            case 'yearly':
                $yearYear = (int)($request->input('year_year') ?: $now->year);
                $startDate = Carbon::create($yearYear,1,1)->startOfDay();
                $endDate = $startDate->copy()->endOfYear();
                break;
            default: // monthly
                $monthYear = (int)($request->input('month_year') ?: $now->year);
                $monthMonth = (int)($request->input('month_month') ?: $now->month);
                $startDate = Carbon::create($monthYear,$monthMonth,1)->startOfDay();
                $endDate = $startDate->copy()->endOfMonth();
        }

        $dataset = $request->input('dataset');
        $incomeCats = $request->input('income_categories', []);
        $expenseCats = $request->input('expense_categories', []);
        $format = $request->input('format','csv');

        $rows = collect();
        // Support multi role (array) dan user (array)
        $roles = $request->input('roles', []); // array dari checkbox
        $roleSingle = $request->input('role'); // dropdown utama (legacy)
        $userIds = $request->input('users', []);
        if($dataset==='income' || $dataset==='both'){
            $q = Income::query()->whereBetween('date',[$startDate->format('Y-m-d'),$endDate->format('Y-m-d')])->with(['category','user']);
            if($incomeCats && count($incomeCats)>0){ $q->whereIn('pemasukkan_id',$incomeCats); }
            if($userIds && count($userIds)>0){
                $q->whereIn('user_id', $userIds);
            } else if($roles && count($roles)>0){
                $q->whereHas('user', function($qu) use ($roles) {
                    $qu->whereIn('role', $roles);
                });
            } else if($roleSingle){
                $q->whereHas('user',fn($qu)=>$qu->where('role',$roleSingle));
            }
            $rows = $rows->merge($q->get()->map(function($r){
                return [
                    'type' => 'Pemasukkan',
                    'date' => $r->date ? Carbon::parse($r->date)->format('Y-m-d') : '',
                    'category' => $r->pemasukkan_id ? ($r->category? $r->category->nama_pemasukkan : 'Kategori Dihapus') : 'Lainnya',
                    'amount' => $r->amount,
                    'user' => optional($r->user)->name,
                    'role' => optional($r->user)->role,
                    'payment_type' => $r->payment_type ?? '-',
                    'income_type' => $r->category? $r->category->type : '-',
                    'expense_type' => '-', // pemasukkan tidak punya expense_type
                ];
            }));
        }
        if($dataset==='expense' || $dataset==='both'){
            $q = Expend::query()->whereBetween('date',[$startDate->format('Y-m-d'),$endDate->format('Y-m-d')])->with(['category','user']);
            if($expenseCats && count($expenseCats)>0){ $q->whereIn('category_id',$expenseCats); }
            if($userIds && count($userIds)>0){
                $q->whereIn('user_id', $userIds);
            } else if($roles && count($roles)>0){
                $q->whereHas('user', function($qu) use ($roles) {
                    $qu->whereIn('role', $roles);
                });
            } else if($roleSingle){
                $q->whereHas('user',fn($qu)=>$qu->where('role',$roleSingle));
            }
            $rows = $rows->merge($q->get()->map(function($r){
                return [
                    'type' => 'Pengeluaran',
                    'date' => $r->date ? Carbon::parse($r->date)->format('Y-m-d') : '',
                    'category' => $r->category_id ? ($r->category? $r->category->name : 'Kategori Dihapus') : 'Lainnya',
                    'amount' => $r->amount,
                    'user' => optional($r->user)->name,
                    'role' => optional($r->user)->role,
                    'payment_type' => $r->payment_type ?? '-',
                    'expense_type' => $r->category? $r->category->type : '-',
                    'income_type' => '-', // pengeluaran tidak punya income_type
                ];
            }));
        }

        // Sort descending by date
        $rows = $rows->sortByDesc('date')->values();

        if($format==='pdf'){
            $pdf = \PDF::loadView('owner.analysis.finance-export-pdf', [
                'rows' => $rows,
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
                'dataset' => $dataset,
                'generatedAt' => Carbon::now()->format('d/m/Y H:i:s')
            ])->setPaper('a4','landscape');
            return $pdf->download('analysis-finance-' . $dataset . '-' . Carbon::now()->format('Ymd_His') . '.pdf');
        }
        if($format==='xlsx'){
            return Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $rows; public function __construct($r){ $this->rows=$r; }
                public function collection(){
                    return collect($this->rows)->map(function($r){
                        return [
                            $r['type'],
                            $r['date'],
                            $r['category'],
                            $r['amount'],
                            $r['user'],
                            $r['role'],
                            $r['payment_type'],
                            $r['expense_type'],
                            $r['income_type'],
                        ];
                    });
                }
                public function headings(): array {
                    return ['Jenis','Tanggal','Kategori','Jumlah','User','Role','Tipe Pembayaran','Jenis Pengeluaran','Jenis Pemasukkan'];
                }
            }, 'analysis-finance-' . $dataset . '-' . Carbon::now()->format('Ymd_His') . '.xlsx');
        }

        // default CSV
        $filename = 'analysis-finance-' . $dataset . '-' . Carbon::now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $callback = function() use ($rows){
            $out = fopen('php://output','w');
            fputcsv($out,['Jenis','Tanggal','Kategori','Jumlah','User','Role','Tipe Pembayaran','Jenis Pengeluaran','Jenis Pemasukkan']);
            foreach($rows as $r){
                fputcsv($out,[
                    $r['type'],
                    $r['date'],
                    $r['category'],
                    $r['amount'],
                    $r['user'],
                    $r['role'],
                    $r['payment_type'],
                    $r['expense_type'],
                    $r['income_type'],
                ]);
            }
            fclose($out);
        };
        return response()->stream($callback,200,$headers);
    }

    private function extractFilterSelections(Request $request)
    {
        return [
            'filterRoles'         => $request->input('roles', []),
            'filterUsers'         => $request->input('users', []),
            'filterIncomeCats'    => $request->input('income_categories', []),
            'filterExpenseCats'   => $request->input('expense_categories', []),
        ];
    }

}
