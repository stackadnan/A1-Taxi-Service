<header class="header-section">
    @php($baseUrl = rtrim(request()->getBaseUrl(), '/'))
    @php($homeUrl = $baseUrl === '' ? '/' : $baseUrl.'/')

    <div class="header-top-section style-two">
        <div class="container-fluid">
            <div class="header-top-wrapper style-2">
                <ul class="contact-list">
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:{{ $topEmail }}" class="link">{{ $topEmail }}</a>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $topAddress }}
                    </li>
                </ul>
                <div class="header-top-right">
                    <ul class="top-list">
                        @foreach ($topLinks as $link)
                            <li><a href="{{ $baseUrl.'/'.ltrim($link['url'], '/') }}">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                    <div class="social-icon d-flex align-items-center">
                        @foreach ($socialLinks as $social)
                            <a href="{{ $social['url'] }}"><i class="{{ $social['icon'] }}"></i></a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="header-sticky" class="header-3">
        <div class="container-fluid">
            <div class="mega-menu-wrapper">
                <div class="header-main">
                    <div class="header-left">
                        <div class="logo">
                            <a href="{{ $homeUrl }}" class="header-logo-1">
                                <img src="{{ $logoLight }}" alt="logo-img">
                            </a>
                            <a href="{{ $homeUrl }}" class="header-logo-2">
                                <img src="{{ $logoDark }}" alt="logo-img">
                            </a>
                        </div>
                        <div class="mean__menu-wrapper">
                            <div class="main-menu">
                                <nav id="mobile-menu">
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0)"><i class="fas fa-plane"></i> Airport Taxi Transfers <i class="fas fa-angle-down"></i></a>
                                            <ul class="submenu">
                                                @foreach ($airportLinks as $item)
                                                    <li><a href="{{ $baseUrl.'/'.ltrim($item['url'], '/') }}">{{ $item['label'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="{{ $baseUrl.'/city-transfers' }}"><i class="fas fa-building"></i> City Transfers <i class="fas fa-angle-down"></i></a>
                                            <ul class="submenu submenu-2">
                                                @foreach ($cityLinks as $item)
                                                    <li><a href="{{ $baseUrl.'/'.ltrim($item['url'], '/') }}">{{ $item['label'] }}</a></li>
                                                @endforeach
                                                <li>
                                                    <a href="{{ $baseUrl.'/city-transfers' }}" class="theme-btn">View All</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a href="{{ $baseUrl.'/fleet' }}"><i class="fas fa-car"></i> Fleet</a></li>
                                        <li><a href="{{ $baseUrl.'/faq' }}"><i class="fas fa-message"></i> FAQ's</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="header-right d-flex justify-content-end align-items-center">
                        <div class="icon-items">
                            <div class="icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="content">
                                <p>{{ $phoneLabel }}</p>
                                <h6><a href="tel:{{ preg_replace('/\D+/', '', $phoneNumber) }}">{{ $phoneNumber }}</a></h6>
                            </div>
                        </div>
                        <div class="header-button">
                            <a href="{{ $baseUrl.'/'.ltrim($buttonLink, '/') }}" class="theme-btn">
                                {{ $buttonText }}
                            </a>
                        </div>
                        <div class="header__hamburger d-xl-none my-auto">
                            <div class="sidebar__toggle">
                                <i class="fas fa-bars"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>