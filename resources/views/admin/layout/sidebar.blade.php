<aside id="sidenav-main" class="sidebar navbar navbar-vertical navbar-expand-xs" id="sidebar-nav">
    <div class="sidenav-header  d-flex justify-content-between align-items-center">

        <a href="{{ route('home') }}" class=" m-0 aHoverRemove  d-flex justify-content-center w-100">
            <img src="{{ asset('images/new-logo.png') }} " alt="" width="auto" height="56">
        </a>
        <svg id="sidebar-cross-icon" class="me-3    sidebar-cross-icon cursor-pointer" xmlns="http://www.w3.org/2000/svg"
            id="Outline" viewBox="0 0 24 24" fill="gray" width="30" height="30">
            <path
                d="M18,6h0a1,1,0,0,0-1.414,0L12,10.586,7.414,6A1,1,0,0,0,6,6H6A1,1,0,0,0,6,7.414L10.586,12,6,16.586A1,1,0,0,0,6,18H6a1,1,0,0,0,1.414,0L12,13.414,16.586,18A1,1,0,0,0,18,18h0a1,1,0,0,0,0-1.414L13.414,12,18,7.414A1,1,0,0,0,18,6Z" />
        </svg>
    </div>
    <hr class="horizontal dark mt-0">
    <ul class="navbar-nav" id=''>
        <li class="nav-item ">
            <a class="nav-link admin_dashboardli {{ (request()->routeIs('admin.dashboard') ? 'active' : '') }}" href="{{ route('admin.dashboard') }}" id="admin_dashboardli">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">

                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#000" fill-rule="evenodd" d="M4.825 11h3.38c.121 0 .32 0 .502.016c.225.02.609.076 1.015.303a2.5 2.5 0 0 1 .96.96c.226.405.282.789.302 1.014c.017.183.016.381.016.502v5.41c0 .121 0 .32-.016.502c-.02.225-.075.609-.303 1.015a2.5 2.5 0 0 1-.96.96a2.5 2.5 0 0 1-1.014.302C8.524 22 8.326 22 8.205 22h-3.41c-.12 0-.32 0-.502-.016a2.5 2.5 0 0 1-1.014-.303a2.5 2.5 0 0 1-.96-.96a2.5 2.5 0 0 1-.303-1.014C2 19.524 2 19.326 2 19.205v-5.41c0-.12 0-.32.016-.502c.02-.225.076-.609.303-1.014a2.5 2.5 0 0 1 .96-.96a2.5 2.5 0 0 1 1.014-.303C4.476 11 4.674 11 4.795 11zm-.761 2.256C4 13.37 4 13.52 4 13.826v5.35c0 .303 0 .455.064.568a.5.5 0 0 0 .192.192c.114.064.265.064.569.064h3.35c.304 0 .456 0 .57-.064a.5.5 0 0 0 .191-.192C9 19.631 9 19.48 9 19.175v-5.35c0-.304 0-.455-.064-.57a.5.5 0 0 0-.192-.191C8.631 13 8.48 13 8.175 13h-3.35c-.304 0-.455 0-.57.064a.5.5 0 0 0-.191.192M4.825 2h3.38c.121 0 .32 0 .502.016c.225.02.609.076 1.015.303a2.5 2.5 0 0 1 .96.96c.226.405.282.789.302 1.014c.017.183.016.381.016.502v2.41c0 .121 0 .32-.016.502c-.02.225-.075.609-.303 1.015a2.5 2.5 0 0 1-.96.96a2.5 2.5 0 0 1-1.014.302c-.183.017-.381.016-.502.016h-3.41c-.12 0-.32 0-.502-.016a2.5 2.5 0 0 1-1.014-.303a2.5 2.5 0 0 1-.96-.96a2.5 2.5 0 0 1-.303-1.014C2 7.524 2 7.326 2 7.205v-2.41c0-.12 0-.32.016-.502c.02-.225.076-.609.303-1.014a2.5 2.5 0 0 1 .96-.96a2.5 2.5 0 0 1 1.014-.303C4.476 2 4.674 2 4.795 2zm-.761 2.256C4 4.37 4 4.52 4 4.825v2.35c0 .304 0 .456.064.57a.5.5 0 0 0 .192.191C4.37 8 4.52 8 4.825 8h3.35c.304 0 .456 0 .57-.064a.5.5 0 0 0 .191-.192C9 7.631 9 7.48 9 7.175v-2.35c0-.304 0-.455-.064-.57a.5.5 0 0 0-.192-.191C8.631 4 8.48 4 8.175 4h-3.35c-.304 0-.455 0-.57.064a.5.5 0 0 0-.191.192M15.825 13h3.38c.121 0 .32 0 .502-.016c.225-.02.609-.075 1.015-.303a2.5 2.5 0 0 0 .96-.96c.227-.405.282-.789.302-1.014c.016-.183.016-.381.016-.502v-5.41c0-.12 0-.32-.016-.502a2.5 2.5 0 0 0-.303-1.014a2.5 2.5 0 0 0-.96-.96a2.5 2.5 0 0 0-1.014-.303C19.524 2 19.326 2 19.205 2h-3.41c-.12 0-.32 0-.502.016c-.225.02-.609.076-1.014.303a2.5 2.5 0 0 0-.96.96a2.5 2.5 0 0 0-.303 1.014C13 4.476 13 4.674 13 4.795v5.41c0 .121 0 .32.016.502c.02.225.076.609.303 1.015a2.5 2.5 0 0 0 .96.96c.405.226.789.282 1.014.302c.183.017.381.016.502.016zm-.761-2.256C15 10.63 15 10.48 15 10.175v-5.35c0-.304 0-.455.064-.57a.5.5 0 0 1 .192-.191C15.37 4 15.52 4 15.826 4h3.35c.303 0 .455 0 .568.064a.5.5 0 0 1 .192.192c.064.114.064.265.064.569v5.35c0 .304 0 .455-.064.57a.5.5 0 0 1-.192.191c-.113.064-.265.064-.569.064h-3.35c-.304 0-.455 0-.57-.064a.5.5 0 0 1-.191-.192M15.825 22h3.38c.121 0 .32 0 .502-.016c.225-.02.609-.076 1.015-.303a2.5 2.5 0 0 0 .96-.96c.227-.405.282-.789.302-1.014c.016-.183.016-.381.016-.502v-2.41c0-.12 0-.32-.016-.502a2.5 2.5 0 0 0-.303-1.014a2.5 2.5 0 0 0-.96-.96a2.5 2.5 0 0 0-1.014-.303C19.524 14 19.326 14 19.205 14h-3.41c-.12 0-.32 0-.502.016c-.225.02-.609.076-1.014.303a2.5 2.5 0 0 0-.96.96a2.5 2.5 0 0 0-.303 1.014c-.016.183-.016.381-.016.502v2.41c0 .121 0 .32.016.502c.02.225.076.609.303 1.015a2.5 2.5 0 0 0 .96.96c.405.227.789.282 1.014.302c.183.016.381.016.502.016zm-.761-2.256C15 19.631 15 19.48 15 19.175v-2.35c0-.304 0-.455.064-.57a.5.5 0 0 1 .192-.191c.114-.064.265-.064.57-.064h3.35c.303 0 .455 0 .568.064c.08.045.147.111.192.192c.064.114.064.265.064.57v2.35c0 .303 0 .455-.064.568a.5.5 0 0 1-.192.192c-.113.064-.265.064-.569.064h-3.35c-.304 0-.455 0-.57-.064a.5.5 0 0 1-.191-.192" clip-rule="evenodd"/></svg>
                </div>
                <span class="nav-link-text ms-1">Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link admin-faqs {{ (request()->routeIs('admin.products.create') ? 'active' : '') }}" href="{{ route('admin.products.create') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none"><path stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m11 12l8.073-4.625M11 12L6.963 9.688M11 12v2.281m8.073-6.906a3.17 3.17 0 0 0-1.165-1.156L15.25 4.696m3.823 2.679c.275.472.427 1.015.427 1.58v1.608M2.926 7.374a3.14 3.14 0 0 0-.426 1.58v6.09c0 1.13.607 2.172 1.592 2.736l5.316 3.046A3.2 3.2 0 0 0 11 21.25M2.926 7.375a3.17 3.17 0 0 1 1.166-1.156l5.316-3.046a3.2 3.2 0 0 1 3.184 0l2.658 1.523M2.926 7.375l4.037 2.313m0 0l8.287-4.992"/><path fill="#000" fill-rule="evenodd" d="M17.5 23a5.5 5.5 0 1 0 0-11a5.5 5.5 0 0 0 0 11m0-8.993a.5.5 0 0 1 .5.5V17h2.493a.5.5 0 1 1 0 1H18v2.493a.5.5 0 1 1-1 0V18h-2.493a.5.5 0 1 1 0-1H17v-2.493a.5.5 0 0 1 .5-.5" clip-rule="evenodd"/></g></svg>
                </div>
                <span class="nav-link-text ms-1">Add Product</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link admin-products {{ request()->routeIs('admin.products.index') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="2048" height="2048" viewBox="0 0 2048 2048"><path fill="#000" d="M1024 1000v959l-64 32l-832-415V536l832-416l832 416v744h-128V680zm-64-736L719 384l621 314l245-122zm-64 1552v-816L256 680v816zM335 576l625 312l238-118l-622-314zm1073 1216v-128h640v128zm0-384h640v128h-640zm-256 640v-128h128v128zm0-512v-128h128v128zm0 256v-128h128v128zm-128 24h1zm384 232v-128h640v128z"/></svg>
                </div>
                <span class="nav-link-text ms-1">All Products</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link admin-orders {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#000" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m0 16H5V5h14zm-7-2h2V7h-4v2h2z"/></svg>
                </div>
                <span class="nav-link-text ms-1">All Orders</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link admin-free-requests {{ request()->routeIs('admin.free-product-requests.index') ? 'active' : '' }}" href="{{ route('admin.free-product-requests.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#000" d="M21 5c-1.1-.35-2.3-.5-3.5-.5c-1.7 0-4.15.65-5.5 1.5V4c-1.45-.35-2.97-.5-4.5-.5C5.26 3.5 3.21 4.24 2 5.5v15c0 .42.23.81.61.96c.19.07.39.04.57-.08c.8-.55 2.35-1.36 4.32-1.36c1.7 0 4.15.64 5.5 1.5c1.55-1 4.05-1.65 5.5-1.5c1.1.09 2.2.35 3.4.88c.18.09.39.04.57-.08c.36-.21.57-.58.57-1V5.5c-.57-.33-1.16-.58-1.97-.78M20 17.5c-1.1-.35-2.3-.5-3.5-.5c-1.7 0-4.15.64-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5c1.2 0 2.4.15 3.5.5z"/></svg>
                </div>
                <span class="nav-link-text ms-1">Free Product Orders</span>
            </a>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link admin-static-pages dropdown-toggle {{ request()->routeIs('admin.testimonials.index') || request()->routeIs('admin.terms.index') || request()->routeIs('admin.privacy.index') || request()->routeIs('admin.license.index') || request()->routeIs('admin.refund.index') || request()->routeIs('admin.faqs.index') ? 'active' : '' }}" href="#" id="staticPagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#000" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m-5 14H7v-2h7zm3-4H7v-2h10zm0-4H7V7h10z"/></svg>
                </div>
                <span class="nav-link-text ms-1">Static Pages</span>
            </a>
            <ul class="dropdown-menu" aria-labelledby="staticPagesDropdown" style="margin-left: 1rem; min-width: 200px; border-radius: 0.5rem; box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);">
                <li><a class="dropdown-item {{ request()->routeIs('admin.testimonials.index') ? 'active' : '' }}" href="{{ route('admin.testimonials.index') }}" style="{{ request()->routeIs('admin.testimonials.index') ? 'color: #4154f1; font-weight: 600;' : '' }}">Testimonials</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.terms.index') ? 'active' : '' }}" href="{{ route('admin.terms.index') }}" style="{{ request()->routeIs('admin.terms.index') ? 'color: #4154f1; font-weight: 600;' : '' }}">Terms & Conditions</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.privacy.index') ? 'active' : '' }}" href="{{ route('admin.privacy.index') }}" style="{{ request()->routeIs('admin.privacy.index') ? 'color: #4154f1; font-weight: 600;' : '' }}">Privacy Policy</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.license.index') ? 'active' : '' }}" href="{{ route('admin.license.index') }}" style="{{ request()->routeIs('admin.license.index') ? 'color: #4154f1; font-weight: 600;' : '' }}">License Agreement</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.refund.index') ? 'active' : '' }}" href="{{ route('admin.refund.index') }}" style="{{ request()->routeIs('admin.refund.index') ? 'color: #4154f1; font-weight: 600;' : '' }}">Refund Policy</a></li>
                <li><a class="dropdown-item {{ request()->routeIs('admin.faqs.index') ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}" style="{{ request()->routeIs('admin.faqs.index') ? 'color: #4154f1; font-weight: 600;' : '' }}">FAQ</a></li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link admin-contacts {{ (request()->routeIs('admin.contact') ? 'active' : '') }}" href="{{ route('admin.contact') }}" href="{{ route('admin.contact') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M16 2v2M7 22v-2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2M8 2v2"/><circle cx="12" cy="11" r="3"/><rect width="18" height="18" x="3" y="4" rx="2"/></g></svg>
                </div>
                <span class="nav-link-text ms-1">Contact Us</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link admin-documents {{ request()->routeIs('admin.documents.index') ? 'active' : '' }}" href="{{ route('admin.documents.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#000" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zm4 18H6V4h7v5h5zm-9-2h2v-2h-2zm0-4h2v-2h-2zm0-4h2V8h-2zm4 8h4v-2h-4zm0-4h4v-2h-4zm0-4h4V8h-4z"/></svg>
                </div>
                <span class="nav-link-text ms-1">All Documents</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link admin-settings {{ (request()->routeIs('admin.settings.index') ? 'active' : '') }}" href="{{ route('admin.settings.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#000" d="M19.9 12.66a1 1 0 0 1 0-1.32l1.28-1.44a1 1 0 0 0 .12-1.17l-2-3.46a1 1 0 0 0-1.07-.48l-1.88.38a1 1 0 0 1-1.15-.66l-.61-1.83a1 1 0 0 0-.95-.68h-4a1 1 0 0 0-1 .68l-.56 1.83a1 1 0 0 1-1.15.66L5 4.79a1 1 0 0 0-1 .48L2 8.73a1 1 0 0 0 .1 1.17l1.27 1.44a1 1 0 0 1 0 1.32L2.1 14.1a1 1 0 0 0-.1 1.17l2 3.46a1 1 0 0 0 1.07.48l1.88-.38a1 1 0 0 1 1.15.66l.61 1.83a1 1 0 0 0 1 .68h4a1 1 0 0 0 .95-.68l.61-1.83a1 1 0 0 1 1.15-.66l1.88.38a1 1 0 0 0 1.07-.48l2-3.46a1 1 0 0 0-.12-1.17ZM18.41 14l.8.9l-1.28 2.22l-1.18-.24a3 3 0 0 0-3.45 2L12.92 20h-2.56L10 18.86a3 3 0 0 0-3.45-2l-1.18.24l-1.3-2.21l.8-.9a3 3 0 0 0 0-4l-.8-.9l1.28-2.2l1.18.24a3 3 0 0 0 3.45-2L10.36 4h2.56l.38 1.14a3 3 0 0 0 3.45 2l1.18-.24l1.28 2.22l-.8.9a3 3 0 0 0 0 3.98m-6.77-6a4 4 0 1 0 4 4a4 4 0 0 0-4-4m0 6a2 2 0 1 1 2-2a2 2 0 0 1-2 2"/></svg>
                </div>
                <span class="nav-link-text ms-1">Settings</span>
            </a>
        </li>
    </ul>
</aside>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const bellIcon = document.getElementById("icon");
        const box = document.getElementById("box");

        bellIcon.addEventListener("click", function() {
            box.classList.toggle("dashboard-show");
        });
    });
</script>

<script>
    // Handle active state for Static Pages dropdown
    document.addEventListener("DOMContentLoaded", function() {
        const staticPagesDropdown = document.getElementById('staticPagesDropdown');
        const dropdownItems = document.querySelectorAll('.dropdown-item');

        // Check if any dropdown item is active
        let hasActiveItem = false;
        dropdownItems.forEach(item => {
            if(item.classList.contains('active')) {
                hasActiveItem = true;
            }
        });

        // If any item is active, make the dropdown button active too
        if(hasActiveItem && staticPagesDropdown) {
            staticPagesDropdown.classList.add('active');
        }
    });
</script>
