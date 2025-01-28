<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Laravel SB Admin 2">
    <meta name="author" content="Alejandro RH">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Favicon -->
    <link href="{{ asset('img/favicon.png') }}" rel="icon" type="image/png">
    <!-- Bootstrap 5 JavaScript CDN -->
   

    <!-- Include Chart.js and the ChartDataLabels Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/home') }}">
                <div class="sidebar-brand-icon">
                    <img src="https://sippn.menpan.go.id/images/article/large/logo-jepara-11.png" alt="Logo" style="max-height: 50px;">
                </div>
                <div class="sidebar-brand-text mx-3">
                    @if(auth()->check())
                    @if(auth()->user()->hasRole('superadmin'))
                    Superadmin <sup>Dasa Wisma</sup>
                    @elseif(auth()->user()->hasRole('administrator'))
                    Administrator <sup>Dasa Wisma</sup>
                    @elseif(auth()->user()->hasRole('user'))
                    User <sup>Dasa Wisma</sup>
                    @endif
                    @else
                    <span>Guest <sup>Dasa Wisma</sup></span>
                    @endif
                </div>


            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ Nav::isRoute('admin.dashboard') }}">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>{{ __('Dashboard') }}</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                {{ __('Halaman Dasa Wisma') }}
            </div>

            <!-- Navigasi Dasa Wisma -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dasawisma.index') }}">
                    <i class="fas fa-fw fa-home"></i> <!-- Ganti dengan ikon rumah -->
                    <span>{{ __('Dasa Wisma') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.laporan.index') }}">
                    <i class="fas fa-fw fa-book"></i>
                    <span>{{ __('Laporan') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.laporan.statistik') }}">
                    <i class="fas fa-fw fa-chart-pie"></i>
                    <span>{{ __('Statistik') }}</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                {{ __('User Settings') }}
            </div>

            <li class="nav-item {{ Nav::isRoute('admin.users.index') }}">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>{{ __('User') }}</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                {{ __('Account Management') }}
            </div>

            <!-- Nav Item - Profile -->
            <li class="nav-item {{ Nav::isRoute('admin.profile') }}">
                <a class="nav-link" href="{{ route('admin.profile') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>{{ __('Profile') }}</span>
                </a>
            </li>
            <!-- Nav Item - Logout -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Logout') }}</span>
                </a>
            </li>

            <!-- Nav Item - About 
            <li class="nav-item {{ Nav::isRoute('about') }}">
                <a class="nav-link" href="{{ route('about') }}">
                    <i class="fas fa-fw fa-hands-helping"></i>
                    <span>{{ __('About') }}</span>
                </a>
            </li>
-->


            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    @if (auth()->check())
                                    {{ Auth::user()->fullName }}<br>
                                    <small class="font-weight-bold text-center" style="display: block;">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </small>
                                    @else
                                    <span>Guest</span> <!-- Jika user tidak login -->
                                    @endif
                                </span>

                                @if (auth()->check() && Auth::user()->profile_photo)
                                <img class="img-profile rounded-circle" src="{{ asset('storage/profile_photos/' . Auth::user()->profile_photo) }}" alt="Profile Photo">
                                @elseif (auth()->check())
                                <figure class="img-profile rounded-circle avatar font-weight-bold" data-initial="{{ Auth::user()->name[0] }}"></figure>
                                @else
                                <figure class="img-profile rounded-circle avatar font-weight-bold" data-initial="G"></figure>
                                @endif
                            </a>

                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                @if (auth()->check())
                                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                @else
                                <a class="dropdown-item" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Login
                                </a>
                                @endif
                            </div>
                        </li>


                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @yield('main-content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; <a href="https://github.com/Romadhoni04" target="_blank">Kominfo Jepara</a> {{ now()->year }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Ready to Leave?') }}</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-link" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <a class="btn btn-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('js/demo/chart-pie-demo.js') }}"></script>
    <script src="{{ asset('js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('js/demo/chart-bar-demo.js') }}"></script>

    @yield('scripts')
    @yield('styles')
</body>

</html>