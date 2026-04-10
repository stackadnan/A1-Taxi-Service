 @php($baseUrl = rtrim(request()->getBaseUrl(), '/'))
 <section class="cta-cheap-rental-section">
        <div class="container">
            <div class="cta-cheap-rental">
                <div class="cta-cheap-rental-left wow fadeInUp" data-wow-delay="
                    .3s">
                    <div class="logo-thumb">
                        <a href="./">
                            <img src="{{ \App\Support\GalleryPath::path($footerLogo ?? 'i/152') }}" alt="logo-img">
                        </a>
                    </div>
                    <h4 class="text-white">{{ $footerTagline ?? 'Your go to option for reliable Airport Transfers' }}</h4>
                </div>
                <div class="social-icon d-flex align-items-center wow fadeInUp" data-wow-delay="
                    .5s">
                    @foreach($socialLinks as $social)
                        <a href="{{ $social['link'] ?? '#' }}"><i class="{{ $social['icon'] ?? 'fab fa-facebook-f' }}"></i></a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

<!-- Footer Section Start -->
<footer class="footer-section fix">
    <div class="container">
        <div class="footer-widgets-wrapper">
            <div class="row justify-content-between">
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                    <div class="single-footer-widget shape-map">
                        <div class="widget-head">
                            <h4>Contact</h4>
                        </div>
                        <div class="footer-content">
                            <p>{{ $contactAddress ?? '960 Capability Green, LU1 3PE Luton, United Kingdom' }}</p>
                            <ul class="contact-info">
                                <li>
                                    <i class="fa-regular fa-envelope"></i>
                                    <a href="mailto:{{ $contactEmail ?? 'info@a1airportcars.co.uk' }}">{{ $contactEmail ?? 'info@a1airportcars.co.uk' }}</a>
                                </li>
                                <li>
                                    <i class="fa-solid fa-phone-volume"></i>
                                    <a href="tel:{{ $contactPhone ?? '(+44) - 1582 - 801 - 611' }}">{{ $contactPhone ?? '(+44) - 1582 - 801 - 611' }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                    <div class="single-footer-widget">
                        <div class="widget-head">
                            <h4>Links</h4>
                        </div>
                        <ul class="list-items">
                            @foreach($links as $link)
                                <li>
                                    <a href="{{ $baseUrl.'/'.ltrim($link['url'], '/') }}">{{ $link['label'] }}</a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ $baseUrl.'/complainet/lost-found' }}">Complainet / Lost Found</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                    <div class="single-footer-widget">
                        <div class="widget-head">
                            <h4>Airports</h4>
                        </div>
                        <ul class="list-items">
                            @foreach($airports as $airport)
                                <li>
                                    <a href="{{ isset($airport['url']) ? $baseUrl.'/'.ltrim($airport['url'], '/') : '#' }}">{{ $airport['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".8s">
                    <div class="single-footer-widget">
                        <div class="widget-head">
                            <h4>Cities Covered</h4>
                        </div>
                        <ul class="list-items list-itemscol2">
                            @foreach($cities as $city)
                                <li><a href="{{ $baseUrl.'/about' }}">{{ $city }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-wrapper">
                <p class="wow fadeInUp" data-wow-delay=".4s">
                    {!! $copyright ?? '&copy; Copyright '.date('Y').' A1 Airport Cars | Powered by <a href="./">BXS</a>' !!}
                </p>
            </div>
        </div>
    </div>
</footer>

