<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController as DashboardAdminController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\Owner\UserController;
use App\Http\Controllers\Owner\ModalController;
use App\Http\Controllers\Owner\PemasukkanController;
use App\Http\Controllers\Kasir\IncomeController;
use App\Http\Controllers\Kasir\ExpendController;
use App\Http\Controllers\Kasir\ExpenseCategoryController as KasirExpenseCategoryController;
use App\Http\Controllers\Owner\KasirExpendController;
use App\Http\Controllers\Owner\KasirIncomeController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\ExpenseCategoryController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\PermissionController;
use App\Http\Controllers\Owner\EmployeeSalaryController;
use App\Http\Controllers\Auth\PasswordVerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    // Redirect to role-specific dashboard
    if (auth()->check()) {
        switch (auth()->user()->role) {
            case 'owner':
                return redirect()->route('owner.dashboard');
            case 'kasir':
                return redirect()->route('kasir.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                // Fallback generic dashboard (Jetstream style uses Vite)
                return view('dashboard');
        }
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Role-specific dashboards
//owner
Route::middleware(['auth', 'role:owner|admin'])->prefix('owner')->name('owner.')->group(function () {
    // Use comprehensive DashboardController that supplies all required variables
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Saldo Management (owner & admin with shared prefix)
    Route::get('saldo', [\App\Http\Controllers\Owner\SaldoManagementController::class, 'index'])->name('saldo.index');
    Route::post('saldo/transfer', [\App\Http\Controllers\Owner\SaldoManagementController::class, 'transfer'])->name('saldo.transfer');
    Route::post('saldo/topup', [\App\Http\Controllers\Owner\SaldoManagementController::class, 'topupStore'])->name('saldo.topup.store');
    Route::put('saldo/topup/{topup}', [\App\Http\Controllers\Owner\SaldoManagementController::class, 'topupUpdate'])->name('saldo.topup.update');
    Route::delete('saldo/topup/{topup}', [\App\Http\Controllers\Owner\SaldoManagementController::class, 'topupDestroy'])->name('saldo.topup.destroy');
    Route::get('saldo/topup/export', [\App\Http\Controllers\Owner\SaldoManagementController::class, 'topupExport'])->name('saldo.topup.export');
    Route::get('saldo/export/custom', [\App\Http\Controllers\Owner\SaldoManagementController::class, 'exportCustom'])->name('saldo.export.custom');

    // Read-only access to invoices for owner/admin (admin must have saldo.export)
    Route::get('invoice/{invoice}', [\App\Http\Controllers\Owner\InvoiceAccessController::class, 'show'])->name('invoice.show');
    Route::get('invoice/{invoice}/print', [\App\Http\Controllers\Owner\InvoiceAccessController::class, 'print'])->name('invoice.print');

    // User management routes
    Route::resource('users', UserController::class);

    // pemasukkan
    Route::resource('pemasukkan', PemasukkanController::class);

    //modal (singular instead of plural)
    Route::get('modals', [ModalController::class, 'index'])->name('modal.index');
    Route::get('modals/create', [ModalController::class, 'create'])->name('modal.create');
    Route::post('modals/store', [ModalController::class, 'store'])->name('modal.store');
    Route::get('modals/{modal}/edit', [ModalController::class, 'edit'])->name('modal.edit');
    Route::put('modals/{modal}', [ModalController::class, 'update'])->name('modal.update');
    Route::delete('modals/{modal}', [ModalController::class, 'destroy'])->name('modal.destroy');

    // Kasir Expend routes
    Route::get('kasir-expend', [KasirExpendController::class, 'index'])->name('kasir-expend.index');
    Route::get('kasir-expend/{id}', [KasirExpendController::class, 'show'])->name('kasir-expend.show');
    Route::get('kasir-expend/report/download', [KasirExpendController::class, 'downloadReport'])->name('kasir-expend.report');

    // Kasir Income routes
    Route::get('kasir-income', [KasirIncomeController::class, 'index'])->name('kasir-income.index');
    Route::get('kasir-income/{id}', [KasirIncomeController::class, 'show'])->name('kasir-income.show');
    Route::get('kasir-income/report/download', [KasirIncomeController::class, 'downloadReport'])->name('kasir-income.report');

    // Expense Categories routes
    Route::resource('expense-categories', ExpenseCategoryController::class);

    Route::get('financial-report', [ReportController::class, 'generateFinancialReport'])->name('financial.report');
    Route::get('financial-report/custom', [ReportController::class, 'generateCustomReport'])->name('financial.custom.report');
    Route::post('permissions/update', [PermissionController::class, 'update'])->name('permissions.update');
    Route::get('permissions', [PermissionController::class, 'show'])->name('permissions.show');

    // Employee Salary routes
    Route::prefix('employee-salary')->name('employee-salary.')->group(function () {
        Route::get('/', [EmployeeSalaryController::class, 'index'])->name('index');

        // Employee CRUD
        Route::get('employees/create', [EmployeeSalaryController::class, 'createEmployee'])->name('createEmployee');
        Route::post('employees', [EmployeeSalaryController::class, 'storeEmployee'])->name('storeEmployee');
        Route::get('employees/{employee}/edit', [EmployeeSalaryController::class, 'editEmployee'])->name('editEmployee');
        Route::put('employees/{employee}', [EmployeeSalaryController::class, 'updateEmployee'])->name('updateEmployee');
        Route::delete('employees/{employee}', [EmployeeSalaryController::class, 'destroyEmployee'])->name('destroyEmployee');

        // Payments
        Route::get('payments/create', [EmployeeSalaryController::class, 'createPayment'])->name('createPayment');
        Route::post('payments', [EmployeeSalaryController::class, 'storePayment'])->name('storePayment');

        // Salary invoice
        Route::get('invoice/{payment}', [EmployeeSalaryController::class, 'invoice'])->name('invoice');
        Route::get('invoice/{payment}/print', [EmployeeSalaryController::class, 'invoicePrint'])->name('invoice.print');
    });

    // Recurring Expenses management (owner/admin)
    Route::resource('recurring-expenses', \App\Http\Controllers\Owner\RecurringExpenseController::class)->except(['show']);

    // Analysis pages
    Route::get('analysis/finance', [\App\Http\Controllers\Owner\AnalysisController::class,'finance'])->name('analysis.finance');
    Route::get('analysis/finance/export', [\App\Http\Controllers\Owner\AnalysisController::class,'financeExport'])->name('analysis.finance.export');
});
// admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Reuse owner dashboard logic with admin layout, but view hides owner-only cards
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Income approvals
    Route::get('income-approvals', [\App\Http\Controllers\Admin\IncomeApprovalController::class, 'index'])->name('income-approvals.index');
    Route::get('income-approvals/{report}', [\App\Http\Controllers\Admin\IncomeApprovalController::class, 'show'])->name('income-approvals.show');
    Route::post('income-approvals/{report}/approve', [\App\Http\Controllers\Admin\IncomeApprovalController::class, 'approve'])->name('income-approvals.approve');
    Route::post('income-approvals/{report}/reject', [\App\Http\Controllers\Admin\IncomeApprovalController::class, 'reject'])->name('income-approvals.reject');
    // Notifications
    Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/{id}/toggle', [\App\Http\Controllers\Admin\NotificationController::class, 'toggle'])->name('notifications.toggle');
});


// kasir
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');

    // kasir routes
    // Income routes
    Route::resource('income', IncomeController::class);

    // Session report routes (kasir submit verifikasi)
    Route::get('session-report', [\App\Http\Controllers\Kasir\SessionReportController::class, 'index'])->name('session-report.index');
    Route::post('session-report/submit', [\App\Http\Controllers\Kasir\SessionReportController::class, 'submit'])->name('session-report.submit');
    Route::post('session-report/resubmit', [\App\Http\Controllers\Kasir\SessionReportController::class, 'resubmit'])->name('session-report.resubmit');

    // Invoice routes
    Route::get('invoice/income/create', [\App\Http\Controllers\Kasir\InvoiceController::class, 'createIncome'])->name('invoice.createIncome');
    Route::post('invoice/income', [\App\Http\Controllers\Kasir\InvoiceController::class, 'storeIncome'])->name('invoice.storeIncome');
    Route::get('invoice/{invoice}', [\App\Http\Controllers\Kasir\InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('invoice/{invoice}/print', [\App\Http\Controllers\Kasir\InvoiceController::class, 'print'])->name('invoice.print');
    Route::post('invoice/{invoice}/email', [\App\Http\Controllers\Kasir\InvoiceController::class, 'email'])->name('invoice.email');
    Route::get('invoice/from-income/{income}', [\App\Http\Controllers\Kasir\InvoiceController::class, 'fromIncome'])->name('invoice.fromIncome');
    Route::get('invoice/from-expend/{expend}', [\App\Http\Controllers\Kasir\InvoiceController::class, 'fromExpend'])->name('invoice.fromExpend');
    // New edit/update/destroy routes for invoice
    Route::get('invoice/{invoice}/edit', [\App\Http\Controllers\Kasir\InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::put('invoice/{invoice}', [\App\Http\Controllers\Kasir\InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('invoice/{invoice}', [\App\Http\Controllers\Kasir\InvoiceController::class, 'destroy'])->name('invoice.destroy');

    // Expend routes
    Route::resource('expend', ExpendController::class);

    // Expense Categories routes
    Route::get('expense-categories/create', [KasirExpenseCategoryController::class, 'create'])->name('expense-categories.create');
    Route::post('expense-categories', [KasirExpenseCategoryController::class, 'store'])->name('expense-categories.store');
    Route::get('expense-categories/by-type', [KasirExpenseCategoryController::class, 'getByType'])->name('expense-categories.by-type');

    // Recurring expense payments
    Route::get('recurring-expenses', [\App\Http\Controllers\Kasir\RecurringExpensePaymentController::class, 'index'])->name('recurring-expenses.index');
    Route::get('recurring-expenses/{recurring_expense}/pay', [\App\Http\Controllers\Kasir\RecurringExpensePaymentController::class, 'pay'])->name('recurring-expenses.pay');
    Route::post('recurring-expenses/{recurring_expense}/pay', [\App\Http\Controllers\Kasir\RecurringExpensePaymentController::class, 'store'])->name('recurring-expenses.store');
});

// Password reset routes
// Nonaktifkan route default Laravel untuk password reset
// Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
// Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

// Aktifkan hanya sistem kode verifikasi
Route::get('/forgot-password', [PasswordVerificationController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordVerificationController::class, 'sendVerificationCode'])->name('password.email');
Route::get('/verify-code', [PasswordVerificationController::class, 'showVerifyCodeForm'])->name('password.verify.code.form');
Route::post('/verify-code', [PasswordVerificationController::class, 'verifyCode'])->name('password.verify.code');
Route::get('/reset-password', [PasswordVerificationController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordVerificationController::class, 'resetPassword'])->name('password.reset.custom');

require __DIR__ . '/auth.php';
