<aside id="sidenav-main" class="sidebar navbar navbar-vertical navbar-expand-xs" id="sidebar-nav">
    <div class="sidenav-header d-flex justify-content-center align-items-center px-3 py-3">
        @php
            $settings = App\Models\Management\Settings::first();
        @endphp
        <a href="{{ route('admin.dashboard') }}" class="m-0 d-flex align-items-center">
            <img src="{{ asset($settings->logo_path) }}" alt="Logo" width="auto" height="32" class="me-2">
            <span class="fw-semibold text-dark" style="font-size: 20px;">{{ $settings->company_name }}</span>
        </a>
    </div>
    <div class="px-3 py-2">
        @php
            $softwareType = config('app.software_type', 1);
        @endphp

        <!-- Search Box -->
        <div class="search-container mb-4">
            <div class="position-relative">
                <input type="search" id="sidebar-search" class="form-control rounded ps-5 py-2" placeholder="Search">
                <span class="position-absolute top-50 translate-middle-y" style="left: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#999" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                    </svg>
                </span>
            </div>
        </div>

        <!-- Menu Items -->
        <ul class="sidebar-nav-list list-unstyled" id="sidebar-menu">

            @permission('management.date-lock.view')
            <li class="menu-item mb-2" data-name="date-lock">
                <a href="{{ route('admin.management.date-lock.index') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.management.date-lock.index') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-lock"></i>
                    </div>
                    <span class="menu-text">Date Lock</span>
                </a>
            </li>
            @endpermission

            <!-- Overview Menu Item -->
            <li class="menu-item mb-2" data-name="overview">
                <a href="{{ route('admin.dashboard') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.dashboard') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-grid-1x2"></i>
                    </div>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <!-- Daybook Menu Item -->
            @permission('daybook.view')
            <li class="menu-item mb-2" data-name="daybook">
                <a href="{{ route('admin.daybook.index') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.daybook.index') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <span class="menu-text">Daybook</span>
                </a>
            </li>
            @endpermission

            <!-- Purchase Menu Item -->
            @permission('purchase.view')
            <li class="menu-item mb-2" data-name="purchases">
                <a href="{{ route('purchase.index') }}"
                   class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('purchase.index') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-cart"></i>
                    </div>
                    <span class="menu-text">Purchase</span>
                </a>
            </li>

            @endpermission

            <!-- Sales Menu Item -->
            @permission('sales.view')
            @if($softwareType == 1)
                <li class="menu-item mb-2" data-name="sales">
                    <a href="{{ route('sales.index') }}" class="menu-link d-flex align-items-center rounded p-2">
                        <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <span class="menu-text">Sales</span>
                    </a>
                </li>
            @else
                @permission('sales.nozzle.view')
                <li class="menu-item mb-2" data-name="nozzle-sales">
                    <a href="{{ route('sales.nozzle.index') }}" class="menu-link d-flex align-items-center rounded p-2">
                        <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-fuel-pump"></i>
                        </div>
                        <span class="menu-text">Nozzle Sales</span>
                    </a>
                </li>
                @endpermission
                @permission('sales.lubricant.view')
                <li class="menu-item mb-2" data-name="lube-sales">
                    <a href="{{ route('sales.lubricant.index') }}" class="menu-link d-flex align-items-center rounded p-2">
                        <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-basket"></i>
                        </div>
                        <span class="menu-text">Lubricant Sales</span>
                    </a>
                </li>
                @endpermission
                @if($softwareType == 2)
                    @permission('sales.credit.view')
                    <li class="menu-item mb-2" data-name="credit-sales">
                        <a href="{{ route('sales.credit.index') }}" class="menu-link d-flex align-items-center rounded p-2">
                            <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                                <i class="bi bi-credit-card-2-front"></i>
                            </div>
                            <span class="menu-text">Credit Sales</span>
                        </a>
                    </li>
                    @endpermission
                @endif
            @endif
            @endpermission

            <!-- Journal Vouchers Menu Item -->
            @permission('journal.view')
            <li class="menu-item mb-2" data-name="journal-vouchers">
                <a href="{{ route('admin.journal.index') }}" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-journal"></i>
                    </div>
                    <span class="menu-text">Journal Vouchers</span>
                </a>
            </li>
            @endpermission

            <!-- Trial Balance Menu Item -->
            @permission('trial-balance.view')
            <li class="menu-item mb-2" data-name="trial-balance">
                <a href="{{ route('admin.trial-balance.index') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.trial-balance.index') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-list-columns-reverse"></i>
                    </div>
                    <span class="menu-text">Trial Balance</span>
                </a>
            </li>
            @endpermission

            <!-- Profit and Loss Menu Item -->
            @permission('profit.view')
            <li class="menu-item mb-2" data-name="profit-loss">
                <a href="{{ route('admin.profit.index') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.profit.index') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <span class="menu-text">Profit and Loss</span>
                </a>
            </li>
            @endpermission

            <!-- Dips Menu Item -->
            @permission('dips.view')
            <li class="menu-item mb-2" data-name="dips">
                <a href="{{ route('admin.dips.index') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.dips.index') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-columns-gap"></i>
                    </div>
                    <span class="menu-text">Dips</span>
                </a>
            </li>
            @endpermission

            <!-- Wet Stock Menu Item -->
            @permission('wet-stock.view')
            <li class="menu-item mb-2" data-name="wet-stock">
                <a href="{{ route('admin.wet-stock.index') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.wet-stock.*') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-water"></i>
                    </div>
                    <span class="menu-text">Wet Stock</span>
                </a>
            </li>
            @endpermission

            <!-- Billing Menu Item -->
            @if($softwareType == 2)
                @permission('billing.view')
                <li class="menu-item mb-2" data-name="billing">
                    <a href="{{ route('admin.billing.index') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.billing.*') ? 'active' : '') }}">
                        <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <span class="menu-text">Billing</span>
                    </a>
                </li>
                @endpermission
            @endif


            @if(auth()->user()->hasAnyPermission(['payments.bank-receiving.view', 'payments.bank-payments.view', 'payments.cash-receiving.view', 'payments.cash-payments.view']))
            <li class="menu-item mb-2" data-name="payments">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('paymentsSubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <span class="menu-text">Payments</span>
                </a>
                <div class="collapse" id="paymentsSubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        @permission('payments.bank-receiving.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.payments.bank-receiving') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <div class="submenu-icon d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-cash"></i>
                                </div>
                                <span class="submenu-text">Bank Receipts</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('payments.bank-payments.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.payments.bank-payments') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <div class="submenu-icon d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-wallet"></i>
                                </div>
                                <span class="submenu-text">Bank Payments</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('payments.cash-receiving.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.payments.cash-receiving') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <div class="submenu-icon d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-receipt-cutoff"></i>
                                </div>
                                <span class="submenu-text">Cash Receipts</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('payments.cash-payments.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.payments.cash-payments') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <div class="submenu-icon d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                                <span class="submenu-text">Cash Payments</span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </div>
            </li>
            @endif


            @if(auth()->user()->hasAnyPermission(['ledger.product.view', 'ledger.supplier.view', 'ledger.customer.view', 'ledger.bank.view', 'ledger.cash.view', 'ledger.mp.view', 'ledger.expense.view', 'ledger.income.view', 'ledger.employee.view']))
            <li class="menu-item mb-2" data-name="ledgers">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('ledgersSubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-book"></i>
                    </div>
                    <span class="menu-text">Ledgers</span>
                </a>
                <div class="collapse" id="ledgersSubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        @permission('ledger.product.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.product') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-box-seam me-2"></i>
                                <span class="submenu-text">Product Ledger</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('ledger.supplier.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.supplier') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck me-2"></i>
                                <span class="submenu-text">Supplier Ledger</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('ledger.customer.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.customer') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-people me-2"></i>
                                <span class="submenu-text">Customer Ledger</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('ledger.bank.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.bank') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-bank2 me-2"></i>
                                <span class="submenu-text">Bank Ledger</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('ledger.cash.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.cash') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-cash-stack me-2"></i>
                                <span class="submenu-text">Cash Ledger</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('ledger.mp.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.mp') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-joystick me-2"></i>
                                <span class="submenu-text">MP Ledger</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('ledger.expense.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.expense') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-wallet2 me-2"></i>
                                <span class="submenu-text">Expense Ledger</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('ledger.employee.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.employee') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-person-vcard me-2"></i>
                                <span class="submenu-text">Employee Ledger</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('ledger.income.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.ledger.income') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-graph-up-arrow me-2"></i>
                                <span class="submenu-text">Income Ledger</span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </div>
            </li>
            @endif


            @if(auth()->user()->hasAnyPermission(['history.purchases.view', 'history.sales.view', 'history.bank-receivings.view', 'history.bank-payments.view', 'history.cash-receipts.view', 'history.cash-payments.view', 'history.journal-vouchers.view', 'logs.view']))
            <li class="menu-item mb-2" data-name="history">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('historySubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <span class="menu-text">History</span>
                </a>
                <div class="collapse" id="historySubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        @permission('history.purchases.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.history.purchases') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-cart me-2"></i>
                                <span class="submenu-text">Purchases</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('history.sales.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.history.sales') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-credit-card me-2"></i>
                                <span class="submenu-text">Sales</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('history.credit-sales.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.history.credit-sales') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-credit-card-2-front me-2"></i>
                                <span class="submenu-text">Credit Sales</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('history.bank-receivings.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.history.bank-receivings') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-receipt-cutoff me-2"></i>
                                <span class="submenu-text">Bank Receivings</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('history.bank-payments.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.history.bank-payments') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-wallet me-2"></i>
                                <span class="submenu-text">Bank Payments</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('history.cash-receipts.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.history.cash-receipts') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-receipt-cutoff me-2"></i>
                                <span class="submenu-text">Cash Receipts</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('history.cash-payments.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.history.cash-payments') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-wallet me-2"></i>
                                <span class="submenu-text">Cash Payments</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('history.journal-vouchers.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.history.journal-vouchers') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-journal me-2"></i>
                                <span class="submenu-text">Journal Vouchers</span>
                            </a>
                        </li>
                        @endpermission
                        {{-- <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-droplet me-2"></i>
                                <span class="submenu-text">Dips</span>
                            </a>
                        </li> --}}
                        @permission('logs.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.logs.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-activity me-2"></i>
                                <span class="submenu-text">System Logs</span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </div>
            </li>
            @endif


            <!-- Management Menu Item -->
            @if(auth()->user()->hasAnyPermission(['management.customers.view', 'management.banks.view', 'management.tanklari.view', 'management.drivers.view', 'management.employees.view', 'management.expenses.view', 'management.incomes.view', 'management.nozzles.view', 'management.products.view', 'management.tanks.view', 'management.suppliers.view', 'management.terminals.view', 'management.transports.view', 'management.users.view', 'management.settings.view', 'management.date-lock.view']))
            <li class="menu-item mb-2" data-name="security">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('securitySubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-building-gear"></i>
                    </div>
                    <span class="menu-text">Management</span>
                </a>
                <div class="collapse" id="securitySubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        @permission('management.banks.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.banks.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-bank2 me-2"></i>
                                <span class="submenu-text">Banks</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.customers.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.customers.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-people me-2"></i>
                                <span class="submenu-text">Customers</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.tanklari.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.tanklari.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-fuel-pump me-2"></i>
                                <span class="submenu-text">Tank Lorry</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.drivers.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.drivers.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-people-fill me-2"></i>
                                <span class="submenu-text">Drivers</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.employees.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.employees.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-person-vcard me-2"></i>
                                <span class="submenu-text">Employees</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.expenses.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.expenses.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-cash-stack me-2"></i>
                                <span class="submenu-text">Expenses Types</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.incomes.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.incomes.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-graph-up-arrow me-2"></i>
                                <span class="submenu-text">Income Types</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.nozzles.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.nozzles.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-droplet me-2"></i>
                                <span class="submenu-text">Nozzles</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.products.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.products.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-box-seam me-2"></i>
                                <span class="submenu-text">Products</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.tanks.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.tanks.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-database me-2"></i>
                                <span class="submenu-text">Tanks</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.suppliers.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.suppliers.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck me-2"></i>
                                <span class="submenu-text">Suppliers</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.terminals.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.terminals.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-terminal me-2"></i>
                                <span class="submenu-text">Terminals</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.transports.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.transports.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck-front me-2"></i>
                                <span class="submenu-text">Transports</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.users.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.users.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-person-gear me-2"></i>
                                <span class="submenu-text">Users</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('management.settings.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.management.settings.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-gear me-2"></i>
                                <span class="submenu-text">Settings</span>
                            </a>
                        </li>
                        @endpermission
                        {{-- @permission('management.date-lock.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.management.date-lock.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-lock me-1"></i>
                                <i class="bi bi-calendar-lock me-2"></i>
                                <span class="submenu-text">Date Lock</span>
                            </a>
                        </li>
                        @endpermission --}}
                        @superadmin
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.roles-permissions.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-shield-lock me-2"></i>
                                <span class="submenu-text">Roles & Permissions</span>
                            </a>
                        </li>
                        @endsuperadmin
                    </ul>
                </div>
            </li>
            @endif

            <!-- Reports Menu Item -->
            @if(auth()->user()->hasAnyPermission(['reports.account-history.view', 'reports.all-stocks.view', 'reports.summary.view', 'reports.purchase-transport.view', 'reports.sale-transport.view']))
            <li class="menu-item mb-2" data-name="reports">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('reportsSubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </div>
                    <span class="menu-text">Reports</span>
                </a>
                <div class="collapse" id="reportsSubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        @permission('reports.account-history.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.reports.account-history') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-clock-history me-2"></i>
                                <span class="submenu-text">Account History</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('reports.all-stocks.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.reports.all-stocks') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-box-seam me-2"></i>
                                <span class="submenu-text">All Stocks</span>
                            </a>
                        </li>
                        @endpermission
                        {{-- @permission('daybook.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.daybook.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-journal-text me-2"></i>
                                <span class="submenu-text">Day Book</span>
                            </a>
                        </li>
                        @endpermission --}}
                        @permission('reports.summary.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.reports.summary') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-file-earmark-check me-2"></i>
                                <span class="submenu-text">Summary</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('reports.purchase-transport.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.reports.purchase-transport') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck me-2"></i>
                                <span class="submenu-text">Purchase Transport Report</span>
                            </a>
                        </li>
                        @endpermission
                        @permission('reports.sale-transport.view')
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.reports.sale-transport') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck-flatbed me-2"></i>
                                <span class="submenu-text">Sale Transport Report</span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </div>
            </li>
            @endif

             <!-- General Search -->
             <li class="menu-item mb-2" data-name="general-search">
                <a href="{{ route('admin.general-search.index') }}" class="menu-link d-flex align-items-center rounded p-2 {{ (request()->routeIs('admin.general-search.index') ? 'active' : '') }}">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-search"></i>
                    </div>
                    <span class="menu-text">General Search</span>
                </a>
            </li>

            <!-- Logout Menu Item -->
            <li class="menu-item mb-2" data-name="logout">
                <a href="{{ route('admin.logout') }}" class="menu-link d-flex align-items-center rounded p-2" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <span class="menu-text">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>



        </ul>
    </div>
</aside>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/sidebar.js') }}"></script>
@endpush

