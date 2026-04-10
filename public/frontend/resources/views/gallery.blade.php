<?php
$headTitle = 'A1 Airport Cars ';
$img = \App\Support\GalleryPath::path('i/149');
$Title = 'Home';
$Title2 = 'Gallery';
$SubTitle = 'Gallery';
?>

@include('partials.layouts.layoutsTop')



<!--<< Gallery Section Start >>-->
<div class="gallery-section-3 fix section-padding">
    <div class="container">
        <div class="galley-wrapper-2">
            <div class="gallery-items">
                <div class="g-items">
                    <img src="{{ \App\Support\GalleryPath::path('i/51') }}" alt="ga-img">
                    <div class="icon-box">
                        <a href="{{ \App\Support\GalleryPath::path('i/51') }}" class="icon img-popup-2">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                    <div class="mask"></div>
                    <div class="mask-second"></div>
                </div>
                <div class="g-items">
                    <img src="{{ \App\Support\GalleryPath::path('i/52') }}" alt="ga-img">
                    <div class="icon-box">
                        <a href="{{ \App\Support\GalleryPath::path('i/52') }}" class="icon img-popup-2">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                    <div class="mask"></div>
                    <div class="mask-second"></div>
                </div>
                <div class="g-items">
                    <img src="{{ \App\Support\GalleryPath::path('i/53') }}" alt="ga-img">
                    <div class="icon-box">
                        <a href="{{ \App\Support\GalleryPath::path('i/53') }}" class="icon img-popup-2">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                    <div class="mask"></div>
                    <div class="mask-second"></div>
                </div>
            </div>
            <div class="gallery-items gallery-items-two">
                <div class="g-items">
                    <img src="{{ \App\Support\GalleryPath::path('i/54') }}" alt="ga-img">
                    <div class="icon-box">
                        <a href="{{ \App\Support\GalleryPath::path('i/54') }}" class="icon img-popup-2">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                    <div class="mask"></div>
                    <div class="mask-second"></div>
                </div>
                <div class="g-items">
                    <img src="{{ \App\Support\GalleryPath::path('i/55') }}" alt="ga-img">
                    <div class="icon-box">
                        <a href="{{ \App\Support\GalleryPath::path('i/55') }}" class="icon img-popup-2">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                    <div class="mask"></div>
                    <div class="mask-second"></div>
                </div>
            </div>
            <div class="gallery-items gallery-items-three">
                <div class="g-items">
                    <img src="{{ \App\Support\GalleryPath::path('i/56') }}" alt="ga-img">
                    <div class="icon-box">
                        <a href="{{ \App\Support\GalleryPath::path('i/56') }}" class="icon img-popup-2">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                    <div class="mask"></div>
                    <div class="mask-second"></div>
                </div>
                <div class="g-items">
                    <img src="{{ \App\Support\GalleryPath::path('i/57') }}" alt="ga-img">
                    <div class="icon-box">
                        <a href="{{ \App\Support\GalleryPath::path('i/57') }}" class="icon img-popup-2">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                    <div class="mask"></div>
                    <div class="mask-second"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Cta Cheap Rental Section Start -->
<section class="cta-cheap-rental-section">
    <div class="container">
        <div class="cta-cheap-rental">
            <div class="cta-cheap-rental-left wow fadeInUp" data-wow-delay="
                    .3s">
                <div class="logo-thumb">
                    <a href="./">
                        <img src="{{ \App\Support\GalleryPath::path('i/21') }}" alt="logo-img">
                    </a>
                </div>
                <h4 class="text-white">Save big with our cheap car rental</h4>
            </div>
            <div class="social-icon d-flex align-items-center wow fadeInUp" data-wow-delay="
                    .5s">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>
    </div>
</section>

@include('partials.layouts.layoutsBottom')


