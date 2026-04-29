<section class="testimonial-section fix section-padding">
    <div class="testimonial-bg-shape">
        <img src="<?php echo e(\App\Support\GalleryPath::path('i/11')); ?>" alt="shape-img">
    </div>
    <div class="container">
        <div class="section-title-area">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="section-title">
                        <img src="<?php echo e(\App\Support\GalleryPath::path('i/2')); ?>" alt="icon-img" class="wow fadeInUp">
                        <span class="wow fadeInUp" data-wow-delay=".2s"><?php echo e($testimonialSectionTitle ?? 'Our Testimonials'); ?></span>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">
                            <?php echo e($testimonialSectionHeading ?? 'What They’re Saying About A1 Airport Cars'); ?>

                        </h2>
                    </div>
                </div>
                <div class="col-lg-6">
                    <p class="wow fadeInUp" data-wow-delay=".5s">
                        <?php echo e($testimonialSectionDescription ?? 'Hear from our satisfied customers who trust A1 Airport Cars for reliable, comfortable, and on-time airport transfers. We pride ourselves on delivering a smooth travel experience from pickup to drop-off.'); ?>

                    </p>
                </div>
            </div>
        </div>
        <div class="swiper testimonial-slider">
            <div class="swiper-wrapper">
                <?php $__empty_1 = true; $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="swiper-slide">
                        <div class="testimonial-card-items">
                            <div class="testimoni-bg-shape">
                                <div class="testimonial-items-top">
                                    <p><?php echo e($testimonial->message); ?></p>
                                    <div class="star">
                                        <?php for($i = 0; $i < max(0, min(5, $testimonial->rating)); $i++): ?>
                                            <i class="fa-solid fa-star"></i>
                                        <?php endfor; ?>
                                        <?php for($i = 0; $i < max(0, 5 - min(5, $testimonial->rating)); $i++): ?>
                                            <i class="fa-regular fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="client-info-items d-flex align-items-center gap-3">
                                <div class="content">
                                    <h5><?php echo e($testimonial->author); ?></h5>
                                    <span><?php echo e($testimonial->company); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="swiper-slide">
                        <div class="testimonial-card-items">
                            <div class="testimoni-bg-shape">
                                <div class="testimonial-items-top">
                                    <p>No testimonials available at the moment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section><?php /**PATH /home/executiveairport/public_html/frontend/resources/views/partials/testimonials.blade.php ENDPATH**/ ?>