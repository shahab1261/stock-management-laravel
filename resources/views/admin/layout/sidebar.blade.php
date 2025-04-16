<aside id="sidenav-main" class="sidebar navbar navbar-vertical navbar-expand-xs" id="sidebar-nav">
    <div class="sidenav-header d-flex justify-content-center align-items-center px-3 py-3">
        <a href="{{ route('admin.dashboard') }}" class="m-0 d-flex align-items-center">
            <img src="{{ asset('images/new-logo.png') }}" alt="Logo" width="auto" height="32" class="me-2">
            <span class="fw-semibold text-dark" style="font-size: 20px;">Stock Management</span>
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
                        <i class="bi bi-grid-1x2-fill"></i>
                    </div>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            
            <!-- Hosting Plan Menu Item -->
            <li class="menu-item mb-2" data-name="hosting plan">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('hostingPlanSubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-hdd-stack"></i>
                    </div>
                    <span class="menu-text">Nozzle Sales</span>
                </a>
                <div class="collapse" id="hostingPlanSubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <span class="submenu-text">Profit</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <span class="submenu-text">Credit Sales</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Performance Menu Item -->
            <li class="menu-item mb-2" data-name="performance">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('performanceSubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <span class="menu-text">Credit Sales</span>
                </a>
                <div class="collapse" id="performanceSubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <span class="submenu-text">Speed Optimization</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <span class="submenu-text">Cache Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Analytics Menu Item -->
            <li class="menu-item mb-2" data-name="analytics">
                <a href="#" class="menu-link d-flex align-items-center rounded p-2">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <span class="menu-text">Journal Vouchers</span>
                </a>
            </li>
            
            <!-- Security Menu Item -->
            <li class="menu-item mb-2" data-name="security">
                <a href="javascript:void(0)" class="menu-link d-flex align-items-center rounded p-2" onclick="toggleSubmenu('securitySubmenu')">
                    <div class="menu-icon d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <span class="menu-text">Management</span>
                </a>
                <div class="collapse" id="securitySubmenu">
                    <ul class="submenu list-unstyled ms-4 mt-2">
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <span class="submenu-text">SSL Certificates</span>
                            </a>
                        </li>
                        <li class="submenu-item mb-2">
                            <a href="#" class="submenu-link d-flex align-items-center rounded p-2">
                                <span class="submenu-text">Firewall Settings</span>
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
