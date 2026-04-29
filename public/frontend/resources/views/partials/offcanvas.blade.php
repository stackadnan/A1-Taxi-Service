<!-- Offcanvas Area Start -->
    <div class="fix-area">
        <div class="offcanvas__info">
            <div class="offcanvas__wrapper">
                <div class="offcanvas__content">
                    <div class="offcanvas__top mb-5 d-flex justify-content-between align-items-center">
                        <div class="offcanvas__logo">
                            <a href="./">
                                <img src="{{ \App\Support\GalleryPath::path($logo ?? 'i/154') }}" alt="logo-img">
                            </a>
                        </div>
                        <div class="offcanvas__close">
                            <button>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- <p class="text d-none d-xl-block">
                        Nullam dignissim, ante scelerisque the is euismod fermentum odio sem semper the is erat, a
                        feugiat leo urna eget eros. Duis Aenean a imperdiet risus.
                    </p> -->
                    <div class="mobile-menu fix mb-3"></div>
                    <div class="offcanvas__contact">
                        <h4>Contact Info</h4>
                        <ul>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon">
                                    <i class="fal fa-map-marker-alt"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a target="_blank" href="#">{{ $address ?? '960 Capability Green, LU1 3PE Luton' }}</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="fal fa-envelope"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="mailto:{{ $email ?? 'info@a1airportcars.co.uk' }}"><span>{{ $email ?? 'info@a1airportcars.co.uk' }}</span></a>
                                </div>
                            </li>
                            
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="far fa-phone"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="tel:{{ $phone ?? '(+44) - 1582 - 801 - 611' }}">{{ $phone ?? '(+44) - 1582 - 801 - 611' }}</a>
                                </div>
                            </li>
                        </ul>
                        <div class="header-button mt-4">
                            <a href="manage-booking" class="theme-btn text-center">
                                <span>{{ $buttonText ?? 'Manage My Booking' }}<i class="fa-solid fa-arrow-right-long"></i></span>
                            </a>
                        </div>
                        <div class="social-icon d-flex align-items-center">
                            @foreach($socialLinks ?? [] as $social)
                                <a href="{{ $social['link'] ?? '#' }}"><i class="{{ $social['icon'] ?? 'fab fa-facebook-f' }}"></i></a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas__overlay"></div>

