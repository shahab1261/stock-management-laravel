<nav class="navbar navbar-main navbar-expand-lg px-0 bg-white shadow-sm" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-2 px-3">
        <div class="d-flex justify-content-between w-100 align-items-center">
            <div class="d-flex align-items-center">
                <button id="show-sidebarbtn" class="btn btn-icon hamburger-btn me-3 d-flex" type="button">
                    <div class="hamburger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <h5 class="mb-0 d-none d-sm-block">Dashboard</h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-circle me-2 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 32px; height: 32px;">
                            <span>A</span>
                        </div>
                        <span class="d-none d-md-block">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="post">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
