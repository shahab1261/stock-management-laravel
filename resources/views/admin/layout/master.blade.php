<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/new-logo.png') }}">
        <!-- Dynamic Meta Titles and Descriptions -->
        <title>@yield('title', 'Root Sounds')</title>
        <meta name="description" content="@yield('description', 'Root Sounds')">

        <!-- CSS Files -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/custommediaqueries.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }} ">
        {{-- <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }} "> --}}
        <link href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/responsive.bootstrap5.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/select.dataTables.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/icons/bootstrap-icons.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

        <style>
            /* Static Pages Dropdown Styling */
            .navbar-nav .dropdown-menu {
                padding: 0.5rem 0;
                background-color: #fff;
                border: none;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0, 0.1);
                border-radius: 0.5rem;
                margin-top: 0.25rem;
            }

            .navbar-nav .dropdown-item {
                padding: 0.6rem 1.5rem;
                font-weight: 500;
                transition: all 0.2s ease;
                position: relative;
                margin: 0 0.5rem;
                border-radius: 0.3rem;
            }

            .navbar-nav .dropdown-item:hover {
                background-color: #f6f9ff;
                color: #4154f1 !important;
                transform: translateX(5px);
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            .navbar-nav .dropdown-item.active {
                background-color: #f6f9ff;
                color: #4154f1 !important;
                font-weight: 600;
            }

            .navbar-nav .nav-item.dropdown .dropdown-toggle.active {
                background-color: #4154f1;
                color: #fff !important;
                box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
                border-radius: 0.5rem;
            }

            .navbar-nav .dropdown-toggle[aria-expanded="true"] {
                background-color: #f6f9ff;
            }

            /* Fix dropdown positioning */
            .navbar-nav .nav-item.dropdown {
                position: relative;
            }



        </style>

    </head>

    <body class="g-sidenav-show bg-gray-100">

        <div id="main" class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
            @include('admin.layout.sidebar')
            <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
                @include('admin.layout.navbar')
                @yield('content')


            </div>
        </div>

        <script src="{{ asset('assets/bootstrap5/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/jquery/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/ajax.js') }}"></script>
        <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
        <script src="{{ asset('assets/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/js/vfs_fonts.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/js/responsive.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.fixedColumns.min.js') }}"></script>

        <script>
            tinymce.init({
                selector: '#my-editor',
                base_url: '/tinymce',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'print', 'preview', 'anchor',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount',
                    'emoticons', 'hr', 'pagebreak', 'codesample'
                ],
                toolbar: 'undo redo | fontselect fontsizeselect formatselect | ' +
                    'bold italic underline strikethrough forecolor backcolor | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | ' +
                    'link image media table | ' +
                    'codesample emoticons hr pagebreak | ' +
                    'removeformat fullscreen help',
                branding: false,
                promotion: false,
                height: 300,
            });
        </script>

        <script>
            (function() {

                "use strict";

                /**
                 * Easy selector helper function
                 */
                const select = (el, all = false) => {
                    el = el.trim()
                    if (all) {
                        return [...document.querySelectorAll(el)]
                    } else {
                        return document.querySelector(el)
                    }
                }

                /**
                 * Easy event listener function
                 */
                const on = (type, el, listener, all = false) => {
                    if (all) {
                        select(el, all).forEach(e => e.addEventListener(type, listener))
                    } else {
                        select(el, all).addEventListener(type, listener)
                    }
                }

                /**
                 * Easy on scroll event listener
                 */
                const onscroll = (el, listener) => {
                    el.addEventListener('scroll', listener)
                }

                /**
                 * Sidebar toggle
                 */
                if (select('.toggle-sidebar-btn')) {
                    on('click', '.toggle-sidebar-btn', function(e) {
                        console.log("click")
                        select('body').classList.toggle('toggle-sidebar')
                    })
                }

                if (select('.search-bar-toggle')) {
                    on('click', '.search-bar-toggle', function(e) {
                        select('.search-bar').classList.toggle('search-bar-show')
                    })
                }


                document.addEventListener("DOMContentLoaded", function() {
                    console.log("sidebar cross")
                    const sidebarCrossIcon = document.getElementById('sidebar-cross-icon');
                    const sidebar = document.getElementById('sidenav-main');
                    const showSidebarbtn = document.getElementById("show-sidebarbtn");
                    showSidebarbtn.addEventListener('click', function() {
                        sidebar.classList.remove('d-none'); // Remove 'd-none' class to show sidebar
                    });

                    sidebarCrossIcon.addEventListener('click', function() {
                        sidebar.classList.add('d-none'); // Add 'd-none' class to hide sidebar
                    });
                });


            })();
        </script>


        @stack('scripts')
    </body>

</html>
