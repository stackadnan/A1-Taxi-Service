<?php
$headTitle = 'A1 Airport Cars ';
$img = 'assets/img/bg-header-banner.jpg';
$Title = 'Home';
$Title2 = 'blog details';
$SubTitle = 'blog details';
?>

@include('partials.layouts.layoutsTop')



<!-- News Details Section Start -->
<section class="news-details-section fix section-padding">
    <div class="container">
        <div class="news-details-wrapper">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="news-details-items">
                        <div class="news-image">
                            <img src="assets/img/news/news-details.jpg" alt="news-img">
                        </div>
                        <div class="news-details-content">
                            <ul class="list-admin">
                                <li>
                                    <i class="fa-solid fa-circle-user"></i>
                                    by Admin
                                </li>
                                <li>
                                    /
                                </li>
                                <li>
                                    <i class="fa-solid fa-comments"></i>
                                    2 Comments
                                </li>
                            </ul>
                            <h3>The best fastest and most powerful road car</h3>
                            <p class="mt-3">
                                Lorem ipsum dolor sit amet, cibo mundi ea duo, vim exerci phaedrum. There are many
                                variations of passages of Lorem Ipsum available, but the majority have alteration in
                                some injected or words which don't look even slightly believable. If you are going
                                to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrang
                                hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to
                                repeat predefined chunks as necessary, making this the first true generator on the
                                Internet. It uses a dictionary of over 200 Latin words, combined with a handful of
                                model sentence structures, to generate Lorem Ipsum which looks reasonable.
                            </p>
                            <p class="mt-4">
                                Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when
                                an unknown printer took a galley of type and scrambled it to make a type simen book.
                                It has survived not only five centuries, but also the leap into electronic
                                typesetting.
                            </p>
                            <p class="mt-4">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. orem
                                Ipsum has been the industry's standard dummy text ever since the when an unknown
                                printer took a galley of type and scrambled it to make a type specimen book.
                            </p>
                        </div>
                    </div>
                    <div class="tag-share-wrap mt-4 mb-4">
                        <div class="tagcloud">
                            <span>Tags</span>
                            <a href="news-details">Off Road</a>
                            <a href="news-details">Luxury</a>
                        </div>
                        <div class="social-share d-flex align-items-center">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="news-rental-servce-items">
                                <h4>
                                    Looking for the best car rental service
                                </h4>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="news-rental-servce-items style-2">
                                <h4>
                                    Looking for the best car rental service
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="comment-reviews">
                        <h3>2 Reviews</h3>
                        <div class="single-comment d-flex gap-4 pb-5">
                            <div class="image">
                                <img src="assets/img/car/comment01.png" alt="image">
                            </div>
                            <div class="content">
                                <div class="head d-flex flex-wrap gap-3 align-items-center justify-content-between">
                                    <div class="con">
                                        <h4>Kevin Martin</h4>
                                    </div>
                                    <a href="news-details" class="reply">Reply</a>
                                </div>
                                <p class="mt-4">
                                    It has survived not only five centuries, but also the leap into electronic
                                    typesetting simply fee text aunchanged. It was popularised in the sheets
                                    containing lorem ipsum is simply free text.
                                </p>
                            </div>
                        </div>
                        <div class="single-comment d-flex gap-4 pt-5 pb-5">
                            <div class="image">
                                <img src="assets/img/car/comment02.png" alt="image">
                            </div>
                            <div class="content">
                                <div class="head d-flex flex-wrap gap-3 align-items-center justify-content-between">
                                    <div class="con">
                                        <h4>Sarah Albert</h4>
                                    </div>
                                    <a href="news-details" class="reply">Reply</a>
                                </div>
                                <p class="mt-4">
                                    It has survived not only five centuries, but also the leap into electronic
                                    typesetting simply fee text aunchanged. It was popularised in the sheets
                                    containing lorem ipsum is simply free text.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="comment-form-wrap pt-5">
                        <h3>Leave a comments</h3>
                        <form action="#" id="contact-form" method="POST">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <input type="text" name="name" id="name" placeholder="Your Name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <input type="text" name="email" id="email2" placeholder="Your Email">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <textarea name="message" id="message" placeholder="Write a Comment"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <button type="submit" class="theme-btn">
                                        Submit Comment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="main-sidebar">
                        <div class="search-widget">
                            <form action="#">
                                <input type="text" placeholder="Search">
                                <button type="submit"><i class="fa-regular fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                        <div class="single-sidebar-widget">
                            <div class="wid-title">
                                <h3>Recent Post</h3>
                            </div>
                            <div class="recent-post-area">
                                <div class="recent-items">
                                    <div class="recent-thumb">
                                        <img src="assets/img/news/pp1.jpg" alt="img">
                                    </div>
                                    <div class="recent-content">
                                        <ul>
                                            <li>
                                                <i class="fa-solid fa-comments"></i>
                                                2 Comments
                                            </li>
                                        </ul>
                                        <h6>
                                            <a href="news-details">
                                                Hassle-free Rental
                                                Experience
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                                <div class="recent-items">
                                    <div class="recent-thumb">
                                        <img src="assets/img/news/pp2.jpg" alt="img">
                                    </div>
                                    <div class="recent-content">
                                        <ul>
                                            <li>
                                                <i class="fa-solid fa-comments"></i>
                                                2 Comments
                                            </li>
                                        </ul>
                                        <h6>
                                            <a href="news-details">
                                                Hassle-free Rental
                                                Experience
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                                <div class="recent-items">
                                    <div class="recent-thumb">
                                        <img src="assets/img/news/pp3.jpg" alt="img">
                                    </div>
                                    <div class="recent-content">
                                        <ul>
                                            <li>
                                                <i class="fa-solid fa-comments"></i>
                                                2 Comments
                                            </li>
                                        </ul>
                                        <h6>
                                            <a href="news-details">
                                                Hassle-free Rental
                                                Experience
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="single-sidebar-widget">
                            <div class="wid-title">
                                <h3>Categories</h3>
                            </div>
                            <div class="news-widget-categories">
                                <ul>
                                    <li><a href="news-details">Rental <i
                                                class="fa-solid fa-arrow-right-long"></i></a></li>
                                    <li><a href="news-details">Luxury Cars <i
                                                class="fa-solid fa-arrow-right-long"></i></a></li>
                                    <li class="active"><a href="news-details">Dream Cars <i
                                                class="fa-solid fa-arrow-right-long"></i></a></li>
                                    <li><a href="news-details">Dream Cars <i
                                                class="fa-solid fa-arrow-right-long"></i></a></li>
                                    <li><a href="news-details">Off Road <i
                                                class="fa-solid fa-arrow-right-long"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="single-sidebar-widget">
                            <div class="wid-title">
                                <h3>Tags</h3>
                            </div>
                            <div class="news-widget-categories">
                                <div class="tagcloud">
                                    <a href="news">Off Road</a>
                                    <a href="news-details">Luxury</a>
                                    <a href="news-details">Cars</a>
                                    <a href="news-details">Rentals</a>
                                    <a href="news-details">Engine</a>
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
                        <img src="assets/img/logo/white-logo.svg" alt="logo-img">
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


