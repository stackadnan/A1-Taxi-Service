<?php
$headTitle = 'A1 Airport Cars ';
$img = 'assets/img/bg-header-banner.jpg';
$Title = 'Home';
$Title2 = 'Cars';
$SubTitle = 'Single Car';
?>

@include('partials.layouts.layoutsTop')


<!-- Car Details Section Start -->
<section class="car-details fix section-padding">
    <div class="container">
        <div class="car-details-wrapper">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="car-details-items">
                        <div class="car-image">
                            <img src="assets/img/car/car-details.jpg" alt="img">
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
                            <h3>Hyundai Accent Limited</h3>
                            <h6>$70.00 <span>/ Day</span></h6>
                            <p class="mt-4 mb-4">
                                To deliver on the promise of technology and human We help our clients become sions of
                                themselves. Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                orem ipsum has been the industryâ€™s standard dummy text ever since the 1500s.
                            </p>
                            <div class="icon-details-area">
                                <h4>Key Features</h4>
                                <div class="icon-details-main-items">
                                    <div class="icon-items">
                                        <div class="icon">
                                            <img src="assets/img/car/icon/07.png" alt="img">
                                        </div>
                                        <div class="content">
                                            <h6>body:</h6>
                                            <p>Sedan</p>
                                        </div>
                                    </div>
                                    <div class="icon-items">
                                        <div class="icon">
                                            <img src="assets/img/car/icon/07.png" alt="img">
                                        </div>
                                        <div class="content">
                                            <h6>Mileage:</h6>
                                            <p>70.000 (Mi)</p>
                                        </div>
                                    </div>
                                    <div class="icon-items">
                                        <div class="icon">
                                            <img src="assets/img/car/icon/07.png" alt="img">
                                        </div>
                                        <div class="content">
                                            <h6>Year:</h6>
                                            <p>2024</p>
                                        </div>
                                    </div>
                                    <div class="icon-items">
                                        <div class="icon">
                                            <img src="assets/img/car/icon/07.png" alt="img">
                                        </div>
                                        <div class="content">
                                            <h6>Engine:</h6>
                                            <p>2500 cc</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="icon-details-main-items">
                                    <div class="icon-items">
                                        <div class="icon">
                                            <img src="assets/img/car/door.svg" alt="img">
                                        </div>
                                        <div class="content">
                                            <h6>Passengers:</h6>
                                            <p>6 Seats</p>
                                        </div>
                                    </div>
                                    <div class="icon-items">
                                        <div class="icon">
                                            <img src="assets/img/car/seat.svg" alt="img">
                                        </div>
                                        <div class="content">
                                            <h6>Gear:</h6>
                                            <p>Automatic</p>
                                        </div>
                                    </div>
                                    <div class="icon-items">
                                        <div class="icon">
                                            <img src="assets/img/car/automatic.svg" alt="img">
                                        </div>
                                        <div class="content">
                                            <h6>Gear:</h6>
                                            <p>Automatic</p>
                                        </div>
                                    </div>
                                    <div class="icon-items">
                                        <div class="icon">
                                            <img src="assets/img/car/petrol.svg" alt="img">
                                        </div>
                                        <div class="content">
                                            <h6>Fuel:</h6>
                                            <p>Petrol</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="price-table-area">
                                <h6>Table Price <span>( by day of the week )</span></h6>
                                <div class="price-table-items section-bg">
                                    <p>Monday</p>
                                    <p>$70.00</p>
                                </div>
                                <div class="price-table-items">
                                    <p>Tuesday</p>
                                    <p>$70.00</p>
                                </div>
                                <div class="price-table-items section-bg">
                                    <p>Wednesday</p>
                                    <p>$70.00</p>
                                </div>
                                <div class="price-table-items">
                                    <p>Thursday</p>
                                    <p>$70.00</p>
                                </div>
                                <div class="price-table-items section-bg">
                                    <p>Friday</p>
                                    <p>$70.00</p>
                                </div>
                                <div class="price-table-items">
                                    <p>Saturday</p>
                                    <p>$70.00</p>
                                </div>
                                <div class="price-table-items section-bg">
                                    <p>Sunday</p>
                                    <p>$70.00</p>
                                </div>
                            </div>
                            <div class="car-video">
                                <img src="assets/img/car/car-details-2.jpg" alt="img">
                                <div class="video-box">
                                    <a href="https://www.youtube.com/watch?v=Cn4G2lZ_g2I"
                                        class="video-btn ripple video-popup">
                                        <i class="fa-solid fa-play"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="car-booking-items">
                        <div class="booking-header">
                            <h3>Request for Booking</h3>
                            <p>Send your requirement to us. We will check email and contact you soon.</p>
                        </div>
                        <form action="#" id="contact-form" method="POST" class="contact-form-items">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <label class="label-text">Your Name</label>
                                        <input type="text" name="name" id="name" placeholder="Afzaal Islam">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <label class="label-text">Email</label>
                                        <input type="text" name="email" id="email" placeholder="pixydrops@gmail.com">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <label class="label-text">Phone Number</label>
                                        <input type="text" name="phone" id="phone" placeholder="+ 00 0000 - 000">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <label class="label-text">Address</label>
                                        <input type="text" name="address" id="address" placeholder="Address">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-clt">
                                        <label class="label-text">Pick-up Location</label>
                                        <div class="category-oneadjust">
                                            <select name="cate" class="category" style="display: none;">
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
                                            <div class="nice-select category" tabindex="0"><span class="current">
                                                    Select Location
                                                </span>
                                                <ul class="list">
                                                    <li data-value="1" class="option selected">
                                                        Select Location
                                                    </li>
                                                    <li data-value="1" class="option">
                                                        Houston
                                                    </li>
                                                    <li data-value="1" class="option">
                                                        Texas
                                                    </li>
                                                    <li data-value="1" class="option">
                                                        New York
                                                    </li>
                                                    <li data-value="1" class="option">
                                                        Other Location
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-clt">
                                        <label class="label-text">Pick-up Date</label>
                                        <div id="datepicker3" class="input-group date" data-date-format="dd-mm-yyyy">
                                            <input class="form-control" type="text" placeholder="Check in" readonly>
                                            <span class="input-group-addon"> <i
                                                    class="fa-solid fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-clt">
                                        <label class="label-text">Drop-off Date</label>
                                        <div id="datepicker4" class="input-group date" data-date-format="dd-mm-yyyy">
                                            <input class="form-control" type="text" placeholder="Check in" readonly>
                                            <span class="input-group-addon"> <i
                                                    class="fa-solid fa-calendar-days"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="input-save-items-area">
                                        <div class="input-save-items">
                                            <div class="input-save d-flex align-items-center mb-3">
                                                <input type="checkbox" class="form-check-input" name="save-for-next"
                                                    id="saveForNext1">
                                                <label for="saveForNext1">Driver</label>
                                            </div>
                                            <div class="input-save d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input" name="save-for-next"
                                                    id="saveForNext2">
                                                <label for="saveForNext2">Baby Seat</label>
                                            </div>
                                        </div>
                                        <div class="input-save-items">
                                            <div class="input-save d-flex align-items-center mb-3">
                                                <label>$10.00 / Day</label>
                                            </div>
                                            <div class="input-save d-flex align-items-center">
                                                <label>$30.00 / Total</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button class="theme-btn" type="submit">
                                        Send Request
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="comment-reviews">
                        <h3>2 Reviews</h3>
                        <div class="car-single-comment d-flex gap-4 pb-5">
                            <div class="image">
                                <img src="assets/img/car/comment01.png" alt="image">
                            </div>
                            <div class="content">
                                <div class="head d-flex flex-wrap gap-3 align-items-center justify-content-between">
                                    <div class="con">
                                        <h4>Kevin Martin</h4>
                                    </div>
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                                <p class="mt-4">
                                    It has survived not only five centuries, but also the into electronic typesetting
                                    simply fee text aunchanged. It was popularised in the sheets containing lorem ipsum
                                    is simply free text.
                                </p>
                            </div>
                        </div>
                        <div class="car-single-comment d-flex gap-4 pt-5 border-none">
                            <div class="image">
                                <img src="assets/img/car/comment02.png" alt="image">
                            </div>
                            <div class="content">
                                <div class="head d-flex flex-wrap gap-3 align-items-center justify-content-between">
                                    <div class="con">
                                        <h4>Sarah Albert</h4>
                                    </div>
                                    <div class="star">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                                <p class="mt-4">
                                    It has survived not only five centuries, but also the into electronic typesetting
                                    simply fee text aunchanged. It was popularised in the sheets containing lorem ipsum
                                    is simply free text.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="car-list-sidebar">
                        <h4 class="title">Booking Form</h4>
                        <form action="#" id="contact-form2" method="POST" class="contact-form-items">
                            <div class="row g-4">
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
                                        <label class="label-text">Quantity</label>
                                        <div class="category-oneadjust">
                                            <select name="cate" class="category">
                                                <option value="1">
                                                    1
                                                </option>
                                                <option value="1">
                                                    2
                                                </option>
                                                <option value="1">
                                                    3
                                                </option>
                                                <option value="1">
                                                    4
                                                </option>
                                                <option value="1">
                                                    5
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <label class="label-text">Resources</label>
                                        <div class="input-save-items-area">
                                            <div class="input-save-items">
                                                <div class="input-save d-flex align-items-center mb-3">
                                                    <input type="checkbox" class="form-check-input" name="save-for-next"
                                                        id="saveForNext3">
                                                    <label for="saveForNext3">Driver</label>
                                                </div>
                                                <div class="input-save d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input" name="save-for-next"
                                                        id="saveForNext4">
                                                    <label for="saveForNext4">Baby Seat</label>
                                                </div>
                                            </div>
                                            <div class="input-save-items">
                                                <div class="input-save d-flex align-items-center mb-3">
                                                    <label>$10.00 / Day</label>
                                                </div>
                                                <div class="input-save d-flex align-items-center">
                                                    <label>$30.00 / Total</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <button type="submit" class="theme-btn">Book Now</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Car Rentals Section Start -->
<section class="car-rentals-section-2 section-padding fix pt-0">
    <div class="container">
        <div class="section-title text-center">
            <img src="assets/img/sub-icon.png" alt="icon-img" class="wow fadeInUp">
            <span class="wow fadeInUp" data-wow-delay=".2s">Checkout our new cars</span>
            <h2 class="wow fadeInUp" data-wow-delay=".4s">
                Similar Cars Available
            </h2>
        </div>
        <div class="row">
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                <div class="car-rentals-items">
                    <div class="car-image">
                        <img src="assets/img/car/08.jpg" alt="img">
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
                                    <img src="assets/img/car/seat.svg" alt="img" class="me-1">
                                    6 Seats
                                </li>
                                <li>
                                    <img src="assets/img/car/door.svg" alt="img" class="me-1">
                                    2 Doors
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <img src="assets/img/car/automatic.svg" alt="img" class="me-1">
                                    Automatic
                                </li>
                                <li>
                                    <img src="assets/img/car/petrol.svg" alt="img" class="me-1">
                                    Petrol
                                </li>
                            </ul>
                        </div>
                        <a href="car-details" class="theme-btn bg-color w-100 text-center">book now <i
                                class="fa-solid fa-arrow-right ps-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                <div class="car-rentals-items">
                    <div class="car-image">
                        <img src="assets/img/car/09.jpg" alt="img">
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
                                    <img src="assets/img/car/seat.svg" alt="img" class="me-1">
                                    6 Seats
                                </li>
                                <li>
                                    <img src="assets/img/car/door.svg" alt="img" class="me-1">
                                    2 Doors
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <img src="assets/img/car/automatic.svg" alt="img" class="me-1">
                                    Automatic
                                </li>
                                <li>
                                    <img src="assets/img/car/petrol.svg" alt="img" class="me-1">
                                    Petrol
                                </li>
                            </ul>
                        </div>
                        <a href="car-details" class="theme-btn bg-color w-100 text-center">book now <i
                                class="fa-solid fa-arrow-right ps-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
                <div class="car-rentals-items">
                    <div class="car-image">
                        <img src="assets/img/car/10.jpg" alt="img">
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
                                    <img src="assets/img/car/seat.svg" alt="img" class="me-1">
                                    6 Seats
                                </li>
                                <li>
                                    <img src="assets/img/car/door.svg" alt="img" class="me-1">
                                    2 Doors
                                </li>
                            </ul>
                            <ul>
                                <li>
                                    <img src="assets/img/car/automatic.svg" alt="img" class="me-1">
                                    Automatic
                                </li>
                                <li>
                                    <img src="assets/img/car/petrol.svg" alt="img" class="me-1">
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
</section>



@include('partials.layouts.layoutsBottom')


