<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('home') }}"
            target="_blank">
            <img src="{{ asset('/img/logos/iotlab.jpg') }}" class="navbar-brand-img h-100 ms-1" alt="main_logo">
            <span class="ms-1 font-weight-bold">Smart Office</span>
            <small class="font-weight-light">by IoT Lab UNS</small>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav" style="display: block; position: absolute; z-index: 10;">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}" href="{{ route('home') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mt-3 d-flex align-items-center">
                <h6 class="ps-2 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account Info</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'profile' ? 'active' : '' }}" href="{{ route('profile') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'user-management') == true ? 'active' : '' }}" href="{{ route('page', ['page' => 'user-management']) }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-bullet-list-67 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">List of Access</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-2 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Energy Managements</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'energy') == true ? 'active' : '' }}" href="{{ route('energy', ['jenis' => 'energy-monitor']) }}">
                {{-- <a class="nav-link {{ str_contains(request()->url(), 'tables') == true ? 'active' : '' }}" href="#energyDropdown"> --}}
                    <div
                        class=" icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-plug-circle-bolt" style="color: #20c997;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Energy</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{  Route::currentRouteName() == 'security' ? 'active' : '' }}" href="{{ url('camera') }}">
                    <div
                        class="icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-shield-halved" style="color: #1671c0;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Security</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'virtual-reality' ? 'active' : '' }}" href="{{ route('virtual-reality') }}">
                    <div
                        class="icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-lightbulb" style="color: #fd7e14"></i>
                    </div>
                    <span class="nav-link-text ms-1">Environment</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-2 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Room Managements</h6>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-2 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Config Pages</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'profile-static' ? 'active' : '' }}" href="{{ route('profile-static') }}">
                    <div
                        class="icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-list-check text-danger"></i>
                    </div>
                    <span class="nav-link-text ms-1">List of Devices</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="{{ route('sign-in-static') }}">
                    <div
                        class="icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-gears"></i>
                    </div>
                    <span class="nav-link-text ms-1">Setting</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="{{ route('sign-up-static') }}">
                    <div
                        class="icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-microchip" style="color: #596CFF"></i>
                    </div>
                    <span class="nav-link-text ms-1">Integrated System</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/docs/bootstrap/overview/argon-dashboard/index.html" target="_blank"
                class="btn btn-dark btn-sm w-100 mb-3">Documentation</a>
            </li>
        </ul>
    </div>
    {{-- <div class="sidenav-footer mx-3 ">
        <div class="card card-plain shadow-none" id="sidenavCard">
            <img class="w-50 mx-auto" src="/img/illustrations/icon-documentation-warning.svg"
                alt="sidebar_illustration">
            <div class="card-body text-center p-3 w-100 pt-0">
                <div class="docs-info">
                    <h6 class="mb-0">Need help?</h6>
                    <p class="text-xs font-weight-bold mb-0">Please check our docs</p>
                </div>
            </div>
        </div>
        <a href="/docs/bootstrap/overview/argon-dashboard/index.html" target="_blank"
            class="btn btn-dark btn-sm w-100 mb-3">Documentation</a>
        <a class="btn btn-primary btn-sm mb-0 w-100"
            href="https://www.creative-tim.com/product/argon-dashboard-pro-laravel" target="_blank" type="button">Upgrade to PRO</a>
    </div> --}}
</aside>
