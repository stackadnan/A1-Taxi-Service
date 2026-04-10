<!DOCTYPE html>
<html lang="en">
<?php $headTitle = 'A1 Airport Cars '; ?>
@include('partials.head')

<body>

    @include('partials.preloader')

    @include('partials.scroll-up')

    @include('partials.header-top')

    @include('partials.header')


    <!-- Search Area Start -->
    @include('partials.search-wrap')

    <!-- Hero Section Start -->
    <section class="hero-section hero-1 fix">
        <div class="array-button">
            <button class="image-array-left">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="image-array-right">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
        <div class="swiper hero-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="hero-image bg-cover" style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                        <div class="overlay-shape">
                            <img src="{{ \App\Support\GalleryPath::path('i/113') }}" alt="img">
                        </div>
                    </div>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-xl-12">
                                <div class="hero-content text-center">
                                    <h4 class="text-white" data-animation="fadeInUp" data-delay="1.3s">
                                        Book Any Luxury Car in low price
                                    </h4>
                                    <h1 class="text-white" data-animation="fadeInUp" data-delay="1.3s">
                                        Car <span>Rental</span>
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="hero-image bg-cover" style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                        <div class="overlay-shape">
                            <img src="{{ \App\Support\GalleryPath::path('i/113') }}" alt="img">
                        </div>
                    </div>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-xl-12">
                                <div class="hero-content text-center">
                                    <h4 class="text-white" data-animation="fadeInUp" data-delay="1.3s">
                                        Book Any Luxury Car in low price
                                    </h4>
                                    <h1 class="text-white" data-animation="fadeInUp" data-delay="1.3s">
                                        Car <span>Rental</span>
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pick Up Location Section Start -->
    <div class="pickup-loaction-area bg-cover" style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
        <div class="container">
            <div class="pickup-wrapper wow fadeInUp" data-wow-delay=".4s">
                <div class="pickup-items">
                    <label class="field-label">Pick-up Location</label>
                    <div class="category-oneadjust">
                        <select name="cate" class="category">
                            <option value="1">
                                Select Location
                            </option>
                            <option value="1">
                                Houston
                            </option>
                            <option value="1">
                                Texas
                            </option>
                            <option value="1">
                                New York
                            </option>
                            <option value="1">
                                Other Location
                            </option>
                        </select>
                    </div>
                </div>
                <div class="pickup-items">
                    <label class="field-label">Pickup Date</label>
                    <div id="datepicker" class="input-group date" data-date-format="dd-mm-yyyy">
                        <input class="form-control" type="text" placeholder="Check in" readonly>
                        <span class="input-group-addon"> <i class="fa-solid fa-calendar-days"></i></span>
                    </div>
                </div>
                <div class="pickup-items">
                    <label class="field-label">Dropoff Date</label>
                    <div id="datepicker2" class="input-group date" data-date-format="dd-mm-yyyy">
                        <input class="form-control" type="text" placeholder="Check in" readonly>
                        <span class="input-group-addon"> <i class="fa-solid fa-calendar-days"></i></span>
                    </div>
                </div>
                <div class="pickup-items">
                    <label class="field-label">Car Type</label>
                    <div class="category-oneadjust">
                        <select name="cate" class="category">
                            <option value="1">
                                cars
                            </option>
                            <option value="1">
                                sedan
                            </option>
                            <option value="1">
                                sports
                            </option>
                            <option value="1">
                                jeep
                            </option>
                            <option value="1">
                                limousine
                            </option>
                        </select>
                    </div>
                </div>
                <div class="pickup-items">
                    <label class="field-label style-2">button</label>
                    <button class="pickup-btn" type="submit">
                        Find a Car
                    </button>
                </div>
            </div>
            <div class="brand-wrapper pt-80 pb-80">
                <div class="array-button">
                    <button class="array-prev-2"><i class="far fa-chevron-left"></i></button>
                    <button class="array-next-2"><i class="far fa-chevron-right"></i></button>
                </div>
                <div class="swiper brand-slider">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="brand-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/15') }}" alt="img">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="brand-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/16') }}" alt="img">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="brand-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/17') }}" alt="img">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="brand-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/18') }}" alt="img">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="brand-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/19') }}" alt="img">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="brand-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/20') }}" alt="img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Benefit Section Start -->
    <section class="feature-benefit section section-padding fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">our benefits</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    Why You Should Use <br>
                    A1 Airport Cars  Rental
                </h2>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="feature-benefit-items">
                        <div class="icon-box-shape">
                            <img src="{{ \App\Support\GalleryPath::path('i/114') }}" alt="shape-img">
                        </div>
                        <div class="bg-button-shape">
                            <img src="{{ \App\Support\GalleryPath::path('i/115') }}" alt="shape-img">
                        </div>
                        <div class="feature-content">
                            <h4>
                                <a href="car-details">
                                    Easier & <br>
                                    Faster Bookings
                                </a>
                            </h4>
                            <p>Neque porro quisquam est, qui fre dolorem ipsum quia dolor.</p>
                            <div class="icon">
                                <img src="{{ \App\Support\GalleryPath::path('i/116') }}" alt="icon-img">
                            </div>
                        </div>
                        <div class="feature-button">
                            <a href="car-details" class="link-btn">View More <i
                                    class="fa-solid fa-arrow-right ps-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                    <div class="feature-benefit-items">
                        <div class="icon-box-shape">
                            <img src="{{ \App\Support\GalleryPath::path('i/117') }}" alt="shape-img">
                        </div>
                        <div class="bg-button-shape">
                            <img src="{{ \App\Support\GalleryPath::path('i/115') }}" alt="shape-img">
                        </div>
                        <div class="feature-content">
                            <h4>
                                <a href="car-details">
                                    Too Many <br>
                                    Pickup Locations
                                </a>
                            </h4>
                            <p>Neque porro quisquam est, qui fre dolorem ipsum quia dolor.</p>
                            <div class="icon">
                                <img src="{{ \App\Support\GalleryPath::path('i/118') }}" alt="icon-img">
                            </div>
                        </div>
                        <div class="feature-button">
                            <a href="car-details" class="link-btn">View More <i
                                    class="fa-solid fa-arrow-right ps-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
                    <div class="feature-benefit-items">
                        <div class="icon-box-shape">
                            <img src="{{ \App\Support\GalleryPath::path('i/119') }}" alt="shape-img">
                        </div>
                        <div class="bg-button-shape">
                            <img src="{{ \App\Support\GalleryPath::path('i/115') }}" alt="shape-img">
                        </div>
                        <div class="feature-content">
                            <h4>
                                <a href="car-details">
                                    Customers <br>
                                    100% Satisfied
                                </a>
                            </h4>
                            <p>Neque porro quisquam est, qui fre dolorem ipsum quia dolor.</p>
                            <div class="icon">
                                <img src="{{ \App\Support\GalleryPath::path('i/120') }}" alt="icon-img">
                            </div>
                        </div>
                        <div class="feature-button">
                            <a href="car-details" class="link-btn">View More <i
                                    class="fa-solid fa-arrow-right ps-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section Start -->
    <section class="about-section fix section-padding pt-0">
        <div class="container">
            <div class="about-wrapper">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="about-image-items">
                            <div class="color-shape">
                                <img src="{{ \App\Support\GalleryPath::path('i/121') }}" alt="shape-img">
                            </div>
                            <div class="car-shape wow fadeInUp" data-wow-delay=".7s">
                                <img src="{{ \App\Support\GalleryPath::path('i/122') }}" alt="shape-img">
                            </div>
                            <div class="counter-content wow fadeInLeft" data-wow-delay=".4s">
                                <h2 class="text-white"><span class="count">50</span></h2>
                                <p class="text-white">
                                    Years of <br>
                                    Experience
                                </p>
                            </div>
                            <div class="about-image-1 wow fadeInDown" data-wow-delay=".3s">
                                <img src="{{ \App\Support\GalleryPath::path('i/123') }}" alt="about-image">
                            </div>
                            <div class="about-image-2 wow fadeInLeft" data-wow-delay=".5s">
                                <img src="{{ \App\Support\GalleryPath::path('i/124') }}" alt="about-image">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content">
                            <div class="section-title">
                                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                                <span class="wow fadeInUp" data-wow-delay=".2s">Get to know us</span>
                                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                                    Services with a Wide
                                    Range of Cars
                                </h2>
                            </div>
                            <h4 class="mt-3 mt-md-0 wow fadeInUp" data-wow-delay=".3s">
                                committed to providing our customers with exceptional service.
                            </h4>
                            <p class="wow fadeInUp" data-wow-delay=".5s">
                                Lorem ipsum is simply ipun txns mane so dummy text of free available in market the
                                printing and typesetting industry has been the industry's standard dummy text ever.
                            </p>
                            <div class="about-list-item wow fadeInUp" data-wow-delay=".7s">
                                <ul>
                                    <li>
                                        Many Pickup Locations
                                    </li>
                                    <li>
                                        Offering Low Prices
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        Many Pickup Locations
                                    </li>
                                    <li>
                                        Offering Low Prices
                                    </li>
                                </ul>
                            </div>
                            <a href="about" class="theme-btn wow fadeInUp" data-wow-delay=".8s">Discover More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Car Rentals Section Start -->
    <section class="car-rentals-section section-padding fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">Checkout our new cars</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    Cars We’re Offering <br>
                    for Rentals
                </h2>
            </div>
        </div>
        <div class="car-rentals-wrapper">
            <div class="array-button">
                <button class="array-prev"><i class="far fa-chevron-left"></i></button>
                <button class="array-next"><i class="far fa-chevron-right"></i></button>
            </div>
            <div class="swiper car-rentals-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="car-rentals-items">
                            <div class="car-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/87') }}" alt="img">
                            </div>
                            <div class="car-content">
                                <div class="post-cat">
                                    2024 Model
                                </div>
                                <div class="star">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <span>2 Reviews</span>
                                </div>
                                <h4><a href="car-details">Hyundai Accent Limited</a></h4>
                                <h6>$70.00 <span>/ Day</span></h6>
                                <div class="icon-items">
                                    <ul>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/30') }}" alt="img" class="me-1">
                                            Automatic
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/31') }}" alt="img" class="me-1">
                                            Petrol
                                        </li>
                                    </ul>
                                </div>
                                <a href="car-details" class="theme-btn bg-color w-100 text-center">book now <i
                                        class="fa-solid fa-arrow-right ps-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="car-rentals-items">
                            <div class="car-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/88') }}" alt="img">
                            </div>
                            <div class="car-content">
                                <div class="post-cat">
                                    2024 Model
                                </div>
                                <div class="star">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <span>2 Reviews</span>
                                </div>
                                <h4><a href="car-details">Hyundai Accent Limited</a></h4>
                                <h6>$70.00 <span>/ Day</span></h6>
                                <div class="icon-items">
                                    <ul>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/30') }}" alt="img" class="me-1">
                                            Automatic
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/31') }}" alt="img" class="me-1">
                                            Petrol
                                        </li>
                                    </ul>
                                </div>
                                <a href="car-details" class="theme-btn bg-color w-100 text-center">book now <i
                                        class="fa-solid fa-arrow-right ps-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="car-rentals-items">
                            <div class="car-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/89') }}" alt="img">
                            </div>
                            <div class="car-content">
                                <div class="post-cat">
                                    2024 Model
                                </div>
                                <div class="star">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <span>2 Reviews</span>
                                </div>
                                <h4><a href="car-details">Hyundai Accent Limited</a></h4>
                                <h6>$70.00 <span>/ Day</span></h6>
                                <div class="icon-items">
                                    <ul>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/30') }}" alt="img" class="me-1">
                                            Automatic
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/31') }}" alt="img" class="me-1">
                                            Petrol
                                        </li>
                                    </ul>
                                </div>
                                <a href="car-details" class="theme-btn bg-color w-100 text-center">book now <i
                                        class="fa-solid fa-arrow-right ps-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="car-rentals-items">
                            <div class="car-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/125') }}" alt="img">
                            </div>
                            <div class="car-content">
                                <div class="post-cat">
                                    2024 Model
                                </div>
                                <div class="star">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <span>2 Reviews</span>
                                </div>
                                <h4><a href="car-details">Hyundai Accent Limited</a></h4>
                                <h6>$70.00 <span>/ Day</span></h6>
                                <div class="icon-items">
                                    <ul>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/30') }}" alt="img" class="me-1">
                                            Automatic
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/31') }}" alt="img" class="me-1">
                                            Petrol
                                        </li>
                                    </ul>
                                </div>
                                <a href="car-details" class="theme-btn bg-color w-100 text-center">book now <i
                                        class="fa-solid fa-arrow-right ps-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Car Service Section Start -->
    <section class="car-service-section">
        <div class="container">
            <div class="car-service-wrapper">
                <div class="shape-image">
                    <img src="{{ \App\Support\GalleryPath::path('i/126') }}" alt="shape-img">
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="service-car-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/127') }}" alt="img">
                            <div class="color-shape">
                                <img src="{{ \App\Support\GalleryPath::path('i/128') }}" alt="shape-img">
                            </div>
                            <div class="booking-content">
                                <p>Call for booking</p>
                                <h3><a href="tel:9288009850">+92 (8800) - 9850</a></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="service-car-content">
                            <h2 class="wow fadeInUp" data-wow-delay=".3s">
                                Looking for a Luxury <br>
                                Car Service?
                            </h2>
                            <h3 class="wow fadeInUp" data-wow-delay=".5s">
                                <span>Starting at</span> <sup>$</sup> 398 <span class="text">/mo</span>
                            </h3>
                            <a href="./" class="theme-btn wow fadeInUp" data-wow-delay=".7s">Discover More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Funfact Section Start -->
    <section class="funfact-section section-padding bg-cover"
        style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
        <div class="container">
            <div class="funfact-wrapper">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="section-title mb-0">
                            <img src="{{ \App\Support\GalleryPath::path('i/7') }}" alt="icon-img" class="wow fadeInUp">
                            <span class="wow fadeInUp" data-wow-delay=".2s">fun facts</span>
                            <h2 class="text-white wow fadeInUp" data-wow-delay=".4s">
                                Save Time & Money <br>
                                with Top Car Rent <br>
                                Services
                            </h2>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="funfact-counter-area">
                            <div class="funfact-items wow fadeInUp" data-wow-delay=".3s">
                                <div class="icon">
                                    <img src="{{ \App\Support\GalleryPath::path('i/8') }}" alt="img">
                                </div>
                                <h2><span class="count">990</span></h2>
                                <p>
                                    Cars <br>
                                    rentouts
                                </p>
                            </div>
                            <div class="funfact-items wow fadeInUp" data-wow-delay=".5s">
                                <div class="icon">
                                    <img src="{{ \App\Support\GalleryPath::path('i/9') }}" alt="img">
                                </div>
                                <h2><span class="count">230</span></h2>
                                <p>
                                    Center <br>
                                    solutions
                                </p>
                            </div>
                            <div class="funfact-items wow fadeInUp" data-wow-delay=".7s">
                                <div class="icon">
                                    <img src="{{ \App\Support\GalleryPath::path('i/10') }}" alt="img">
                                </div>
                                <h2><span class="count">660</span></h2>
                                <p>
                                    happy <br>
                                    customers
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Car Section Start -->
    <section class="popular-car-section fix section-padding">
        <div class="container">
            <div class="section-title text-center">
                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">select car types</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    We’re Offering Popular <br>
                    Cars Models
                </h2>
            </div>
            <div class="row g-4 mt-30">
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="popular-card-items">
                        <div class="content">
                            <h4><a href="car-details">Sedan</a></h4>
                            <p>Available for Rent</p>
                        </div>
                        <div class="car-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/75') }}" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                    <div class="popular-card-items">
                        <div class="content">
                            <h4><a href="car-details">Sports</a></h4>
                            <p>Available for Rent</p>
                        </div>
                        <div class="car-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/77') }}" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
                    <div class="popular-card-items">
                        <div class="content">
                            <h4><a href="car-details">Jeep</a></h4>
                            <p>Available for Rent</p>
                        </div>
                        <div class="car-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/79') }}" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="popular-card-items">
                        <div class="content">
                            <h4><a href="car-details">SUV</a></h4>
                            <p>Available for Rent</p>
                        </div>
                        <div class="car-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/81') }}" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                    <div class="popular-card-items">
                        <div class="content">
                            <h4><a href="car-details">Luxury</a></h4>
                            <p>Available for Rent</p>
                        </div>
                        <div class="car-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/83') }}" alt="img">
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
                    <div class="popular-card-items">
                        <div class="content">
                            <h4><a href="car-details">Luxury</a></h4>
                            <p>Available for Rent</p>
                        </div>
                        <div class="car-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/85') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
            <div class="popular-car-text wow fadeInUp" data-wow-delay=".4s">
                <h6>Car rental services specifically for our customers.</h6>
                <a href="car-details" class="theme-btn">Find a car</a>
            </div>
        </div>
    </section>

    <!-- Testimonial Section Start -->
    <section class="testimonial-section fix section-padding">
        <div class="testimonial-bg-shape">
            <img src="{{ \App\Support\GalleryPath::path('i/11') }}" alt="shape-img">
        </div>
        <div class="container">
            <div class="section-title-area">
                <div class="section-title">
                    <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                    <span class="wow fadeInUp" data-wow-delay=".2s">our testimonials</span>
                    <h2 class="wow fadeInUp" data-wow-delay=".4s">
                        What They’re Talking <br>
                        About A1 Airport Cars 
                    </h2>
                </div>
                <p class="wow fadeInUp" data-wow-delay=".5s">
                    Lorem ipsum dolor sit amet nsectetur cing elituspe ndisse suscipit <br> sagitis leo sit.
                </p>
            </div>
            <div class="swiper testimonial-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="testimonial-card-items">
                            <div class="testimoni-bg-shape">
                                <div class="testimonial-items-top">
                                    <div class="icon">
                                        <i class="fa-solid fa-quote-left"></i>
                                    </div>
                                    <p>
                                        I was very impresed by the A1 Airport Cars  service lorem ipsum is simply free text used
                                        by copy typing refreshing. Neque porro est qui dolorem ipsum quia.
                                    </p>
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="client-info-items d-flex align-items-center gap-3">
                                <div class="client-img bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');"></div>
                                <div class="content">
                                    <h5>
                                        Jessica Brown
                                    </h5>
                                    <span>Customer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-card-items">
                            <div class="testimoni-bg-shape">
                                <div class="testimonial-items-top">
                                    <div class="icon">
                                        <i class="fa-solid fa-quote-left"></i>
                                    </div>
                                    <p>
                                        I was very impresed by the A1 Airport Cars  service lorem ipsum is simply free text used
                                        by copy typing refreshing. Neque porro est qui dolorem ipsum quia.
                                    </p>
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="client-info-items d-flex align-items-center gap-3">
                                <div class="client-img bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');"></div>
                                <div class="content">
                                    <h5>
                                        Kevin Martin
                                    </h5>
                                    <span>Customer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-card-items">
                            <div class="testimoni-bg-shape">
                                <div class="testimonial-items-top">
                                    <div class="icon">
                                        <i class="fa-solid fa-quote-left"></i>
                                    </div>
                                    <p>
                                        I was very impresed by the A1 Airport Cars  service lorem ipsum is simply free text used
                                        by copy typing refreshing. Neque porro est qui dolorem ipsum quia.
                                    </p>
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="client-info-items d-flex align-items-center gap-3">
                                <div class="client-img bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');"></div>
                                <div class="content">
                                    <h5>
                                        Jessica Brown
                                    </h5>
                                    <span>Customer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section Start -->
    <div class="gallery-section fix">
        <div class="gallery-wrapper">
            <div class="row g-4">
                <div class="col-xxl-4 col-xl-5 col-lg-5">
                    <div class="row g-4">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="gallery-image">
                                        <img src="{{ \App\Support\GalleryPath::path('i/129') }}" alt="img">
                                        <div class="icon-box">
                                            <a href="{{ \App\Support\GalleryPath::path('i/129') }}" class="icon img-popup-2">
                                                <i class="fa-solid fa-plus"></i>
                                            </a>
                                        </div>
                                        <div class="mask"></div>
                                        <div class="mask-second"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="gallery-image">
                                        <img src="{{ \App\Support\GalleryPath::path('i/130') }}" alt="img">
                                        <div class="icon-box">
                                            <a href="{{ \App\Support\GalleryPath::path('i/130') }}" class="icon img-popup-2">
                                                <i class="fa-solid fa-plus"></i>
                                            </a>
                                        </div>
                                        <div class="mask"></div>
                                        <div class="mask-second"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="gallery-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/131') }}" alt="img">
                                <div class="icon-box">
                                    <a href="{{ \App\Support\GalleryPath::path('i/131') }}" class="icon img-popup-2 style-two">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                </div>
                                <div class="mask"></div>
                                <div class="mask-second"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-xl-7 col-lg-7">
                    <div class="row g-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="gallery-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/132') }}" alt="img">
                                <div class="icon-box">
                                    <a href="{{ \App\Support\GalleryPath::path('i/132') }}" class="icon img-popup-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                </div>
                                <div class="mask"></div>
                                <div class="mask-second"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="gallery-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/133') }}" alt="img">
                                <div class="icon-box">
                                    <a href="{{ \App\Support\GalleryPath::path('i/133') }}" class="icon img-popup-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                </div>
                                <div class="mask"></div>
                                <div class="mask-second"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="gallery-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/134') }}" alt="img">
                                <div class="icon-box">
                                    <a href="{{ \App\Support\GalleryPath::path('i/134') }}" class="icon img-popup-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                </div>
                                <div class="mask"></div>
                                <div class="mask-second"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="gallery-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/135') }}" alt="img">
                                <div class="icon-box">
                                    <a href="{{ \App\Support\GalleryPath::path('i/135') }}" class="icon img-popup-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                </div>
                                <div class="mask"></div>
                                <div class="mask-second"></div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="gallery-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/136') }}" alt="img">
                                <div class="icon-box">
                                    <a href="{{ \App\Support\GalleryPath::path('i/136') }}" class="icon img-popup-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                </div>
                                <div class="mask"></div>
                                <div class="mask-second"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-xl-4 col-lg-4 col-md-6 col-sm-6">
                    <div class="gallery-image style-2">
                        <img src="{{ \App\Support\GalleryPath::path('i/137') }}" alt="img">
                        <div class="icon-box">
                            <a href="{{ \App\Support\GalleryPath::path('i/137') }}" class="icon img-popup-2 style-two">
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

    <!-- News Section Start -->
    <section class="news-section section-padding fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">From the Blog</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    Latest News & <br>
                    Articles From the Blog
                </h2>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="news-card-items">
                        <div class="news-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/62') }}" alt="news-img">
                            <div class="post-date">
                                <h6>
                                    20 <br>
                                    Mar
                                </h6>
                            </div>
                        </div>
                        <div class="news-content">
                            <div class="post-client">
                                <img src="{{ \App\Support\GalleryPath::path('i/63') }}" alt="img">
                            </div>
                            <div class="news-cont">
                                <span>by Mike Hardson</span>
                                <h3><a href="news-details">The best fastest and most powerful road car</a></h3>
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem…</p>
                            </div>
                            <ul>
                                <li>
                                    <i class="fa-solid fa-comments"></i>
                                    2 Comments
                                </li>
                                <li>
                                    <a href="news-details">
                                        <i class="fa-solid fa-arrow-right-long"></i>
                                        More
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                    <div class="news-card-items">
                        <div class="news-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/64') }}" alt="news-img">
                            <div class="post-date">
                                <h6>
                                    26 <br>
                                    Mar
                                </h6>
                            </div>
                        </div>
                        <div class="news-content">
                            <div class="post-client">
                                <img src="{{ \App\Support\GalleryPath::path('i/63') }}" alt="img">
                            </div>
                            <div class="news-cont">
                                <span>by Mike Hardson</span>
                                <h3><a href="news-details">The best fastest and most powerful road car</a></h3>
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem…</p>
                            </div>
                            <ul>
                                <li>
                                    <i class="fa-solid fa-comments"></i>
                                    2 Comments
                                </li>
                                <li>
                                    <a href="news-details">
                                        <i class="fa-solid fa-arrow-right-long"></i>
                                        More
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
                    <div class="news-card-items">
                        <div class="news-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/65') }}" alt="news-img">
                            <div class="post-date">
                                <h6>
                                    29 <br>
                                    Mar
                                </h6>
                            </div>
                        </div>
                        <div class="news-content">
                            <div class="post-client">
                                <img src="{{ \App\Support\GalleryPath::path('i/63') }}" alt="img">
                            </div>
                            <div class="news-cont">
                                <span>by Mike Hardson</span>
                                <h3><a href="news-details">The best fastest and most powerful road car</a></h3>
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem…</p>
                            </div>
                            <ul>
                                <li>
                                    <i class="fa-solid fa-comments"></i>
                                    2 Comments
                                </li>
                                <li>
                                    <a href="news-details">
                                        <i class="fa-solid fa-arrow-right-long"></i>
                                        More
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cta Rental Section Start -->
    <section class="cta-rental-section bg-cover fix section-padding"
        style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
        <div class="container">
            <div class="row g-4 justify-content-between align-items-center">
                <div class="col-lg-6">
                    <div class="cta-rental-items">
                        <h4 class="wow fadeInUp" data-wow-delay=".3s">Faster, easier access to car rental services</h4>
                        <h2 class="wow fadeInUp" data-wow-delay=".5s">Premium Car Rental</h2>
                        <div class="rental-app-button">
                            <a href="./" class="app-button-items wow fadeInUp" data-wow-delay=".7s">
                                <span class="button-icon"><i class="fa-solid fa-play"></i></span>
                                <span class="button-text">
                                    <span class="text">Get in</span> <br>
                                    <span class="headding-text">Google Play</span>
                                </span>
                            </a>
                            <a href="./" class="app-button-items style-2 wow fadeInUp" data-wow-delay=".8s">
                                <span class="button-icon"><i class="fa-brands fa-apple"></i></span>
                                <span class="button-text">
                                    <span class="text">Get in</span> <br>
                                    <span class="headding-text">Play Store</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 wow fadeInUp" data-wow-delay=".4s">
                    <div class="mobile-A1 Airport Cars -image">
                        <img src="{{ \App\Support\GalleryPath::path('i/138') }}" alt="img">
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    @include('partials.footer')

    @include('partials.script')
</body>

</html>


