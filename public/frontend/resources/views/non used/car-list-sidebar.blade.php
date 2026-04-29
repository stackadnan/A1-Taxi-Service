<?php
$headTitle = 'A1 Airport Cars ';
$img = \App\Support\GalleryPath::path('i/149');
$Title = 'Home';
$Title2 = 'Cars';
$SubTitle = 'list Style';
?>

@include('partials.layouts.layoutsTop')

<!-- Car Rentals Section Start -->
<section class="car-list-section section-padding fix">
    <div class="container">
        <div class="car-list-wrapper">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="car-list-sidebar">
                        <h4 class="title">Search Car</h4>
                        <form action="#" id="contact-form" method="POST" class="contact-form-items">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <label class="label-text">Category</label>
                                        <div class="category-oneadjust">
                                            <select name="cate" class="category">
                                                <option value="1">
                                                    Select Category
                                                </option>
                                                <option value="1">
                                                    Category 01
                                                </option>
                                                <option value="1">
                                                    Category 02
                                                </option>
                                                <option value="1">
                                                    Category 03
                                                </option>
                                                <option value="1">
                                                    Category 04
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <label class="label-text">Pick-up Location</label>
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
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <label class="label-text">Dropoff Location</label>
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
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <label class="label-text">Pick-up Date</label>
                                        <div id="datepicker" class="input-group date" data-date-format="dd-mm-yyyy">
                                            <input class="form-control" type="text" placeholder="Check in" readonly>
                                            <span class="input-group-addon"> <i
                                                    class="fa-solid fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <label class="label-text">Drop-off Date</label>
                                        <div id="datepicker2" class="input-group date" data-date-format="dd-mm-yyyy">
                                            <input class="form-control" type="text" placeholder="Check in" readonly>
                                            <span class="input-group-addon"> <i
                                                    class="fa-solid fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <label class="label-text">Car Type</label>
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
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <label class="label-text">Colors</label>
                                        <div class="category-oneadjust">
                                            <select name="cate" class="category">
                                                <option value="1">
                                                    Select Colors
                                                </option>
                                                <option value="1">
                                                    Red
                                                </option>
                                                <option value="1">
                                                    Green
                                                </option>
                                                <option value="1">
                                                    Yellow
                                                </option>
                                                <option value="1">
                                                    Black
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <button type="submit" class="theme-btn">Search Now</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="woocommerce-notices-wrapper">
                        <div class="product-showing">
                            <p>Showing 1–9 of 12 Results</p>
                        </div>
                        <div class="woocommerce-right d-flex align-items-center">
                            <div class="icon-items">
                                <a href="car-grid"><i class="fas fa-th"></i></a>
                                <a href="car-list-sidebar"><i class="fa-solid fa-list"></i></a>
                            </div>
                            <div class="form-clt">
                                <div class="nice-select" tabindex="0">
                                    <span class="current">
                                        Sort by Popular
                                    </span>
                                    <ul class="list">
                                        <li data-value="1" class="option selected focus">
                                            Default sorting
                                        </li>
                                        <li data-value="1" class="option">
                                            Sort by popularity
                                        </li>
                                        <li data-value="1" class="option">
                                            Sort by average rating
                                        </li>
                                        <li data-value="1" class="option">
                                            Sort by latest
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="car-list-items style-2">
                                <div class="car-image bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                                    <div class="post-cat">
                                        2024 Model
                                    </div>
                                </div>
                                <div class="car-content">
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <span>2 Reviews</span>
                                    </div>
                                    <h6 class="price">$70.00 <span>/ Day</span></h6>
                                    <h3><a href="car-details">Hyundai Accent Limited</a></h3>
                                    <p>
                                        To deliver on the promise of technology and human ingenuity We help our
                                        clients become the next and best versions of themselves.
                                    </p>
                                    <ul class="icon-items">
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
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
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="car-list-items style-2">
                                <div class="car-image bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                                    <div class="post-cat">
                                        2024 Model
                                    </div>
                                </div>
                                <div class="car-content">
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <span>2 Reviews</span>
                                    </div>
                                    <h6 class="price">$70.00 <span>/ Day</span></h6>
                                    <h3><a href="car-details">Hyundai Accent Limited</a></h3>
                                    <p>
                                        To deliver on the promise of technology and human ingenuity We help our
                                        clients become the next and best versions of themselves.
                                    </p>
                                    <ul class="icon-items">
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
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
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="car-list-items style-2">
                                <div class="car-image bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                                    <div class="post-cat">
                                        2024 Model
                                    </div>
                                </div>
                                <div class="car-content">
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <span>2 Reviews</span>
                                    </div>
                                    <h6 class="price">$70.00 <span>/ Day</span></h6>
                                    <h3><a href="car-details">Hyundai Accent Limited</a></h3>
                                    <p>
                                        To deliver on the promise of technology and human ingenuity We help our
                                        clients become the next and best versions of themselves.
                                    </p>
                                    <ul class="icon-items">
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
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
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="car-list-items style-2">
                                <div class="car-image bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                                    <div class="post-cat">
                                        2024 Model
                                    </div>
                                </div>
                                <div class="car-content">
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <span>2 Reviews</span>
                                    </div>
                                    <h6 class="price">$70.00 <span>/ Day</span></h6>
                                    <h3><a href="car-details">Hyundai Accent Limited</a></h3>
                                    <p>
                                        To deliver on the promise of technology and human ingenuity We help our
                                        clients become the next and best versions of themselves.
                                    </p>
                                    <ul class="icon-items">
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
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
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="car-list-items style-2">
                                <div class="car-image bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                                    <div class="post-cat">
                                        2024 Model
                                    </div>
                                </div>
                                <div class="car-content">
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <span>2 Reviews</span>
                                    </div>
                                    <h6 class="price">$70.00 <span>/ Day</span></h6>
                                    <h3><a href="car-details">Hyundai Accent Limited</a></h3>
                                    <p>
                                        To deliver on the promise of technology and human ingenuity We help our
                                        clients become the next and best versions of themselves.
                                    </p>
                                    <ul class="icon-items">
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
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
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="car-list-items style-2">
                                <div class="car-image bg-cover"
                                    style="background-image: url('{{ \App\Support\GalleryPath::path('') }}');">
                                    <div class="post-cat">
                                        2024 Model
                                    </div>
                                </div>
                                <div class="car-content">
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <span>2 Reviews</span>
                                    </div>
                                    <h6 class="price">$70.00 <span>/ Day</span></h6>
                                    <h3><a href="car-details">Hyundai Accent Limited</a></h3>
                                    <p>
                                        To deliver on the promise of technology and human ingenuity We help our
                                        clients become the next and best versions of themselves.
                                    </p>
                                    <ul class="icon-items">
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/29') }}" alt="img" class="me-1">
                                            6 Seats
                                        </li>
                                        <li>
                                            <img src="{{ \App\Support\GalleryPath::path('i/28') }}" alt="img" class="me-1">
                                            2 Doors
                                        </li>
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
                            </div>
                        </div>
                    </div>
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

@include('partials.layouts.layoutsBottom')


