<!-- Offcanvas Area Start -->
    <div class="fix-area">
        <div class="offcanvas__info">
            <div class="offcanvas__wrapper">
                <div class="offcanvas__content">
                    <div class="offcanvas__top mb-5 d-flex justify-content-between align-items-center">
                        <div class="offcanvas__logo">
                            <a href="./">
                                <img src="<?php echo e(\App\Support\GalleryPath::path($logo ?? 'i/154')); ?>" alt="logo-img">
                            </a>
                        </div>
                        <div class="offcanvas__close">
                            <button>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- <p class="text d-none d-xl-block">
                        Nullam dignissim, ante scelerisque the is euismod fermentum odio sem semper the is erat, a
                        feugiat leo urna eget eros. Duis Aenean a imperdiet risus.
                    </p> -->
                    <div class="mobile-menu fix mb-3"></div>
                    <div class="offcanvas__contact">
                        <h4>Contact Info</h4>
                        <ul>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon">
                                    <i class="fal fa-map-marker-alt"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a target="_blank" href="#"><?php echo e($address ?? '960 Capability Green, LU1 3PE Luton'); ?></a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="fal fa-envelope"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="mailto:<?php echo e($email ?? 'info@a1airportcars.co.uk'); ?>"><span><?php echo e($email ?? 'info@a1airportcars.co.uk'); ?></span></a>
                                </div>
                            </li>
                            
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="far fa-phone"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="tel:<?php echo e($phone ?? '(+44) - 1582 - 801 - 611'); ?>"><?php echo e($phone ?? '(+44) - 1582 - 801 - 611'); ?></a>
                                </div>
                            </li>
                        </ul>
                        <div class="header-button mt-4">
                            <a href="manage-booking" class="theme-btn text-center">
                                <span><?php echo e($buttonText ?? 'Manage My Booking'); ?><i class="fa-solid fa-arrow-right-long"></i></span>
                            </a>
                        </div>
                        <div class="social-icon d-flex align-items-center">
                            <?php $__currentLoopData = $socialLinks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e($social['link'] ?? '#'); ?>"><i class="<?php echo e($social['icon'] ?? 'fab fa-facebook-f'); ?>"></i></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas__overlay"></div>

<?php /**PATH /home/executiveairport/public_html/frontend/resources/views/partials/offcanvas.blade.php ENDPATH**/ ?>