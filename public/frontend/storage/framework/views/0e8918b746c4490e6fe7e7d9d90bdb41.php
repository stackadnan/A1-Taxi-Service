 <?php ($baseUrl = rtrim(request()->getBaseUrl(), '/')); ?>
 <section class="cta-cheap-rental-section">
        <div class="container">
            <div class="cta-cheap-rental">
                <div class="cta-cheap-rental-left wow fadeInUp" data-wow-delay="
                    .3s">
                    <div class="logo-thumb">
                        <a href="./">
                            <img src="<?php echo e(\App\Support\GalleryPath::path($footerLogo ?? 'i/152')); ?>" alt="logo-img">
                        </a>
                    </div>
                    <h4 class="text-white"><?php echo e($footerTagline ?? 'Your go to option for reliable Airport Transfers'); ?></h4>
                </div>
                <div class="social-icon d-flex align-items-center wow fadeInUp" data-wow-delay="
                    .5s">
                    <?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($social['link'] ?? '#'); ?>"><i class="<?php echo e($social['icon'] ?? 'fab fa-facebook-f'); ?>"></i></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>

<!-- Footer Section Start -->
<footer class="footer-section fix">
    <div class="container">
        <div class="footer-widgets-wrapper">
            <div class="row justify-content-between">
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                    <div class="single-footer-widget shape-map">
                        <div class="widget-head">
                            <h4>Contact</h4>
                        </div>
                        <div class="footer-content">
                            <p><?php echo e($contactAddress ?? '960 Capability Green, LU1 3PE Luton, United Kingdom'); ?></p>
                            <ul class="contact-info">
                                <li>
                                    <i class="fa-regular fa-envelope"></i>
                                    <a href="mailto:<?php echo e($contactEmail ?? 'info@a1airportcars.co.uk'); ?>"><?php echo e($contactEmail ?? 'info@a1airportcars.co.uk'); ?></a>
                                </li>
                                <li>
                                    <i class="fa-solid fa-phone-volume"></i>
                                    <a href="tel:<?php echo e($contactPhone ?? '(+44) - 1582 - 801 - 611'); ?>"><?php echo e($contactPhone ?? '(+44) - 1582 - 801 - 611'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                    <div class="single-footer-widget">
                        <div class="widget-head">
                            <h4>Links</h4>
                        </div>
                        <ul class="list-items">
                            <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a href="<?php echo e($baseUrl.'/'.ltrim($link['url'], '/')); ?>"><?php echo e($link['label']); ?></a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e($baseUrl.'/complainet/lost-found'); ?>">Complainet / Lost Found</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                    <div class="single-footer-widget">
                        <div class="widget-head">
                            <h4>Airports</h4>
                        </div>
                        <ul class="list-items">
                            <?php $__currentLoopData = $airports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a href="<?php echo e(isset($airport['url']) ? $baseUrl.'/'.ltrim($airport['url'], '/') : '#'); ?>"><?php echo e($airport['label']); ?></a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".8s">
                    <div class="single-footer-widget">
                        <div class="widget-head">
                            <h4>Cities Covered</h4>
                        </div>
                        <ul class="list-items list-itemscol2">
                            <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a href="<?php echo e($baseUrl.'/about'); ?>"><?php echo e($city); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-wrapper">
                <p class="wow fadeInUp" data-wow-delay=".4s">
                    <?php echo $copyright ?? '&copy; Copyright '.date('Y').' A1 Airport Cars | Powered by <a href="./">BXS</a>'; ?>

                </p>
            </div>
        </div>
    </div>
</footer>

<?php /**PATH /home/executiveairport/public_html/frontend/resources/views/partials/footer.blade.php ENDPATH**/ ?>