<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@yield('title', 'Petrol Pump')</title>
        <meta name="meta_title" content="@yield('title', 'Petrol Pump')">
        <meta name="meta_description" content="@yield('description', 'Petrol Pump')">
        <meta name="meta_keywords" content="@yield('keywords', 'Petrol Pump')">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/" rel="preconnect">
        <link href="https://fonts.gstatic.com/" rel="preconnect" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
            rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('assets/icons/bootstrap-icons.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/swiper-bundle.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/aos.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/glightbox.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/drift-basic.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/main.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/custom.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/social-icons.css') }}">
        <link rel="icon" type="icon" href="{{ asset('images/new-logo.png') }}">

        <!-- Audio player -->
        <link rel="stylesheet" href="{{ asset('resources/audioplayer/style.css') }}" media="screen">

        <style>
            /* Cookie Popup */
            .cookie-popup {
                border-radius: 20px;
                border: 1px solid #D9DBE9;
                box-shadow: 0px 14px 42px 0px rgba(8, 15, 52, 0.06);
                width: 40%;
                display: none;
            }

            .cookie-popup .btn-primary {
                border-color: #d35c57 !important;
                color: #f0f0f0
            }

            .cookie-popup .btn-secondary {
                border-color: #f0f0f0 !important;
                background-color: #f0f0f0 !important;
                color: #000 !important;
            }

            .cookie-popup {
                @media only screen and (max-width: 992px) {
                    width: 90%;
                }
            }

            .cookie-popup p {
                line-height: 1.2;
            }

            .cookie-popup .btn {
                font-size: 12px !important;
            }
        </style>
        <script type="text/javascript" src="//s3.amazonaws.com/valao-cloud/cookie-hinweis/script-v2.js"></script>

        @yield('styles')
    </head>

    <body>

        {{-- <header id="header" class="header position-relative">
            <div class="main-header">
                <div class="container-fluid container-xl">
                    <div class="d-flex py-3 align-items-center justify-content-between">
                        <a href="{{ route('home') }}" class="logo d-flex align-items-center">
                            <img src="{{ asset('images/new-logo.png') }}" alt="Root Sounds Logo">
                        </a>

                        <div class="header-nav d-flex gap-4">
                            <div class="container-fluid container-xl">
                                <div class="position-relative">
                                    <nav id="navmenu" class="navmenu">
                                        <ul>
                                            <li><a class="{{ request()->routeIs('home') ? 'active' : '' }}"
                                                    href="{{ route('home') }}">Home</a></li>
                                            <li class="dropdown">
                                                <a href="#"><span>Products</span> <i
                                                        class="bi bi-chevron-down toggle-dropdown"></i></a>
                                                <ul class="products-menu">
                                                    <li class="row g-0">
                                                        @foreach ($prod as $product)
                                                            <span class="col-md-6">
                                                                <a
                                                                    href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                                            </span>
                                                        @endforeach
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a href="{{ route('contact') }}"
                                                    class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact
                                                    Us</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="header-actions d-flex align-items-center justify-content-end">
                                <i class="mobile-nav-toggle d-xl-none bi bi-list me-0"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse" id="mobileSearch">
                <div class="container">
                    <form class="search-form">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for products" />
                            <button class="btn" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </header> --}}

        <main class="main">
            @yield('content')
        </main>

        {{-- <footer id="footer" class="footer">
            <div class="footer-main">
                <div class="container">
                    <div class="row gy-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="footer-widget footer-about pe-lg-5">
                                <a href="/" class="footer-logo d-flex align-items-center">
                                    <img src="/images/new-logo.png" alt="">
                                </a>
                                <p class= "mt-3">root sounds is the sample library division of root studio. We have
                                    been collecting sounds for more than 30 years, offering meticulously crafted sample
                                    libraries for your creative projects.</p>
                                <div class="footer-contact mt-4">
                                    <div class="contact-item">
                                        <i class="bi bi-envelope"></i>
                                        <span><a class= "ctext-primary" href="{{ route('contact') }}">Contact
                                                Us</a></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-6 ms-auto">
                            <div class="footer-widget">
                                <h4>Company</h4>
                                <ul class="footer-links">
                                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                                    <li><a href="{{ route('terms') }}">Terms and conditions</a></li>
                                    <li><a href="{{ route('privacy') }}">Privacy policy</a></li>
                                    <li><a href="{{ route('license') }}">License agreement</a></li>
                                    <li><a href="{{ route('refund') }}">Refund policy</a></li>
                                    <li><a href="{{ route('imprint') }}">Imprint</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="container">
                    @php
                        $settings = \App\Models\Setting::first();
                    @endphp
                    <div class="social-links text-center mb-3">
                        <a href="{{ $settings->facebook }}"
                            class="facebook mx-2 {{ $settings->facebook ?? 'd-none' }}" title="Facebook"
                            target="_blank"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="{{ $settings->instagram }}"
                            class="twitter mx-2 {{ $settings->instagram ?? 'd-none' }}" title="Twitter"
                            target="_blank"><i class="bi bi-twitter-x fs-5"></i></a>
                        <a href="{{ $settings->youtube }}" class="youtube mx-2 {{ $settings->youtube ?? 'd-none' }}"
                            title="YouTube" target="_blank"><i class="bi bi-youtube fs-5"></i></a>
                        <a href="{{ $settings->soundcloud }}"
                            class="soundcloud mx-2 {{ $settings->soundcloud ?? 'd-none' }}" title="SoundCloud"
                            target="_blank"><i class="bi bi-soundwave fs-5"></i></a>
                        <a href="{{ $settings->linkedin }}"
                            class="linkedin mx-2 {{ $settings->linkedin ?? 'd-none' }}" title="LinkedIn"
                            target="_blank"><i class="bi bi-linkedin fs-5"></i></a>
                    </div>
                    <div class="copyright text-center">
                        <p>&copy; 2012-<span id="year"></span> <span class= "ctext-primary"
                                href="{{ route('home') }}">root sounds</span>. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </footer> --}}

        <!-- Scroll Top -->
        <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>

        <!-- Preloader -->
        <div id="preloader"></div>


        <!-- Vendor JS Files -->

        <script src="{{ asset('assets/jquery/jquery-3.7.1.min.js') }}"></script>

        <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>

        <script data-cfasync="false" src="{{ asset('resources/assets/js/email-decode.min.js') }}"></script>
        <script src="{{ asset('resources/assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('resources/assets/js/validate.js') }}"></script>
        <script src="{{ asset('resources/assets/js/swiper-bundle.min.js') }}"></script>
        <script src="{{ asset('resources/assets/js/aos.js') }}"></script>
        <script src="{{ asset('resources/assets/js/imagesloaded.pkgd.min.js') }}"></script>
        <script src="{{ asset('resources/assets/js/isotope.pkgd.min.js') }}"></script>
        <script src="{{ asset('resources/assets/js/glightbox.min.js') }}"></script>
        <script src="{{ asset('resources/assets/js/Drift.min.js') }}"></script>
        <script src="{{ asset('resources/assets/js/purecounter_vanilla.js') }}"></script>

        <!-- Main JS File -->
        <script src="{{ asset('resources/assets/js/main.js') }}"></script>

        <script>
            document.getElementById("year").textContent = new Date().getFullYear();
        </script>

        @yield('scripts')
    </body>

</html>
