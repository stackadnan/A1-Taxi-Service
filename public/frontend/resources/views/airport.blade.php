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
                @if(!empty($airportContentHtml))
                    {!! $airportContentHtml !!}
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="about-content pt-4">
                                <div class="section-title-content">
                                    <h3 class="wow fadeInUp" data-wow-delay=".4s">
                                        Content Not Configured
                                    </h3>
                                </div>
                                <p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">
                                    Add HTML in one_column, two_column, three_column and set number_of_rows like 1,2,3 or 1,2,1.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

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
