document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidenav-main");
    const showSidebarbtn = document.getElementById("show-sidebarbtn");

    // Sidebar toggle functionality
    if (showSidebarbtn && sidebar) {
        showSidebarbtn.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                document.body.classList.toggle('sidebar-open');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
            }
        });
    }

    // Close sidebar on outside click (mobile)
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

            menuItems.forEach(item => {
                const itemName = item.getAttribute('data-name').toLowerCase();
                const menuText = item.querySelector('.menu-text').textContent.toLowerCase();
                const hasSubmenu = item.querySelector('.collapse') !== null;
                let showItem = false;

                if (itemName.includes(searchTerm) || menuText.includes(searchTerm)) {
                    showItem = true;
                    hasResults = true;
                }

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

                    const collapse = item.querySelector('.collapse');
                    if (hasSubmenuMatch && searchTerm) {
                        collapse.classList.add('show');
                    } else if (!searchTerm) {
                        // Do nothing, keep current state
                    } else {
                        collapse.classList.remove('show');
                    }
                }

                item.style.display = showItem ? 'block' : 'none';
            });

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

    // Active state detection
    detectActiveStates();
});

// Toggle submenu with single dropdown behavior
function toggleSubmenu(id) {
    const submenu = document.getElementById(id);
    if (submenu) {
        // Close all other submenus first
        const allSubmenus = document.querySelectorAll('.collapse');
        allSubmenus.forEach(menu => {
            if (menu.id !== id && menu.classList.contains('show')) {
                menu.classList.remove('show');
            }
        });
        
        // Toggle the clicked submenu
        if (submenu.classList.contains('show')) {
            submenu.classList.remove('show');
        } else {
            submenu.classList.add('show');
        }
    }
}

// Helper function to activate routes
function activateRoute(routeType) {
    switch (routeType) {
        case 'sales-main':
            const salesLink = document.querySelector('a[href="/sales"]');
            if (salesLink) salesLink.classList.add('active');
            break;
            
        case 'sales-nozzle':
            const nozzleSalesLink = document.querySelector('a[href="/sales/nozzle"]');
            if (nozzleSalesLink) nozzleSalesLink.classList.add('active');
            break;
            
        case 'sales-lubricant':
            const lubricantSalesLink = document.querySelector('a[href="/sales/lubricant"]');
            if (lubricantSalesLink) lubricantSalesLink.classList.add('active');
            break;
            
        case 'history-main':
            const historyLink = document.querySelector('a[onclick*="historySubmenu"]');
            if (historyLink) {
                historyLink.classList.add('active');
                const historySubmenu = document.getElementById('historySubmenu');
                if (historySubmenu) historySubmenu.classList.add('show');
            }
            break;
            
        case 'history-sales':
            const historySalesLink = document.querySelector('a[onclick*="historySubmenu"]');
            const salesHistoryLink = document.querySelector('a[href="/history/sales"]');
            if (historySalesLink) {
                historySalesLink.classList.add('active');
                const historySubmenu = document.getElementById('historySubmenu');
                if (historySubmenu) historySubmenu.classList.add('show');
            }
            if (salesHistoryLink) salesHistoryLink.classList.add('active');
            break;
            
        case 'history-purchases':
            const historyPurchasesLink = document.querySelector('a[onclick*="historySubmenu"]');
            const purchasesHistoryLink = document.querySelector('a[href="/history/purchases"]');
            if (historyPurchasesLink) {
                historyPurchasesLink.classList.add('active');
                const historySubmenu = document.getElementById('historySubmenu');
                if (historySubmenu) historySubmenu.classList.add('show');
            }
            if (purchasesHistoryLink) purchasesHistoryLink.classList.add('active');
            break;
            
        case 'history-bank-receivings':
            const historyBankReceivingsLink = document.querySelector('a[onclick*="historySubmenu"]');
            const bankReceivingsHistoryLink = document.querySelector('a[href="/history/bank-receivings"]');
            if (historyBankReceivingsLink) {
                historyBankReceivingsLink.classList.add('active');
                const historySubmenu = document.getElementById('historySubmenu');
                if (historySubmenu) historySubmenu.classList.add('show');
            }
            if (bankReceivingsHistoryLink) bankReceivingsHistoryLink.classList.add('active');
            break;
            
        case 'history-bank-payments':
            const historyBankPaymentsLink = document.querySelector('a[onclick*="historySubmenu"]');
            const bankPaymentsHistoryLink = document.querySelector('a[href="/history/bank-payments"]');
            if (historyBankPaymentsLink) {
                historyBankPaymentsLink.classList.add('active');
                const historySubmenu = document.getElementById('historySubmenu');
                if (historySubmenu) historySubmenu.classList.add('show');
            }
            if (bankPaymentsHistoryLink) bankPaymentsHistoryLink.classList.add('active');
            break;
            
        case 'history-cash-receipts':
            const historyCashReceiptsLink = document.querySelector('a[onclick*="historySubmenu"]');
            const cashReceiptsHistoryLink = document.querySelector('a[href="/history/cash-receipts"]');
            if (historyCashReceiptsLink) {
                historyCashReceiptsLink.classList.add('active');
                const historySubmenu = document.getElementById('historySubmenu');
                if (historySubmenu) historySubmenu.classList.add('show');
            }
            if (cashReceiptsHistoryLink) cashReceiptsHistoryLink.classList.add('active');
            break;
            
        case 'history-cash-payments':
            const historyCashPaymentsLink = document.querySelector('a[onclick*="historySubmenu"]');
            const cashPaymentsHistoryLink = document.querySelector('a[href="/history/cash-payments"]');
            if (historyCashPaymentsLink) {
                historyCashPaymentsLink.classList.add('active');
                const historySubmenu = document.getElementById('historySubmenu');
                if (historySubmenu) historySubmenu.classList.add('show');
            }
            if (cashPaymentsHistoryLink) cashPaymentsHistoryLink.classList.add('active');
            break;
            
        case 'history-journal-vouchers':
            const historyJournalVouchersLink = document.querySelector('a[onclick*="historySubmenu"]');
            const journalVouchersHistoryLink = document.querySelector('a[href="/history/journal-vouchers"]');
            if (historyJournalVouchersLink) {
                historyJournalVouchersLink.classList.add('active');
                const historySubmenu = document.getElementById('historySubmenu');
                if (historySubmenu) historySubmenu.classList.add('show');
            }
            if (journalVouchersHistoryLink) journalVouchersHistoryLink.classList.add('active');
            break;
            
        case 'purchase-main':
            const purchaseLink = document.querySelector('a[href="/purchase"]');
            if (purchaseLink) purchaseLink.classList.add('active');
            break;
            
        case 'journal-main':
            const journalLink = document.querySelector('a[href="/journal"]');
        if (journalLink) journalLink.classList.add('active');
            break;
            
        case 'management-main':
        const managementLink = document.querySelector('a[onclick*="securitySubmenu"]');
        if (managementLink) {
            managementLink.classList.add('active');
            const securitySubmenu = document.getElementById('securitySubmenu');
            if (securitySubmenu) securitySubmenu.classList.add('show');
        }
            break;
            
        case 'management-banks':
            const managementBanksLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const banksManagementLink = document.querySelector('a[href="/banks"]');
            if (managementBanksLink) {
                managementBanksLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (banksManagementLink) banksManagementLink.classList.add('active');
            break;
            
        case 'management-customers':
            const managementCustomersLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const customersManagementLink = document.querySelector('a[href="/customers"]');
            if (managementCustomersLink) {
                managementCustomersLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (customersManagementLink) customersManagementLink.classList.add('active');
            break;
            
        case 'management-suppliers':
            const managementSuppliersLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const suppliersManagementLink = document.querySelector('a[href="/suppliers"]');
            if (managementSuppliersLink) {
                managementSuppliersLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (suppliersManagementLink) suppliersManagementLink.classList.add('active');
            break;
            
        case 'management-products':
            const managementProductsLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const productsManagementLink = document.querySelector('a[href="/products"]');
            if (managementProductsLink) {
                managementProductsLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (productsManagementLink) productsManagementLink.classList.add('active');
            break;
            
        case 'management-tanks':
            const managementTanksLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const tanksManagementLink = document.querySelector('a[href="/tanks"]');
            if (managementTanksLink) {
                managementTanksLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (tanksManagementLink) tanksManagementLink.classList.add('active');
            break;
            
        case 'management-nozzles':
            const managementNozzlesLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const nozzlesManagementLink = document.querySelector('a[href="/nozzles"]');
            if (managementNozzlesLink) {
                managementNozzlesLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (nozzlesManagementLink) nozzlesManagementLink.classList.add('active');
            break;
            
        case 'management-drivers':
            const managementDriversLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const driversManagementLink = document.querySelector('a[href="/drivers"]');
            if (managementDriversLink) {
                managementDriversLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (driversManagementLink) driversManagementLink.classList.add('active');
            break;
            
        case 'management-employees':
            const managementEmployeesLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const employeesManagementLink = document.querySelector('a[href="/employees"]');
            if (managementEmployeesLink) {
                managementEmployeesLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (employeesManagementLink) employeesManagementLink.classList.add('active');
            break;
            
        case 'management-expenses':
            const managementExpensesLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const expensesManagementLink = document.querySelector('a[href="/expenses"]');
            if (managementExpensesLink) {
                managementExpensesLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (expensesManagementLink) expensesManagementLink.classList.add('active');
            break;
            
        case 'management-incomes':
            const managementIncomesLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const incomesManagementLink = document.querySelector('a[href="/incomes"]');
            if (managementIncomesLink) {
                managementIncomesLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (incomesManagementLink) incomesManagementLink.classList.add('active');
            break;
            
        case 'management-terminals':
            const managementTerminalsLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const terminalsManagementLink = document.querySelector('a[href="/terminals"]');
            if (managementTerminalsLink) {
                managementTerminalsLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (terminalsManagementLink) terminalsManagementLink.classList.add('active');
            break;
            
        case 'management-transports':
            const managementTransportsLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const transportsManagementLink = document.querySelector('a[href="/transports"]');
            if (managementTransportsLink) {
                managementTransportsLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (transportsManagementLink) transportsManagementLink.classList.add('active');
            break;
            
        case 'management-users':
            const managementUsersLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const usersManagementLink = document.querySelector('a[href="/users"]');
            if (managementUsersLink) {
                managementUsersLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (usersManagementLink) usersManagementLink.classList.add('active');
            break;
            
        case 'management-settings':
            const managementSettingsLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const settingsManagementLink = document.querySelector('a[href="/settings"]');
            if (managementSettingsLink) {
                managementSettingsLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (settingsManagementLink) settingsManagementLink.classList.add('active');
            break;
            
        case 'management-roles':
            const managementRolesLink = document.querySelector('a[onclick*="securitySubmenu"]');
            const rolesManagementLink = document.querySelector('a[href="/roles-permissions"]');
            if (managementRolesLink) {
                managementRolesLink.classList.add('active');
                const securitySubmenu = document.getElementById('securitySubmenu');
                if (securitySubmenu) securitySubmenu.classList.add('show');
            }
            if (rolesManagementLink) rolesManagementLink.classList.add('active');
            break;
            
        case 'payments-main':
        const paymentsLink = document.querySelector('a[onclick*="paymentsSubmenu"]');
        if (paymentsLink) {
            paymentsLink.classList.add('active');
            const paymentsSubmenu = document.getElementById('paymentsSubmenu');
            if (paymentsSubmenu) paymentsSubmenu.classList.add('show');
        }
            break;
            
        case 'payments-bank':
            const paymentsBankLink = document.querySelector('a[onclick*="paymentsSubmenu"]');
            const bankPaymentsLink = document.querySelector('a[href="/payments/bank"]');
            if (paymentsBankLink) {
                paymentsBankLink.classList.add('active');
                const paymentsSubmenu = document.getElementById('paymentsSubmenu');
                if (paymentsSubmenu) paymentsSubmenu.classList.add('show');
            }
            if (bankPaymentsLink) bankPaymentsLink.classList.add('active');
            break;
            
        case 'payments-cash':
            const paymentsCashLink = document.querySelector('a[onclick*="paymentsSubmenu"]');
            const cashPaymentsLink = document.querySelector('a[href="/payments/cash"]');
            if (paymentsCashLink) {
                paymentsCashLink.classList.add('active');
                const paymentsSubmenu = document.getElementById('paymentsSubmenu');
                if (paymentsSubmenu) paymentsSubmenu.classList.add('show');
            }
            if (cashPaymentsLink) cashPaymentsLink.classList.add('active');
            break;
            
        case 'ledger-main':
        const ledgerLink = document.querySelector('a[onclick*="ledgersSubmenu"]');
        if (ledgerLink) {
            ledgerLink.classList.add('active');
            const ledgersSubmenu = document.getElementById('ledgersSubmenu');
            if (ledgersSubmenu) ledgersSubmenu.classList.add('show');
        }
            break;
            
        case 'ledger-customer':
            const ledgerCustomerLink = document.querySelector('a[onclick*="ledgersSubmenu"]');
            const customerLedgerLink = document.querySelector('a[href="/ledger/customer"]');
            if (ledgerCustomerLink) {
                ledgerCustomerLink.classList.add('active');
                const ledgersSubmenu = document.getElementById('ledgersSubmenu');
                if (ledgersSubmenu) ledgersSubmenu.classList.add('show');
            }
            if (customerLedgerLink) customerLedgerLink.classList.add('active');
            break;
            
        case 'ledger-supplier':
            const ledgerSupplierLink = document.querySelector('a[onclick*="ledgersSubmenu"]');
            const supplierLedgerLink = document.querySelector('a[href="/ledger/supplier"]');
            if (ledgerSupplierLink) {
                ledgerSupplierLink.classList.add('active');
                const ledgersSubmenu = document.getElementById('ledgersSubmenu');
                if (ledgersSubmenu) ledgersSubmenu.classList.add('show');
            }
            if (supplierLedgerLink) supplierLedgerLink.classList.add('active');
            break;
            
        case 'reports-main':
        const reportsLink = document.querySelector('a[onclick*="reportsSubmenu"]');
        if (reportsLink) {
            reportsLink.classList.add('active');
            const reportsSubmenu = document.getElementById('reportsSubmenu');
            if (reportsSubmenu) reportsSubmenu.classList.add('show');
            }
            break;
            
        case 'reports-account-history':
            const reportsAccountHistoryLink = document.querySelector('a[onclick*="reportsSubmenu"]');
            const accountHistoryReportsLink = document.querySelector('a[href="/reports/account-history"]');
            if (reportsAccountHistoryLink) {
                reportsAccountHistoryLink.classList.add('active');
                const reportsSubmenu = document.getElementById('reportsSubmenu');
                if (reportsSubmenu) reportsSubmenu.classList.add('show');
            }
            if (accountHistoryReportsLink) accountHistoryReportsLink.classList.add('active');
            break;
            
        case 'reports-trial-balance':
            const reportsTrialBalanceLink = document.querySelector('a[onclick*="reportsSubmenu"]');
            const trialBalanceReportsLink = document.querySelector('a[href="/reports/trial-balance"]');
            if (reportsTrialBalanceLink) {
                reportsTrialBalanceLink.classList.add('active');
                const reportsSubmenu = document.getElementById('reportsSubmenu');
                if (reportsSubmenu) reportsSubmenu.classList.add('show');
            }
            if (trialBalanceReportsLink) trialBalanceReportsLink.classList.add('active');
            break;
            
        case 'reports-profit':
            const reportsProfitLink = document.querySelector('a[onclick*="reportsSubmenu"]');
            const profitReportsLink = document.querySelector('a[href="/reports/profit"]');
            if (reportsProfitLink) {
                reportsProfitLink.classList.add('active');
                const reportsSubmenu = document.getElementById('reportsSubmenu');
                if (reportsSubmenu) reportsSubmenu.classList.add('show');
            }
            if (profitReportsLink) profitReportsLink.classList.add('active');
            break;
            
        case 'dips':
            const dipsLink = document.querySelector('a[href="/dips"]');
            if (dipsLink) dipsLink.classList.add('active');
            break;
            
        case 'wet-stock':
            const wetStockLink = document.querySelector('a[href="/wet-stock"]');
            if (wetStockLink) wetStockLink.classList.add('active');
            break;
            
        case 'daybook':
            const daybookLink = document.querySelector('a[href="/daybook"]');
            if (daybookLink) daybookLink.classList.add('active');
            break;
    }
}

// Detect and set active states
function detectActiveStates() {
	const normalizePath = (path) => {
		if (!path) return '/';
		// Ensure leading slash and remove trailing slashes
		const url = new URL(path, window.location.origin);
		let p = url.pathname.replace(/\/+$/, '');
		return p === '' ? '/' : p;
	};

	const currentPath = normalizePath(window.location.pathname);

	// Clear existing states
	document.querySelectorAll('.menu-link, .submenu-link').forEach(link => link.classList.remove('active'));
	document.querySelectorAll('.collapse').forEach(c => c.classList.remove('show'));

	let matched = false;

	// Exact match on any sidebar link (top-level or submenu)
	document.querySelectorAll('a.menu-link, a.submenu-link').forEach(link => {
		const linkPath = normalizePath(link.getAttribute('href'));
		if (linkPath && linkPath !== 'javascript:void(0)' && linkPath !== '#' && linkPath === currentPath) {
			matched = true;
			link.classList.add('active');

			// If this is inside a submenu, open the parent collapse and mark parent toggle active
			const parentCollapse = link.closest('.collapse');
			if (parentCollapse) {
				parentCollapse.classList.add('show');
				const parentMenuItem = parentCollapse.closest('.menu-item');
				if (parentMenuItem) {
					const parentToggle = parentMenuItem.querySelector('.menu-link');
					if (parentToggle) parentToggle.classList.add('active');
				}
			}

			// If this is a top-level link that itself has a submenu next to it, open that submenu
			const owningMenuItem = link.closest('.menu-item');
			if (owningMenuItem) {
				const ownCollapse = owningMenuItem.querySelector('.collapse');
				if (ownCollapse) ownCollapse.classList.add('show');
			}
		}
	});

	// Special handling for dashboard route (route('admin.dashboard') likely maps to '/')
	if (!matched && currentPath === '/') {
		const dashboardLink = document.querySelector('a.menu-link[href="/"]');
		if (dashboardLink) {
			dashboardLink.classList.add('active');
        }
    }
}
