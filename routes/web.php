<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ApprovalTimelineController;
use App\Http\Controllers\BudgetReportController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FinancialYearController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\HeadItemController;
use App\Http\Controllers\ExpenseTransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeHeadController;
use App\Http\Controllers\InventoryItemIssueController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\LeavePlanController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LedgerReportController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\LotItemController;
use App\Http\Controllers\MoreReportController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchasePlanController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clear-cache', function () {
    Artisan::call('view:clear');
});

//authentication

Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('do-login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'set-session'])->group(function () {

    Route::resource('/account-types', AccountTypeController::class);

    Route::resource('/banks', BankController::class);

    Route::resource('/branches', BranchController::class);

    Route::get("/get-branch-data", [BranchController::class, 'getBranchData']);

    Route::resource('/designations', DesignationController::class);

    Route::resource('/employees', EmployeeController::class);

    //expense

    Route::get('/{type}-heads', [HeadController::class, 'index'])
        ->where('type', 'expense|income')
        ->name('heads.index');



    //get json head items

    Route::get('/get-head-items', [HeadItemController::class, 'getHeadItems']);

    Route::resource('/heads', HeadController::class)
        ->except('index');

       Route::get('/arrange-expense-head/{type}', [HeadController::class, 'arrange_head'])->name('arrange-expense-head');
    Route::post('/update-order', [HeadController::class, 'updateOrder'])->name('save-order');

    Route::get('/arrange-expense-head-item/{type}', [HeadItemController::class, 'arrange_head'])->name('arrange-expense-head-item');
    Route::post('/save-order-head-item', [HeadItemController::class, 'updateOrder'])->name('save-order-head-item');


    Route::get('/{type}-items', [HeadItemController::class, 'index'])
        ->where('type', 'expense|income')
        ->name('head-items.index');

    /*Route::get('{type}-items', [HeadItemController::class, 'index'])
        ->where('type', 'purchase')
        ->name('purchase-items.index');*/

    Route::resource('/head-items', HeadItemController::class)
        ->except('index');

    Route::resource('/expenses', ExpenseTransactionController::class);

    Route::get('/get-expense-table', [ExpenseTransactionController::class, 'getExpenseTable']);

    Route::resource('/payrolls', PayrollController::class);

    Route::get('/get-item-budget', [BudgetController::class, 'getItemBudget']);

    Route::resource('/budgets', BudgetController::class);

    Route::get('/get-heads-with-status', [BudgetController::class, 'getHeadsWithStatus'])->name('get.heads');
    Route::post('/update-status', [BudgetController::class, 'updateStatus'])->name('update.status');
    Route::delete('/budget-items/destroy', [BudgetController::class, 'destroyBudgetItems'])->name('budget-items.destroy');


    Route::post('/post-petty-cash-account', [AccountController::class, 'postPettyCash']);

    Route::get('/create-petty-cash-account', [AccountController::class, 'createPettyCashAccount']);

    Route::get('/get-petty-cash', [AccountController::class, 'getPettyCash']);

    Route::get('/check-transection', [AccountTransactionController::class, 'checkTransection']);

    //renew account

    Route::get('/accounts/{id}/renew', [AccountController::class, 'getAccountRenew'])
        ->name('accounts.get-renew');

    Route::post('/accounts/{id}/renew', [AccountController::class, 'postAccountRenew'])
        ->name('accounts.post-renew');

    //charge

    Route::get('add-service-charge', [AccountTransactionController::class, 'addServiceCharge'])
        ->name('accounts.add-service-charge');

    Route::post('/post-service-charge', [AccountTransactionController::class, 'postServiceCharge'])
        ->name('accounts.post-service-charge');

    /*Route::get('/accounts/{id}/charge', [AccountController::class, 'getAccountCharge'])
        ->name("accounts.add-charge");*/

    /*Route::post('/accounts/{id}/charge', [AccountController::class, 'postAccountCharge'])
        ->name('accounts.post-charge');*/


    //update opening balance

    Route::get('/accounts/{id}/edit-opening-balance', [AccountController::class, 'editOpeningBalance']);

    Route::post('/accounts/{id}/edit-opening-balance', [AccountController::class, 'updateOpeningBalance'])
        ->name('accounts.update-opening-balance');

    Route::get('/accounts/{id}/account-book', [AccountController::class, 'accountBook'])
        ->name('accounts.account-book');

    Route::get("/accounts/{id}/interests", [AccountController::class, 'viewInterest']);

    Route::get('/get-accounts-data', [AccountController::class, 'getAccountsData']);

    Route::get('/get-account-info', [AccountController::class, 'getAccountInfo']);

    Route::resource('accounts', AccountController::class);

    Route::get('/account-deposits/create-modal', [AccountTransactionController::class, 'createDeposit'])->name('deposits.create-modal');
    Route::get('/account-deposits/create', [AccountTransactionController::class, 'createDeposit'])
        ->name('deposits.create');
    Route::post('/account-deposits', [AccountTransactionController::class, 'storeDeposits'])->name('deposits.store');

    Route::get('/account-withdraws/create', [AccountTransactionController::class, 'createWithdrawModal'])->name('withdraws.create-modal');
    Route::get('/account-withdraws/create', [AccountTransactionController::class, 'createWithdraw'])->name('withdraws.create');
    Route::post('/account-withdraws', [AccountTransactionController::class, 'storeWithdraw'])->name('withdraws.store');

    Route::get('/account-transfers/create', [AccountTransactionController::class, 'createTransfer'])->name('transfers.create');
    Route::post('/account-transfers', [AccountTransactionController::class, 'storeTransfer'])->name('transfers.store');

    Route::resource('/reconciliations', ReconciliationController::class);

    Route::get('/cheques/{id}/deposit', [ChequeController::class, 'chequeDeposit'])
        ->name('cheques.create-deposit');

    Route::post('/cheques/{id}/deposit', [ChequeController::class, 'storeChequeDeposit'])
        ->name('cheques.post-deposit');

    Route::get('/cheques/{id}/complete-transaction', [ChequeController::class, 'chequeTransaction'])
        ->name('cheques.create-transaction');

    Route::post('/cheques/{id}/complete-transaction', [ChequeController::class, 'storeTransaction'])
        ->name('cheques.post-transaction');

    Route::post('/cheques/{id}/attach-file', [ChequeController::class, 'attachFile'])
        ->name('cheques.attach-file');

    Route::resource('/cheques', ChequeController::class)
        ->except(['index']);

    Route::get('/{type}-cheques', [ChequeController::class, 'index'])
        ->name('cheques.index')
        ->where('type', 'issued|received|transaction');

    //income

    //Route::resource('income-heads', IncomeHeadController::class);
    Route::resource('/incomes', IncomeController::class);

    //lot

    Route::get('/lots/search', [LotController::class, 'getSearch'])->name('lots-get-search');
    Route::post('/lots/search', [LotController::class, 'postSearch'])->name('lots-post-search');
    Route::get('/lots/search-results', [LotController::class, 'getSearchResult'])->name("lots-search-results");

    //lot items

    //Send BEFTN

    Route::post('/lots/{id}/send-beftn', [LotController::class, 'sendBeftn']);

    Route::get('/lots/{id}/confirmation', [LotController::class, 'confirmation']);

    Route::get('/lots/search/{id}/get-lot-item', [LotController::class, 'getLotItem'])
    ->name('lot-search.get-lot-item');

    Route::post('/hold-lot-item', [LotController::class, 'hold']);

    Route::post('/lots/{id}/resend', [LotController::class, 'resend']);

    Route::post('/lots/{id}/return-item', [LotController::class, 'return']);

    Route::post('/return-index', [LotController::class, 'returnIndex']);

    Route::post('/stop-index', [LotController::class, 'stopIndex']);

    Route::post('lots/search/send-index', [LotController::class, 'sendAmountIndex']);

    Route::get('/lot-items/{id}/transactions/{transaction_id}/edit', [LotItemController::class, 'transactionEdit'])
    ->name('lot-item-transactions.edit');

    Route::put('/lot-items/{id}/transactions/{transaction_id}/update', [LotItemController::class, 'transactionUpdate'])
        ->name('lot-item-transactions.update');

    Route::resource('/lot-items', LotItemController::class);

    Route::get('/old-lot-sent-tran', [LotItemController::class, 'oldLotSentTran']);
    Route::post('/old-lot-fsibl-transection-post', [LotItemController::class, 'oldFsiblTranPost']);


    Route::post('/old-lot-transection-post', [LotItemController::class, 'oldTranPost']);
    Route::get('/date-lot-transection-get', [LotItemController::class, 'dateTranGet']);
    Route::post('/date-lot-transection-post', [LotItemController::class, 'dateTranPost']);

    Route::get('/lots/{id}/pay', [LotController::class, 'getPay']);

    Route::post('/lots/{id}/pay', [LotController::class, 'pay']);

    Route::get("/lots/{id}/add-lot-item", [LotController::class, 'addLotItem'])
        ->name('add-lot-item.create');

    Route::post("/lots/{id}/add-lot-item", [LotController::class, 'postLotItem'])
        ->name('add-lot-item.post');

    Route::get('/lots-export', [LotController::class, 'export']);

    Route::resource('/lots', LotController::class);

    //settings

    Route::resource('/financial-years', FinancialYearController::class);

    //inventory items

    Route::resource('/items', ItemController::class);

    Route::post('/add-request-item-row', [ItemRequestController::class, 'addRequestItemRow']);

    Route::post('/add-issue-item-row', [InventoryItemIssueController::class, 'addIssueItemRow']);

    Route::resource('/issue-inventory-items', InventoryItemIssueController::class);

    Route::get('/items-requests/{id}/issue-item', [ItemRequestController::class, 'issueItem'])->name('item-requests.issue-item');

    Route::resource('/item-requests', ItemRequestController::class);

    Route::resource('/purchases', PurchaseController::class);

    Route::resource('/purchase-plans', PurchasePlanController::class);

    Route::resource('/contacts', ContactController::class);

    //user management

    Route::resource('/roles', RoleController::class);

    Route::resource('/users', UserController::class);

    Route::get('/approval-requests', [ApprovalTimelineController::class, 'index'])->name('approvals.index');

    Route::get('/approval/{id}/detail', [ApprovalTimelineController::class, 'getDetail'])
        ->name('approvals.detail');

    Route::resource('/approvals', ApprovalTimelineController::class)
        ->except(['index']);

    Route::resource('/settings', SettingController::class)
        ->only(['index', 'store']);

    //Reports

    Route::get('/bank-report', [ReportController::class, 'bankReport'])
        ->name('bank-report');

    Route::get('/employee-payment-report', [ReportController::class, 'employeePaymentReport'])
        ->name('employee-payment-report');

    Route::get("/income-report-export", [ReportController::class, 'incomeReportExport'])
        ->name("income-report-export");

    Route::get("/income-report", [ReportController::class, 'incomeReport'])
        ->name("income-report");

    Route::get("/income-sub-details/{type}/{acc_no}/{daterange}", [ReportController::class, 'incomeSubDetails'])
        ->name("income-sub-details");

    Route::get('/expense-report', [ReportController::class, 'expenseReport'])
        ->name("expense-report");

    Route::get('/fdr-report', [ReportController::class, 'fdrReport'])
        ->name("fdr-report");

    Route::get('/fdr-account-report-test', [MoreReportController::class, 'fdrReport'])
        ->name('fdr-account-report-test');

    //Bank Report

    Route::get('/cashbook-report', [ReportController::class, 'bankCashbookReport'])
        ->name("bank-cashbook-report");

    Route::get('/cashbook-report-export', [ReportController::class, 'bankCashBookReportExportPDF']);

    Route::get('/hold-items-report', [ReportController::class, 'holdItemsReport'])
        ->name('hold-items-report');

    Route::get('/bank-hold-report-export', [ReportController::class, 'bankHoldReportExport'])
        ->name('bank-hold-report-export');

    Route::get('/pending-items-report', [ReportController::class, 'pendingItemsReport'])
        ->name('pending-items-report');

    Route::get('/pending-items-report-export', [ReportController::class, 'exportPendingItemsReport'])
        ->name('pending-items-report-export');


    Route::get('/payment-items-report', [ReportController::class, 'paymentItemsReport'])
        ->name('payment-items-report');

    Route::get('/payment-items-report-export', [ReportController::class, 'exportPaymentItemsReport'])
        ->name('payment-items-report-export');


    Route::get('/stop-items-report', [ReportController::class, 'stopItemsReport'])
        ->name('stop-items-report');

    Route::get('/stop-items-report-export', [ReportController::class, 'exportStopItemsReport'])
        ->name('stop-items-report-export');


    Route::get('/returned-items-report', [ReportController::class, 'returnedItemsReport'])
        ->name('returned-items-report');

    Route::get('/returned-items-report-export', [ReportController::class, 'exportReturnedItemsReport'])
        ->name('returned-items-report-export');

    Route::get('/bank-wise-report', [ReportController::class, 'bankWiseReport'])
        ->name('bank-wise-report');

    Route::get('/bank-wise-report-export', [ReportController::class, 'exportBankWiseReport'])
        ->name('bank-wise-report-export');

    Route::get('/lot-wise-report', [ReportController::class, 'lotWiseReport'])
        ->name('lot-wise-report');

    Route::get('/lot-wise-report-export', [ReportController::class, 'exportLotWiseReport'])
        ->name('lot-wise-report-export');

    Route::get('/reconciliation-report', [ReportController::class, 'reconciliationReport'])
        ->name('reconciliation-report');

    Route::get('/reconciliation-report-export', [ReportController::class, 'exportReconciliationReport']);

    Route::get('/ledger-report', [LedgerReportController::class, 'getLedgerReport'])
        ->name('ledger-report.get-ledger-report');

    Route::get('/ledger-report-export', [LedgerReportController::class, 'exportLedgerReport']);

    Route::get('/budget-report', [BudgetReportController::class, 'budgetReport'])
        ->name('budget-report');

    Route::get('/budget-report-export', [BudgetReportController::class, 'budgetReportExport'])
        ->name('budget-report-export');

    Route::get('/budget-ghatti-report', [MoreReportController::class, 'ghatti']);

    Route::get('/income-expense-summary', [MoreReportController::class, 'incomeExpenseSummary']);

    Route::get('/fdr-account-report', [MoreReportController::class, 'fdrReport'])
        ->name('fdr-account-report');

    Route::get('/std-account-report', [MoreReportController::class, 'stdReport'])
        ->name('std-account-report');

     Route::get('/std-account-test-report/{id}', [MoreReportController::class, 'stdReportTest'])
        ->name('std-account-test-report');

    Route::get('/lot-report', [MoreReportController::class, 'lotReport'])
        ->name('date-wise-lot-report');

    Route::get('/', [HomeController::class, 'index'])->name('home.dashboard');

    Route::get('/leave-plans/add-more', [LeavePlanController::class, 'addMore'])
        ->name('leave-plans.add-more');
    Route::resource('/leave-plans', LeavePlanController::class);

    Route::put('/leave-requests/{id}/update-status', [LeaveRequestController::class, 'updateStatus'])
        ->name('leave-requests.update-status');
    Route::resource('/leave-requests', LeaveRequestController::class);


});

Route::get('/api-for-terbb/{index_no}', [LotItemController::class, 'apiForTerbb']);

Route::get('/export', function(){

});
