<!DOCTYPE html>
<html lang="en">
<?php $headTitle = $airportHeadTitle ?? 'A1 Airport Cars '; ?>
@php
    $enabledPartials = $enabledPartials ?? [
        'head',
        'preloader',
        'scroll-up',
        'offcanvas',
        'header',
        'breadcrumb',
        'quotes',
        'testimonials',
        'why-us',
        'card-fleet',
        'steps',
        'card-blog',
        'faq',
        'footer',
        'script',
    ];
@endphp
@if(in_array('head', $enabledPartials, true))
@include('partials.head')
@endif


<body>

    <!-- Preloader Start -->
    @if(in_array('preloader', $enabledPartials, true))
    @include('partials.preloader')
    @endif

    <!-- Back To Top Start -->
    @if(in_array('scroll-up', $enabledPartials, true))
    @include('partials.scroll-up')
    @endif

    <!-- Offcanvas Area Start -->
    @if(in_array('offcanvas', $enabledPartials, true))
    @include('partials.offcanvas')
    @endif

    <!-- Header Section Start -->
    @if(in_array('header', $enabledPartials, true))
    @include('partials.header')
    @endif

    @if(in_array('breadcrumb', $enabledPartials, true))
    @include('partials.breadcrumb')
    @endif
    <!-- Search Area Start -->

    <!-- Hero Section Start -->
    @if(in_array('quotes', $enabledPartials, true))
    @include('partials.quotes')
    @endif

    <!-- testimonials Section Start -->
    @if(in_array('testimonials', $enabledPartials, true))
    @include('partials.testimonials')
    @endif

    <!-- Why us Start -->
    @if(in_array('why-us', $enabledPartials, true))
    @include('partials.why-us')
    @endif

    <!-- content Section Start -->
    <section class="about-section fix section-padding">
        <div class="container">
            <div class="about-wrapper-2">

                <!-- First Full Width -->
                <div class="row">
                    <div class="col-12">
                        <div class="about-content pt-4">
                            <div class="section-title-content">
                                <h3 class="wow fadeInUp" data-wow-delay=".4s">
                                    {{ $airportMainTitle }}
                                </h3>
                            </div>
                            <p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">
                                {!! $airportMainDescription !!}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Middle Two Columns -->
                <div class="row g-4">

                    <div class="col-md-6">
                        <div class="about-content pt-4">
                            <div class="section-title-content">
                                <h4 class="wow fadeInUp" data-wow-delay=".4s">
                                    {{ $airportLeftTitle }}
                                </h4>
                            </div>
                            <p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">
                                {!! $airportLeftDescription !!}
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="about-content pt-4">
                            <div class="section-title-content">
                                <h4 class="wow fadeInUp" data-wow-delay=".4s">
                                    {{ $airportRightTitle }}
                                </h4>
                            </div>
                            <p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">
                                {!! $airportRightDescription !!}
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Last Full Width -->
                <div class="row">
                    <div class="col-12">
                        <div class="about-content pt-4">
                            <div class="section-title-content">
                                <h4 class="wow fadeInUp" data-wow-delay=".4s">
                                    {{ $airportBottomTitle }}
                                </h4>
                            </div>
                            <p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">
                                {!! $airportBottomDescription !!}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- fleet Section Start -->
    @if(in_array('card-fleet', $enabledPartials, true))
    @include('partials.card-fleet')
    @endif

    <!-- Gallery Section Start -->
    @if(in_array('steps', $enabledPartials, true))
    @include('partials.steps')
    @endif

    <!-- News Section Start -->
    @if(in_array('card-blog', $enabledPartials, true))
    @include('partials.card-blog')
    @endif

    <!-- Faq Section Start -->
    @if(in_array('faq', $enabledPartials, true))
    @include('partials.faq')
    @endif

    <!-- footer cta Section Start -->
    @if(in_array('footer', $enabledPartials, true))
    @include('partials.footer')
    @endif

    @if(in_array('script', $enabledPartials, true))
    @include('partials.script')
    @endif
</body>

</html>
