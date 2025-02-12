<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        {{config('app.name')}}
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul id="sidebar-menu" class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">

                <li class="nav-item">
                    <a href="{{route('home.dashboard')}}"
                       class="nav-link {{ request()->is('/') ? 'active': ''}}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <!--- Accounts -->
                @can('bank.view')
                    <li class="nav-item has-treeview {{(request()->segment('1') == 'account-types' || request()->segment('1') == 'banks' ||
                        request()->segment('1') == 'branches' || request()->segment(1) == 'accounts' || request()->segment(1) == 'reconciliations'
                        || request()->segment(1) == 'cheques'                        )  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class='nav-icon fa fa-building'></i>
                            <p>
                                Banks
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{route('accounts.index')}}"
                                   class="nav-link {{request()->segment('1') == 'accounts' ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bank Accounts</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('deposits.create')}}"
                                   class="nav-link {{request()->route( 'deposits.create') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Deposit Money</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('withdraws.create')}}"
                                   class="nav-link {{request()->is('/withdraws/create') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Withdraw</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('cheques.index', ['type' => 'issued'])}}"
                                   class="nav-link {{request()->is('/cheques') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Issued Cheques</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('cheques.index', ['type' => 'received'])}}"
                                   class="nav-link {{request()->is('/cheques') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Received Cheques</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('cheques.index', ['type' => 'transaction'])}}"
                                   class="nav-link {{request()->is('/cheques') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Party/Tranx. Cheques</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('banks.index')}}"
                                   class="nav-link {{request()->segment('1') == 'banks' ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Banks</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('branches.index')}}"
                                   class="nav-link {{request()->segment('1') == 'branches' ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Branches</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/get-petty-cash"
                                   class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Petty Cash</p>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a href="{{route('reconciliations.index')}}"
                                   class="nav-link {{request()->segment('1') == 'reconciliations' ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Reconciliations</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan

                <!-- Employee -->

                @if(auth()->user()->can('employee.view') || auth()->user()->can('designation.view') )

                    <li class="nav-item has-treeview {{(request()->segment('1') == 'designations' || request()->segment('1') == 'employees' )  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>
                                Employee Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            @if(auth()->user()->can('employee.view'))
                                <li class="nav-item">
                                    <a href="{{route('employees.index')}}"
                                       class="nav-link {{request()->segment('1') == 'employees' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Employee List</p>
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->can('designation.create'))

                                <li class="nav-item">
                                    <a href="{{route('designations.index')}}"
                                       class="nav-link {{request()->segment('1') == 'designations' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Designation</p>
                                    </a>
                                </li>

                            @endif

                        </ul>
                    </li>

                @endif

                <li class="nav-item has-treeview {{(request()->segment('1') == 'designations' || request()->segment('1') == 'employees' )  ? 'menu-open': ''}}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>
                            Leave Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('leave-plans.index')}}"
                               class="nav-link {{request()->segment('1') == 'leave-plans' ? 'active': ''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Leave Plan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('leave-requests.index')}}"
                               class="nav-link {{request()->segment('1') == 'leave-requests' ? 'active': ''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Leave Requests</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--Contacts -->

                @if (auth()->user()->can('contact.view'))

                    <li class="nav-item has-treeview {{(request()->segment('1') == 'contacts')  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>
                                Contacts
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{route('contacts.index', ['type' => 'supplier'])}}"
                                   class="nav-link {{(request()->segment('1') == 'contacts' && request()->query('type') == 'supplier') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Suppliers</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('contacts.index',['type' => 'service-provider'])}}"
                                   class="nav-link {{(request()->segment('1') == 'contacts' && request()->query('type') == 'service-provider') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Service Providers</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                @endif


{{--                <li class="nav-item">--}}
{{--                    <a href="{{route('approvals.index')}}"--}}
{{--                       class="nav-link {{(request()->segment('1') == 'approval-requests') ? 'active': ''}}">--}}
{{--                        <i class="far fa-bell nav-icon"></i>--}}
{{--                        <p>Approval Requests</p>--}}
{{--                    </a>--}}
{{--                </li>--}}

{{--                <li class="nav-item has-treeview {{(request()->segment('1') == 'items' || request()->is('issue-inventory-items/create') || request()->is('item-requests') || request()->is('issue-inventory-items') )   ? 'menu-open': ''}}">--}}
{{--                    <a href="#" class="nav-link">--}}
{{--                        <i class="nav-icon fas fa-plus-circle"></i>--}}
{{--                        <p>--}}
{{--                            Inventory--}}
{{--                            <i class="right fas fa-angle-left"></i>--}}
{{--                        </p>--}}
{{--                    </a>--}}
{{--                    <ul class="nav nav-treeview">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('items.index')}}"--}}
{{--                               class="nav-link {{(request()->segment('1') == 'items') ? 'active': ''}}">--}}
{{--                                <i class="far nav-icon"></i>--}}
{{--                                <p>Inventory Items</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="#"--}}
{{--                               class="nav-link">--}}
{{--                                <i class="far nav-icon"></i>--}}
{{--                                <p>Stock In</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}

{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('issue-inventory-items.create')}}"--}}
{{--                               class="nav-link {{request()->is('issue-inventory-items/create') ? 'active' : ''}}">--}}
{{--                                <i class="far nav-icon"></i>--}}
{{--                                <p>Stock Out</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}

{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('item-requests.index')}}"--}}
{{--                               class="nav-link {{request()->is('item-requests') ? 'active': ''}}">--}}
{{--                                <i class="far nav-icon"></i>--}}
{{--                                <p>Requested Items</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}

{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('issue-inventory-items.index')}}"--}}
{{--                               class="nav-link {{request()->is('issue-inventory-items') ? 'active': ''}}">--}}
{{--                                <i class="far nav-icon"></i>--}}
{{--                                <p>Issued Items</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}

{{--                    </ul>--}}
{{--                </li>--}}

                <!-- Income -->

                @if(auth()->user()->can('income.view'))

                    <li class="nav-item has-treeview {{(request()->segment('1') == 'income-heads' || request()->segment('1') == 'incomes' || request()->segment('1') == 'income-head-items')  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>
                                Incomes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            @if(auth()->user()->can('income.view'))

                                <li class="nav-item">
                                    <a href="{{route('incomes.index')}}"
                                       class="nav-link {{request()->segment('1') == 'incomes' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Incomes</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('heads.index', ['type' => 'income'])}}"
                                       class="nav-link {{request()->segment('1') == 'income-heads' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Income Heads</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('head-items.index', ['type' => 'income'])}}"
                                       class="nav-link {{request()->segment('1') == 'income-head-items' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Items</p>
                                    </a>
                                </li>

                            @endif

                        </ul>
                    </li>

                @endif

                <!-- Expense -->

                @if(auth()->user()->can('expenses.view') || auth()->user()->can('purchase.view'))

                    <li class="nav-item has-treeview {{(request()->segment('1') == 'expense-heads' || request()->segment('1') == 'expenses' || request()->segment('1') == 'expense-head-items' )  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-minus-circle"></i>
                            <p>
                                Expenses
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            @if(auth()->user()->can('expense.view'))

                                <li class="nav-item">
                                    <a href="{{route('expenses.index')}}"
                                       class="nav-link {{request()->segment('1') == 'expenses' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Expenses</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('heads.index', ['type' => 'expense'])}}"
                                       class="nav-link {{request()->segment('1') == 'heads' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Expense Heads</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('head-items.index', ['type' => 'expense'])}}"
                                       class="nav-link {{request()->segment('1') == 'expense-head-items' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Expense Items</p>
                                    </a>
                                </li>

                            @endif

                            @if(auth()->user()->can('purchase.view'))

                                <li class="nav-item">
                                    <a href="{{route('purchases.index')}}"
                                       class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Purchase</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('items.index')}}"
                                       class="nav-link {{request()->segment('1') == 'inventory-items' ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Purchase Items</p>
                                    </a>
                                </li>

                            @endif

                        </ul>
                    </li>

                @endif

                @if(auth()->user()->can('lot.view'))

                    <li class="nav-item has-treeview {{(request()->segment('1') == 'lots' )  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Lots
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{route('lots.index')}}"
                                   class="nav-link {{request()->is('lots') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lot Lists</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('lots.create')}}"
                                   class="nav-link {{request()->is('/lots/create') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Add Lot</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('lots-get-search')}}"
                                   class="nav-link {{request()->is('/lots/create') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Search Lot</p>
                                </a>
                            </li>

                        </ul>
                    </li>

                @endif

                <!-- Budget -->

                @if(auth()->user()->can('expense-budget.view') || auth()->user()->can('income-budget.view') || auth()->user()->can('purchase-plan.view'))

                    <li class="nav-item has-treeview {{(request()->segment('1') == 'budgets' )  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>
                                Budgets
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if(auth()->user()->can('expense-budget.view'))
                                <li class="nav-item">
                                    <a href="{{route('budgets.index', ['type' => 'expense'])}}"
                                       class="nav-link {{ (request()->is('budgets') && request()->query('type') ==='expense') ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Expense Budget</p>
                                    </a>
                                </li>
                            @endif

                            <!--                        <li class="nav-item">
                            <a href="{{route('budgets.create', ['type' => 'expense'])}}"
                               class="nav-link {{(request()->is('/budgets/create') && request()->query('type') ==='expense')  ? 'active': ''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Expense Budget</p>
                            </a>
                        </li>-->
                            @if(auth()->user()->can('income-budget.view'))
                                <li class="nav-item">
                                    <a href="{{route('budgets.index', ['type' => 'income'])}}"
                                       class="nav-link {{(request()->is('budgets') && request()->query('type') ==='income') ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Income Budget</p>
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->can('purchase-plan.view'))
                                <li class="nav-item">
                                    <a href="{{route('purchase-plans.index')}}"
                                       class="nav-link {{(request()->is('budgets') && request()->query('type') ==='purchase') ? 'active': ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Purchase Plan</p>
                                    </a>
                                </li>
                            @endif

                            <!--                        <li class="nav-item">
                            <a href="{{route('budgets.create', ['type' => 'income'])}}"
                               class="nav-link {{(request()->is('/budgets/create') && request()->query('type') ==='income') ? 'active': ''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Income Budget</p>
                            </a>
                        </li>-->

                        </ul>
                    </li>

                @endif

                <!-- Payroll -->

                @if(auth()->user()->can('payroll.view'))

                    <li class="nav-item has-treeview {{(request()->segment('1') == 'payrolls' )  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-circle"></i>
                            <p>
                                Payroll
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{route('payrolls.index')}}"
                                   class="nav-link {{request()->is('payrolls') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Payroll List</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('payrolls.create')}}"
                                   class="nav-link {{request()->is('/payrolls/create') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Add Payroll</p>
                                </a>
                            </li>

                        </ul>
                    </li>

                @endif

                <!-- Reports -->

                @if(auth()->user()->can('report.view'))

                    <li class="nav-item has-treeview {{(request()->is('bank-cashbook-report') || request()->is('hold-items-report') || request()->is('reconciliation-report') || request()->is('returned-items-report') || request()->is('pending-items-report') || request()->is('bank-wise-report') || request()->is('lot-wise-report') ) ? 'menu-open' : ''}}">
                        <a href="#"
                           class="nav-link">
                            <i class="nav-icon fas fa-list-alt"></i>
                            <p>
                                Reports
                            </p>
                            <i class="right fas fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item has-treeview {{(request()->is('bank-cashbook-report') || request()->is('hold-items-report') || request()->is('reconciliation-report') || request()->is('returned-items-report') || request()->is('pending-items-report')) ? 'menu-open' : ''}}">
                                <a href="{{route('bank-report')}}"
                                   class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bank Account Report</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>

                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{route('bank-cashbook-report')}}"
                                           class="nav-link {{request()->is('bank-cashbook-report') ? 'active' : ''}} ? 'active': ''}}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Cash Book</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('payment-items-report')}}"
                                           class="nav-link {{request()->is('payment-items-report') ? 'active': ''}}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Payments Items</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('returned-items-report')}}"
                                           class="nav-link {{request()->is('returned-items-report') ? 'active': ''}}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Returned Items</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('pending-items-report')}}"
                                           class="nav-link {{request()->is('pending-items-report') ? 'active': ''}}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Pending Items</p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{route('hold-items-report')}}"
                                           class="nav-link {{request()->is('hold-items-report') ? 'active': ''}}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Hold Items</p>
                                        </a>
                                    </li>


                                    <li class="nav-item">
                                        <a href="{{route('stop-items-report')}}"
                                           class="nav-link {{request()->is('stop-items-report') ? 'active': ''}}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Stop Items</p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{route('reconciliation-report')}}"
                                           class="nav-link {{request()->is('reconciliation-report') ? 'active': ''}}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Reconciliation Report</p>
                                        </a>
                                    </li>

                                </ul>

                            </li>

                            <li class="nav-item">
                                <a href="{{route('bank-cashbook-report', ['petty_cash' => true])}}"
                                   class="nav-link {{request()->is('bank-cashbook-report') ? 'active' : ''}} ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Petty Cash Book</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('bank-wise-report')}}"
                                   class="nav-link {{request()->is('bank-wise-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bank Wise Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('lot-wise-report')}}"
                                   class="nav-link {{request()->is('lot-wise-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lot Wise T/E Payment Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#"
                                   class="nav-link {{request()->is('reports.one') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Party Payment Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route("expense-report")}}"
                                   class="nav-link {{request()->is('expense-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Office Expense Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route("income-report")}}"
                                   class="nav-link {{request()->is('income-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Income Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route("fdr-report")}}"
                                   class="nav-link {{request()->is('fdr-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>FDR Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route("fdr-account-report")}}"
                                   class="nav-link {{request()->is('fdr-account-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>FDR Summary Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route("std-account-report")}}"
                                   class="nav-link {{request()->is('std-account-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>STD Summary Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#"
                                   class="nav-link {{request()->is('reports.one') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>6% Income Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#"
                                   class="nav-link {{request()->is('reports.one') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Purchase Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#"
                                   class="nav-link {{request()->is('reports.one') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Interest Report</p>
                                </a>
                            </li>

                            <li class="nav-item has-treeview">
                                <a href="#"
                                   class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Budget Report</p>
                                    <i class="right fas fa-angle-left"></i>
                                </a>

                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{route("budget-report")}}"
                                           class="nav-link {{request()->is('budget-report') ? 'active': ''}}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Budget Expense Report</p>
                                        </a>
                                    </li>
                                </ul>

                            </li>

                            <li class="nav-item">
                                <a href="{{route("ledger-report.get-ledger-report")}}"
                                   class="nav-link {{request()->is('ledger-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ledger Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route("date-wise-lot-report")}}"
                                   class="nav-link {{request()->is('date-wise-lot-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lot Report</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/income-expense-summary"
                                   class="nav-link {{request()->is('income-expense-summary') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Income Expense Summary</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/budget-ghatti-report"
                                   class="nav-link {{request()->is('budget-ghatti-report') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ghatti Report</p>
                                </a>
                            </li>

                        </ul>
                    </li>

                @endif

                <!-- Settings -->

                <!-- only super admin can view -->
                @if(auth()->user()->hasRole('Super Admin'))
                    <li class="nav-item has-treeview {{(request()->segment('1') == 'financial-years' )  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-circle"></i>
                            <p>
                                Settings
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{route('settings.index')}}"
                                   class="nav-link {{request()->is('settings') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>General Settings</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('financial-years.index')}}"
                                   class="nav-link {{request()->is('financial-years') ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Financial Years</p>
                                </a>
                            </li>
                        </ul>
                    </li>



                    <!-- User Management -->

                    <li class="nav-item has-treeview {{(request()->segment('1') == 'roles' || request()->segment('1') == 'users' )  ? 'menu-open': ''}}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>
                                Manage Users
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('users.index')}}"
                                   class="nav-link {{request()->segment('1') == 'users' ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Users</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{route('roles.index')}}"
                                   class="nav-link {{request()->segment('1') == 'roles' ? 'active': ''}}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Roles</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                @endif

            </ul>
        </nav>

        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
