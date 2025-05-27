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
            <li class="menu-item mb-2" data-name="daybook">
                <a href="#" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <span class="menu-text">Daybook</span>
                </a>
            </li>

            <!-- Purchase Menu Item -->
            <li class="menu-item mb-2" data-name="purchases">
                <a href="{{ route('purchase.index') }}" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-cart"></i>
                    </div>
                    <span class="menu-text">Purchase</span>
                </a>
            </li>

            <!-- Sales Menu Item -->
            <li class="menu-item mb-2" data-name="sales">
                <a href="{{ route('sales.index') }}" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <span class="menu-text">Sales</span>
                </a>
            </li>

            <!-- Journal Vouchers Menu Item -->
            <li class="menu-item mb-2" data-name="journal-vouchers">
                <a href="#" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-journal"></i>
                    </div>
                    <span class="menu-text">Journal Vouchers</span>
                </a>
            </li>

            <!-- Trial Balance Menu Item -->
            <li class="menu-item mb-2" data-name="trial-balance">
                <a href="#" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-list-columns-reverse"></i>
                    </div>
                    <span class="menu-text">Trial Balance</span>
                </a>
            </li>

            <!-- Profit and Loss Menu Item -->
            <li class="menu-item mb-2" data-name="profit-loss">
                <a href="#" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <span class="menu-text">Profit and Loss</span>
                </a>
            </li>

            <!-- Balance Sheet Menu Item -->
            <li class="menu-item mb-2" data-name="balance-sheet">
                <a href="#" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-columns-gap"></i>
                    </div>
                    <span class="menu-text">Balance Sheet</span>
                </a>
            </li>

            <!-- Wet Stock Menu Item -->
            <li class="menu-item mb-2" data-name="wet-stock">
                <a href="#" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-water"></i>
                    </div>
                    <span class="menu-text">Wet Stock</span>
                </a>
            </li>


            <li class="menu-item mb-2" data-name="payments">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('paymentsSubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <span class="menu-text">Payments</span>
                </a>
                <div class="collapse" id="paymentsSubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <div class="submenu-icon d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-cash"></i>
                                </div>
                                <span class="submenu-text">Bank Receipts</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <div class="submenu-icon d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-wallet"></i>
                                </div>
                                <span class="submenu-text">Bank Payments</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <div class="submenu-icon d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-receipt-cutoff"></i>
                                </div>
                                <span class="submenu-text">Cash Receipts</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <div class="submenu-icon d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                                <span class="submenu-text">Cash Payments</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-item mb-2" data-name="ledgers">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('ledgersSubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-book"></i>
                    </div>
                    <span class="menu-text">Ledgers</span>
                </a>
                <div class="collapse" id="ledgersSubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-box-seam me-2"></i>
                                <span class="submenu-text">Product Ledger</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck me-2"></i>
                                <span class="submenu-text">Supplier Ledger</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-people me-2"></i>
                                <span class="submenu-text">Customer Ledger</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-bank2 me-2"></i>
                                <span class="submenu-text">Bank Ledger</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-cash-stack me-2"></i>
                                <span class="submenu-text">Cash Ledger</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-joystick me-2"></i>
                                <span class="submenu-text">MP Ledger</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-cash-stack me-2"></i>
                                <span class="submenu-text">Expense Ledger</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-person-vcard me-2"></i>
                                <span class="submenu-text">Employee Ledger</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-graph-up-arrow me-2"></i>
                                <span class="submenu-text">Income Ledger</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-item mb-2" data-name="history">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('historySubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <span class="menu-text">History</span>
                </a>
                <div class="collapse" id="historySubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-cart me-2"></i>
                                <span class="submenu-text">Purchases</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-credit-card me-2"></i>
                                <span class="submenu-text">Sales</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-receipt-cutoff me-2"></i>
                                <span class="submenu-text">Bank Receivings</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-wallet me-2"></i>
                                <span class="submenu-text">Bank Payments</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-receipt-cutoff me-2"></i>
                                <span class="submenu-text">Cash Receipts</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-wallet me-2"></i>
                                <span class="submenu-text">Cash Payments</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-journal me-2"></i>
                                <span class="submenu-text">Journal Vouchers</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-droplet me-2"></i>
                                <span class="submenu-text">Dips</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.logs.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-activity me-2"></i>
                                <span class="submenu-text">System Logs</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Management Menu Item -->
            <li class="menu-item mb-2" data-name="security">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('securitySubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-building-gear"></i>
                    </div>
                    <span class="menu-text">Management</span>
                </a>
                <div class="collapse" id="securitySubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.banks.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-bank2 me-2"></i>
                                <span class="submenu-text">Banks</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.customers.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-people me-2"></i>
                                <span class="submenu-text">Customers</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.tanklari.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-fuel-pump me-2"></i>
                                <span class="submenu-text">Customers Tank Lari</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.drivers.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-people-fill me-2"></i>
                                <span class="submenu-text">Drivers</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.employees.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-person-vcard me-2"></i>
                                <span class="submenu-text">Employees</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.expenses.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-cash-stack me-2"></i>
                                <span class="submenu-text">Expenses Types</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.incomes.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-graph-up-arrow me-2"></i>
                                <span class="submenu-text">Income Types</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.nozzles.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-droplet me-2"></i>
                                <span class="submenu-text">Nozzles</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.products.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-box-seam me-2"></i>
                                <span class="submenu-text">Products</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.tanks.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-database me-2"></i>
                                <span class="submenu-text">Tanks</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.suppliers.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck me-2"></i>
                                <span class="submenu-text">Suppliers</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.terminals.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-terminal me-2"></i>
                                <span class="submenu-text">Terminals</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.transports.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck-front me-2"></i>
                                <span class="submenu-text">Transports</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.users.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-person-gear me-2"></i>
                                <span class="submenu-text">Users</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="{{ route('admin.management.settings.index') }}" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-gear me-2"></i>
                                <span class="submenu-text">Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Reports Menu Item -->
            <li class="menu-item mb-2" data-name="reports">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('reportsSubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </div>
                    <span class="menu-text">Reports</span>
                </a>
                <div class="collapse" id="reportsSubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-clock-history me-2"></i>
                                <span class="submenu-text">Account History</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-box-seam me-2"></i>
                                <span class="submenu-text">All Stocks</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-journal-text me-2"></i>
                                <span class="submenu-text">Day Book</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-file-earmark-check me-2"></i>
                                <span class="submenu-text">Summary</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck me-2"></i>
                                <span class="submenu-text">Purchase Transport Report</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <i class="bi bi-truck-flatbed me-2"></i>
                                <span class="submenu-text">Sale Transport Report</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>



        </ul>
    </div>
</aside>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Sidebar toggle functionality
        const sidebar = document.getElementById("sidenav-main");
        const showSidebarbtn = document.getElementById("show-sidebarbtn");

        if (showSidebarbtn && sidebar) {
            showSidebarbtn.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 992 &&
                !sidebar.contains(event.target) &&
                !showSidebarbtn.contains(event.target) &&
                document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
            }
        });

        // Search functionality
        const searchInput = document.getElementById('sidebar-search');
        const menuItems = document.querySelectorAll('.menu-item');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let hasResults = false;

                // First pass: Check main menu items and their direct text
                menuItems.forEach(item => {
                    const itemName = item.getAttribute('data-name').toLowerCase();
                    const menuText = item.querySelector('.menu-text').textContent.toLowerCase();
                    const hasSubmenu = item.querySelector('.collapse') !== null;
                    let showItem = false;

                    // Check if main item matches
                    if (itemName.includes(searchTerm) || menuText.includes(searchTerm)) {
                        showItem = true;
                        hasResults = true;
                    }

                    // Check submenu items if this item has a submenu
                    if (hasSubmenu) {
                        const submenuItems = item.querySelectorAll('.submenu-item');
                        let hasSubmenuMatch = false;

                        submenuItems.forEach(subItem => {
                            const subItemText = subItem.textContent.toLowerCase();
                            if (subItemText.includes(searchTerm)) {
                                showItem = true;
                                hasSubmenuMatch = true;
                                hasResults = true;
                                subItem.style.display = 'block';
                            } else if (searchTerm) {
                                subItem.style.display = 'none';
                            } else {
                                subItem.style.display = 'block';
                            }
                        });

                        // If any submenu items match, open the dropdown
                        const collapse = item.querySelector('.collapse');
                        if (hasSubmenuMatch && searchTerm) {
                            collapse.classList.add('show');
                        } else if (!searchTerm) {
                            // If search is cleared, keep the dropdown state as is
                        } else {
                            collapse.classList.remove('show');
                        }
                    }

                    // Show/hide the main menu item
                    item.style.display = showItem ? 'block' : 'none';
                });

                // If no search term, reset everything
                if (!searchTerm) {
                    menuItems.forEach(item => {
                        item.style.display = 'block';
                        const submenuItems = item.querySelectorAll('.submenu-item');
                        submenuItems.forEach(subItem => {
                            subItem.style.display = 'block';
                        });
                        const collapse = item.querySelector('.collapse');
                        if (collapse) {
                            collapse.classList.remove('show');
                        }
                    });
                }
            });
        }

        // Auto-expand parent menu when child is active
        const activeSubmenuItems = document.querySelectorAll('.submenu-link.active');
        activeSubmenuItems.forEach(item => {
            const parentCollapse = item.closest('.collapse');
            if (parentCollapse) {
                parentCollapse.classList.add('show');
            }
        });
    });

    // Function to toggle submenu
    function toggleSubmenu(id) {
        const submenu = document.getElementById(id);
        if (submenu) {
            if (submenu.classList.contains('show')) {
                submenu.classList.remove('show');
            } else {
                submenu.classList.add('show');
            }
        }
    }
</script>

