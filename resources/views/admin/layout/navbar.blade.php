<nav class="navbar navbar-main navbar-expand-lg px-0  bg-white " id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <button id="show-sidebarbtn" class="me-3 cursor-pointer toggle-sidebar-btn btn"
                style="color: #4154f1; border-color: #4154f1; transition: none; background-color: transparent;">
                <i class="bi bi-list" style="font-size: 1.5rem;"></i>
            </button>
            {{-- <div class=" w-100 dashboard-breadcrumb">
                <span class="dashboard-bread-home">Home</span>
                <img src="{{ asset('assets/img/dashboard-right-arrow.svg') }} " alt="">
                <span class=" dashboard-bread-page">Dashboard</span>
            </div> --}}
            <div class=" d-flex align-items-center ms-auto  ">
                <li class=" pe-4 ">
                </li>
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                        data-bs-toggle="dropdown">
                        <span class="d-none d-md-block dropdown-toggle ps-2">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li>
                            <form action="{{ route('admin.logout') }}" method="post">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center">
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </div>
        </div>
    </div>
</nav>
