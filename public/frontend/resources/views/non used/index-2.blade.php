<!DOCTYPE html>
<html lang="en">
<?php $headTitle = 'A1 Airport Cars '; ?>
@include('partials.head')

<body>

    <!-- Preloader Start -->
    @include('partials.preloader')

    <!-- Back To Top Start -->
    @include('partials.scroll-up')

    <!-- Offcanvas Area Start -->
    @include('partials.offcanvas')

    <!-- Header Section Start -->
    <header id="header-sticky" class="header-2">
        <div class="container-fluid">
            <div class="mega-menu-wrapper">
                <div class="header-main">
                    <div class="header-left">
                        <div class="logo">
                            <a href="./" class="header-logo">
                                <img src="{{ \App\Support\GalleryPath::path('i/69') }}" alt="logo-img">
                            </a>
                        </div>
                        <a href="#0" class="search-trigger search-icon"><i
                                class="fa-regular fa-magnifying-glass"></i></a>
                    </div>
                    <div class="mean__menu-wrapper">
                        <div class="main-menu">
                            <nav id="mobile-menu">
                                <ul>
                                    <li class="has-dropdown active menu-thumb">
                                        <a href="./">
                                            Home
                                            <i class="fas fa-angle-down"></i>
                                        </a>
                                        <ul class="submenu">
                                            <li><a href="./">Home 01</a></li>
                                            <li><a href="index-2">Home 02</a></li>
                                            <li><a href="./">Home 03</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="about">About Us</a>
                                    </li>
                                    <li class="has-dropdown">
                                        <a href="news">
                                            Pages
                                            <i class="fas fa-angle-down"></i>
                                        </a>
                                        <ul class="submenu">
                                            <li><a href="gallery">Gallery</a></li>
                                            <li><a href="faq">Faq's</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="car-details">
                                            Cars
                                            <i class="fas fa-angle-down"></i>
                                        </a>
                                        <ul class="submenu">
                                            <li><a href="car-grid">Car Grid</a></li>
                                            <li><a href="car-list-sidebar">Car List</a></li>
                                            <li><a href="car-list-sidebar">Car Sidebar</a></li>
                                            <li><a href="car-details">Car Details</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="news">
                                            Blog
                                            <i class="fas fa-angle-down"></i>
                                        </a>
                                        <ul class="submenu">
                                            <li><a href="news">Blog</a></li>
                                            <li><a href="news-details">Blog Details</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="contact">Contact</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="header-right d-flex justify-content-end align-items-center">
                        <div class="icon-items">
                            <div class="icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="content">
                                <p>Call Anytime</p>
                                <h6><a href="tel:+9288009850">+92 (8800) - 9850</a></h6>
                            </div>
                        </div>
                        <div class="header-button">
                            <a href="car-details" class="theme-btn">
                                Find a Car
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
    </header>

    <!-- Search Area Start -->
    @include('partials.search-wrap')

    <!-- Hero Section Start -->
    <section class="hero-section-2 bg-cover fix" style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
        <div class="radius-shape">
            <img src="{{ \App\Support\GalleryPath::path('i/70') }}" alt="shape-img">
        </div>
        <div class="shadow-shape">
            <img src="{{ \App\Support\GalleryPath::path('i/71') }}" alt="img">
        </div>
        <div class="array-button">
            <button class="image-array-left bg-cover" style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="image-array-right bg-cover" style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
        <div class="swiper hero-slider-2">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="hero-2">
                        <div class="top-image" data-animation="fadeInDown" data-delay="1.5s">
                            <img src="{{ \App\Support\GalleryPath::path('i/72') }}" alt="img">
                        </div>
                        <div class="circle-shape" data-animation="fadeInDown" data-delay="1.5s">
                            <img src="{{ \App\Support\GalleryPath::path('i/73') }}" alt="img">
                        </div>
                        <div class="container">
                            <div class="row g-4 align-items-center">
                                <div class="col-xl-7 col-lg-6">
                                    <div class="hero-content">
                                        <h6 data-animation="fadeInUp" data-delay=".3s" class="hero-title">Welcome to
                                            A1 Airport Cars  Rental</h6>
                                        <h1 data-animation="fadeInUp" data-delay=".5s">
                                            Search, Book <br>
                                            & Rent Car
                                            <span>Easily</span>
                                        </h1>
                                    </div>
                                </div>
                                <div class="col-xl-5 col-lg-6">
                                    <div class="hero-image" data-animation="fadeInUp" data-delay=".7s">
                                        <img src="{{ \App\Support\GalleryPath::path('i/74') }}" alt="img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="hero-2">
                        <div class="top-image" data-animation="fadeInDown" data-delay="1.5s">
                            <img src="{{ \App\Support\GalleryPath::path('i/72') }}" alt="img">
                        </div>
                        <div class="circle-shape" data-animation="fadeInDown" data-delay="1.5s">
                            <img src="{{ \App\Support\GalleryPath::path('i/73') }}" alt="img">
                        </div>
                        <div class="container">
                            <div class="row g-4 align-items-center">
                                <div class="col-xl-7 col-lg-6">
                                    <div class="hero-content">
                                        <h6 data-animation="fadeInUp" data-delay=".3s" class="hero-title">Welcome to
                                            A1 Airport Cars  Rental</h6>
                                        <h1 data-animation="fadeInUp" data-delay=".5s">
                                            Search, Book <br>
                                            & Rent Car
                                            <span>Easily</span>
                                        </h1>
                                    </div>
                                </div>
                                <div class="col-xl-5 col-lg-6">
                                    <div class="hero-image" data-animation="fadeInUp" data-delay=".7s">
                                        <img src="{{ \App\Support\GalleryPath::path('i/74') }}" alt="img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pick Up Location Section Start -->
    <div class="pickup-loaction-area-2">
        <div class="container">
            <div class="pickup-wrapper style-2 wow fadeInUp" data-wow-delay=".4s">
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
        </div>
    </div>

    <!-- Select Car Section Start -->
    <section class="select-car-section section-padding fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">select car types</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    We’re Offering Popular <br>
                    Cars Models
                </h2>
            </div>
            <div class="row">
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay=".2s">
                    <div class="select-car-items">
                        <div class="thumb">
                            <img src="{{ \App\Support\GalleryPath::path('i/75') }}" alt="">
                        </div>
                        <div class="bg-overlay"></div>
                        <div class="icon">
                            <img src="{{ \App\Support\GalleryPath::path('i/76') }}" alt="img">
                        </div>
                        <div class="content">
                            <h5><a href="car-details">Sedan</a></h5>
                            <p>10 Cars Available</p>
                        </div>
                        <a href="car-details" class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay=".3s">
                    <div class="select-car-items">
                        <div class="thumb">
                            <img src="{{ \App\Support\GalleryPath::path('i/77') }}" alt="">
                        </div>
                        <div class="bg-overlay"></div>
                        <div class="icon">
                            <img src="{{ \App\Support\GalleryPath::path('i/78') }}" alt="img">
                        </div>
                        <div class="content">
                            <h5><a href="car-details">SUV</a></h5>
                            <p>10 Cars Available</p>
                        </div>
                        <a href="car-details" class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay=".4s">
                    <div class="select-car-items">
                        <div class="thumb">
                            <img src="{{ \App\Support\GalleryPath::path('i/79') }}" alt="img">
                        </div>
                        <div class="bg-overlay"></div>
                        <div class="icon">
                            <img src="{{ \App\Support\GalleryPath::path('i/80') }}" alt="img">
                        </div>
                        <div class="content">
                            <h5><a href="car-details">Limousine</a></h5>
                            <p>10 Cars Available</p>
                        </div>
                        <a href="car-details" class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay=".5s">
                    <div class="select-car-items">
                        <div class="thumb">
                            <img src="{{ \App\Support\GalleryPath::path('i/81') }}" alt="img">
                        </div>
                        <div class="bg-overlay"></div>
                        <div class="icon">
                            <img src="{{ \App\Support\GalleryPath::path('i/82') }}" alt="img">
                        </div>
                        <div class="content">
                            <h5><a href="car-details">Cabriolet</a></h5>
                            <p>10 Cars Available</p>
                        </div>
                        <a href="car-details" class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay=".6s">
                    <div class="select-car-items">
                        <div class="thumb">
                            <img src="{{ \App\Support\GalleryPath::path('i/83') }}" alt="img">
                        </div>
                        <div class="bg-overlay"></div>
                        <div class="icon">
                            <img src="{{ \App\Support\GalleryPath::path('i/84') }}" alt="img">
                        </div>
                        <div class="content">
                            <h5><a href="car-details">Pickup</a></h5>
                            <p>10 Cars Available</p>
                        </div>
                        <a href="car-details" class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay=".7s">
                    <div class="select-car-items">
                        <div class="thumb">
                            <img src="{{ \App\Support\GalleryPath::path('i/85') }}" alt="img">
                        </div>
                        <div class="bg-overlay"></div>
                        <div class="icon">
                            <img src="{{ \App\Support\GalleryPath::path('i/86') }}" alt="img">
                        </div>
                        <div class="content">
                            <h5><a href="car-details">Compact</a></h5>
                            <p>10 Cars Available</p>
                        </div>
                        <a href="car-details" class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section Start -->
    <section class="about-section-2 fix section-padding bg-cover"
        style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
        <div class="container">
            <div class="about-wrapper-2">
                <div class="row g-4">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                        <div class="about-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/1') }}" alt="about-image">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content">
                            <div class="section-title">
                                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                                <span class="wow fadeInUp" data-wow-delay=".2s">Get to know us</span>
                                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                                    Trusted & Leading in
                                    Car Rent Services
                                </h2>
                            </div>
                            <p class="mt-3 mt-md-0 wow fadeInUp" data-wow-delay=".6s">
                                There are many variations of passages of Lorem Ipsum available, but the majority have
                                suffered alteration in some form, by injected humour, or randomised.
                            </p>
                            <div class="about-list-wrapper">
                                <div class="about-list-items wow fadeInUp" data-wow-delay=".3s">
                                    <ul>
                                        <li>
                                            <i class="fa-solid fa-arrow-right"></i>
                                            Nsectetur cing elit
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-arrow-right"></i>
                                            Suspe ndisse suscit sagittis leo
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-arrow-right"></i>
                                            If you are going to use pasage
                                        </li>
                                        <li>
                                            <i class="fa-solid fa-arrow-right"></i>
                                            Generators on the to repeatY
                                        </li>
                                    </ul>
                                    <a href="about" class="theme-btn">Discover More</a>
                                </div>
                                <div class="author-items wow fadeInUp" data-wow-delay=".5s">
                                    <div class="icon">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <p>Need help? Talk to an <br> Expert</p>
                                    <h6><a href="tel:9288009850">+92 (8800) - 9850</a></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Car Rentals Section Start -->
    <section class="car-rentals-section-2 section-padding fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">Checkout our new cars</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    Cars We’re Offering <br>
                    for Rentals
                </h2>
            </div>
            <div class="car-rentals-wrapper style-2">
                <div class="array-button">
                    <button class="array-prev"><i class="far fa-chevron-left"></i></button>
                    <button class="array-next"><i class="far fa-chevron-right"></i></button>
                </div>
                <div class="swiper car-rentals-slider-2">
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
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How Works Section Start -->
    <section class="how-works-section fix section-padding pt-0">
        <div class="container">
            <div class="section-title text-center">
                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">Simple 4 easy steps</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">See How It Works</h2>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                    <div class="how-works-items">
                        <h6 class="title"><a href="car-details">Search</a></h6>
                        <div class="icon-box">
                            <div class="icon">
                                <img src="{{ \App\Support\GalleryPath::path('i/90') }}" alt="img" class="icon-1">
                                <img src="{{ \App\Support\GalleryPath::path('i/91') }}" alt="img" class="icon-2">
                            </div>
                        </div>
                        <p>
                            Aliquam viverra arcu. Donec aliquet blandit enim feugiat. Suspendisse id quam sed eros.
                        </p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                    <div class="how-works-items">
                        <h6 class="title"><a href="car-details">Select</a></h6>
                        <div class="icon-box">
                            <div class="icon">
                                <img src="{{ \App\Support\GalleryPath::path('i/92') }}" alt="img" class="icon-1">
                                <img src="{{ \App\Support\GalleryPath::path('i/93') }}" alt="img" class="icon-2">
                            </div>
                        </div>
                        <p>
                            Aliquam viverra arcu. Donec aliquet blandit enim feugiat. Suspendisse id quam sed eros.
                        </p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                    <div class="how-works-items">
                        <h6 class="title"><a href="car-details">Book</a></h6>
                        <div class="icon-box">
                            <div class="icon">
                                <img src="{{ \App\Support\GalleryPath::path('i/94') }}" alt="img" class="icon-1">
                                <img src="{{ \App\Support\GalleryPath::path('i/95') }}" alt="img" class="icon-2">
                            </div>
                        </div>
                        <p>
                            Aliquam viverra arcu. Donec aliquet blandit enim feugiat. Suspendisse id quam sed eros.
                        </p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".8s">
                    <div class="how-works-items">
                        <h6 class="title"><a href="car-details">Drive</a></h6>
                        <div class="icon-box">
                            <div class="icon">
                                <img src="{{ \App\Support\GalleryPath::path('i/96') }}" alt="img" class="icon-1">
                                <img src="{{ \App\Support\GalleryPath::path('i/97') }}" alt="img" class="icon-2">
                            </div>
                        </div>
                        <p>
                            Aliquam viverra arcu. Donec aliquet blandit enim feugiat. Suspendisse id quam sed eros.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rental Benefit Section Start -->
    <section class="rental-benefit-section fix section-padding pb-0">
        <div class="container">
            <div class="rental-benefit-wrapper">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="rental-benefit-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/98') }}" alt="img">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="rental-benefit-content">
                            <div class="section-title">
                                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="img" class="wow fadeInUp">
                                <span class="wow fadeInUp" data-wow-delay=".2s">our benefits</span>
                                <h2 class="text-white wow fadeInUp" data-wow-delay=".4s">
                                    Why You Should Use
                                    A1 Airport Cars  Rental
                                </h2>
                            </div>
                            <p class="wow fadeInUp" data-wow-delay=".6s">
                                There are many variations of passages of available but the majority have suffered.
                                Alteration in some form, lipsum is simply free text by injected humou or randomised
                                words even believable.
                            </p>
                            <div class="icon-items wow fadeInUp" data-wow-delay=".3s">
                                <div class="icon">
                                    <img src="{{ \App\Support\GalleryPath::path('i/99') }}" alt="img">
                                </div>
                                <div class="content">
                                    <p>Benefit 01</p>
                                    <h3>Easy & Fast Booking</h3>
                                </div>
                            </div>
                            <div class="icon-items style-bottom wow fadeInUp" data-wow-delay=".5s">
                                <div class="icon">
                                    <img src="{{ \App\Support\GalleryPath::path('i/100') }}" alt="img">
                                </div>
                                <div class="content">
                                    <p>Benefit 02</p>
                                    <h3>Many Pickup Locations</h3>
                                </div>
                            </div>
                            <div class="benefit-counter-items wow fadeInUp" data-wow-delay=".7s">
                                <div class="icon-img">
                                    <img src="{{ \App\Support\GalleryPath::path('i/101') }}" alt="img">
                                    <div class="divider"></div>
                                </div>
                                <div class="content">
                                    <h2><span class="count">86,700</span></h2>
                                    <p>Our Satisfied Customers </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Brand Section Start -->
    <div class="brand-wrapper style-2 pt-80 pb-80">
        <div class="array-button">
            <button class="array-prev-2"><i class="far fa-chevron-left"></i></button>
            <button class="array-next-2"><i class="far fa-chevron-right"></i></button>
        </div>
        <div class="container">
            <div class="swiper brand-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="brand-image style-color">
                            <img src="{{ \App\Support\GalleryPath::path('i/15') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-image style-color">
                            <img src="{{ \App\Support\GalleryPath::path('i/16') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-image style-color">
                            <img src="{{ \App\Support\GalleryPath::path('i/17') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-image style-color">
                            <img src="{{ \App\Support\GalleryPath::path('i/18') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-image style-color">
                            <img src="{{ \App\Support\GalleryPath::path('i/19') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="brand-image style-color">
                            <img src="{{ \App\Support\GalleryPath::path('i/20') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Car Sale Section Start -->
    <section class="car-sale-section fix section-padding pt-0">
        <div class="container-fluid">
            <div class="car-sale-wrapper">
                <div class="sale-shape">
                    <img src="{{ \App\Support\GalleryPath::path('i/12') }}" alt="shape-img">
                </div>
                <div class="badge-shape">
                    <img src="{{ \App\Support\GalleryPath::path('i/13') }}" alt="shape-img">
                </div>
                <div class="car-shape">
                    <img src="{{ \App\Support\GalleryPath::path('i/14') }}" alt="img">
                </div>
                <div class="car-sale-content">
                    <h2 class="wow fadeInUp" data-wow-delay=".3s">Sale 50% Off</h2>
                    <h3 class="wow fadeInUp" data-wow-delay=".5s">on all rental cars for 1 month</h3>
                    <a href="car-details" class="theme-btn bg-header wow fadeInUp" data-wow-delay=".7s">Book Your
                        Car</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section Start -->
    <section class="testimonial-section fix section-padding">
        <div class="container">
            <div class="testimonial-wrapper">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="section-title">
                            <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="img" class="wow fadeInUp">
                            <span class="wow fadeInUp" data-wow-delay=".2s">our testimonials</span>
                            <h2 class="wow fadeInUp" data-wow-delay=".4s">
                                What They’re
                                Talking About
                                A1 Airport Cars 
                            </h2>
                        </div>
                        <p class="mt-3 mt-md-0 wow fadeInUp" data-wow-delay=".6s">
                            Aliquam viverra arcu. Donec aliquet blandit enim feugiat. Suspendisse id quam sed eros
                            tincidunt luctus sit amet eu nibh tempus turpis.
                        </p>
                        <div class="array-button mt-5 wow fadeInUp" data-wow-delay=".6s">
                            <button class="array-prev"><i class="fa-solid fa-arrow-left-long"></i></button>
                            <button class="array-next"><i class="fa-solid fa-arrow-right-long"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="testimonial-items">
                            <div class="swiper testimonial-slider-2">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="client-info-items">
                                            <p>
                                                This is due to their excellent service, competitive pricing and customer
                                                support. It’s throughly refresing to get such a personal touch. Duis
                                                aute irure dolor in reprehenderit in voluptate velit esse cillum.
                                            </p>
                                            <div class="info">
                                                <div class="icon">
                                                    <img src="{{ \App\Support\GalleryPath::path('i/102') }}" alt="img">
                                                </div>
                                                <div class="name-job">
                                                    <h5 class="name">
                                                        Jessica Brown
                                                    </h5>
                                                    <div class="separator">.</div>
                                                    <span class="job">Customer</span>
                                                </div>
                                                <div class="triangle">
                                                    <div class="inner-triangle"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="client-info-items">
                                            <p>
                                                This is due to their excellent service, competitive pricing and customer
                                                support. It’s throughly refresing to get such a personal touch. Duis
                                                aute irure dolor in reprehenderit in voluptate velit esse cillum.
                                            </p>
                                            <div class="info">
                                                <div class="icon">
                                                    <img src="{{ \App\Support\GalleryPath::path('i/102') }}" alt="img">
                                                </div>
                                                <div class="name-job">
                                                    <h5 class="name">
                                                        Jessica Brown
                                                    </h5>
                                                    <div class="separator">.</div>
                                                    <span class="job">Customer</span>
                                                </div>
                                                <div class="triangle">
                                                    <div class="inner-triangle"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="client-info-items">
                                            <p>
                                                This is due to their excellent service, competitive pricing and customer
                                                support. It’s throughly refresing to get such a personal touch. Duis
                                                aute irure dolor in reprehenderit in voluptate velit esse cillum.
                                            </p>
                                            <div class="info">
                                                <div class="icon">
                                                    <img src="{{ \App\Support\GalleryPath::path('i/102') }}" alt="img">
                                                </div>
                                                <div class="name-job">
                                                    <h5 class="name">
                                                        Jessica Brown
                                                    </h5>
                                                    <div class="separator">.</div>
                                                    <span class="job">Customer</span>
                                                </div>
                                                <div class="triangle">
                                                    <div class="inner-triangle"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-image">
                                <img src="{{ \App\Support\GalleryPath::path('i/103') }}" alt="img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Car Slider Section Start -->
    <div class="car-slider-section section-padding pt-0">
        <div class="car-slider-wrapper">
            <div class="swiper car-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="car-slider-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/104') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="car-slider-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/105') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="car-slider-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/106') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="car-slider-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/107') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="car-slider-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/108') }}" alt="img">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="car-slider-image">
                            <img src="{{ \App\Support\GalleryPath::path('i/109') }}" alt="img">
                        </div>
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


    @include('partials.footer')

    @include('partials.script')
</body>

</html>


