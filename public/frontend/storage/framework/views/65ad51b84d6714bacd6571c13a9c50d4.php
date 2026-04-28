<?php
$headTitle = 'A1 Airport Cars ';
$img = \App\Support\GalleryPath::path('i/149');
$Title = 'Home';
$Title2 = 'Contact';
$SubTitle = 'Contact';
?>

<?php echo $__env->make('partials.layouts.layoutsTop', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Contact Section Start -->
<section class="contact-section-1 fix section-padding pb-0">
    <div class="container">
        <div class="contact-wrapper-area">
            <div class="row g-4">
                <div class="col-lg-9">
                    <div class="contact-content">
                        <div class="section-title">
                            <img src="<?php echo e(\App\Support\GalleryPath::path('i/2')); ?>" alt="icon-img" class="wow fadeInUp">
                            <span class="wow fadeInUp" data-wow-delay=".2s">contact us</span>
                            <h2 class="wow fadeInUp" data-wow-delay=".4s">
                                Drop us a Line
                            </h2>
                        </div>
                        <form action="<?php echo e(route('contact.send')); ?>" id="contact-form" method="POST"
                            class="contact-form-items mt-5 mt-md-0">
                            <?php echo csrf_field(); ?>
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
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <input type="text" name="subject" id="subject" placeholder="Subject">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <input type="text" name="phone" id="phone" placeholder="Phone">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <textarea name="message" id="message" placeholder="Write a Comment"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <button type="submit" class="theme-btn">
                                        Send a Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="contact-right-items">
                        <div class="contact-img">
                            <img src="<?php echo e(\App\Support\GalleryPath::path('i/41')); ?>" alt="img">
                        </div>
                        <div class="icon-items">
                            <div class="icon">
                                <img src="<?php echo e(\App\Support\GalleryPath::path('i/42')); ?>" alt="img">
                            </div>
                            <div class="content">
                                <p>Have Question?</p>
                                <h6><a href="tel:+923076806860">+ 92 ( 307 ) 68 - 06860</a></h6>
                            </div>
                        </div>
                        <div class="icon-items">
                            <div class="icon">
                                <img src="<?php echo e(\App\Support\GalleryPath::path('i/43')); ?>" alt="img">
                            </div>
                            <div class="content">
                                <p>Write Email</p>
                                <h6><a href="mailto:info@example.com" class="link">info@example.com</a></h6>
                            </div>
                        </div>
                        <div class="icon-items">
                            <div class="icon">
                                <img src="<?php echo e(\App\Support\GalleryPath::path('i/44')); ?>" alt="img">
                            </div>
                            <div class="content">
                                <p>Visit Office</p>
                                <h6>
                                    960 Capability Green, LU1 3PE <br>
                                    Luton, United Kingdom
                                </h6>
                            </div>
                        </div>
                        <div class="social-icon d-flex align-items-center">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--<< Map Section Start >>-->
<div class="map-section">
    <div class="map-items">
        <div class="googpemap">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6678.7619084840835!2d144.9618311901502!3d-37.81450084255415!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!
                4f13.1!3m3!1m2!1s0x6ad642b4758afc1d%3A0x3119cc820fdfc62e!2sEnvato!5e0!3m2!1sen!2sbd!4v1641984054261!5m2!1sen!2sbd"
                style="border:0;" allowfullscreen="" loading="lazy"></iframe>
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
                        <img src="<?php echo e(\App\Support\GalleryPath::path('i/21')); ?>" alt="logo-img">
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

<?php echo $__env->make('partials.layouts.layoutsBottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php /**PATH /home/executiveairport/public_html/frontend/resources/views/contact.blade.php ENDPATH**/ ?>