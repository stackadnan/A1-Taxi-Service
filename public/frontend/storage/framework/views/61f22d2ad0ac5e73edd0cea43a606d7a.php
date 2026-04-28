 <section class="car-rentals-section section-padding fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="<?php echo e(\App\Support\GalleryPath::path('i/2')); ?>" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">Checkout our new cars</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    Our Fleet
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
                    <?php $__empty_1 = true; $__currentLoopData = $fleetItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="swiper-slide">
                            <div class="car-rentals-items">
                                <div class="car-image">
                                    <img src="<?php echo e(\App\Support\GalleryPath::path($item->image)); ?>" alt="<?php echo e($item->title); ?>">
                                </div>
                                <div class="car-content">
                                    <div class="post-cat">
                                        <?php echo e($item->category ?? $item->subtitle); ?>

                                    </div>
                                    <h4><a href="<?php echo e($item->link ?? 'car-details'); ?>"><?php echo e($item->title); ?></a></h4>
                                    <h6><?php echo e($item->description); ?>

                                        <?php if($item->passengers || $item->suitcases || $item->cabin_bags): ?>
                                            <span>
                                                These can accommodate
                                                <?php echo e($item->passengers ?? 0); ?> passengers,
                                                <?php echo e($item->suitcases ?? 0); ?> standard suitcases,
                                                <?php echo e($item->cabin_bags ?? 0); ?> cabin bags.
                                            </span>
                                        <?php endif; ?>
                                    </h6>
                                    <ul class="theme-btn bg-color w-100 text-center">
                                        <?php if($item->passengers): ?>
                                            <i class="fa-solid fa-users" style="color: red; margin-right: 5px;"></i>
                                            <?php echo e($item->passengers); ?> |
                                        <?php endif; ?>
                                        <?php if($item->suitcases): ?>
                                            <i class="fa-solid fa-suitcase" style="color: red; margin-right: 5px;"></i>
                                            <?php echo e($item->suitcases); ?> |
                                        <?php endif; ?>
                                        <?php if($item->cabin_bags): ?>
                                            <i class="fa-solid fa-suitcase-rolling" style="color: red; margin-right: 5px;"></i>
                                            <?php echo e($item->cabin_bags); ?>

                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="swiper-slide">
                            <div class="car-rentals-items">
                                <div class="car-content">
                                    <h4>No fleet items available yet.</h4>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php /**PATH C:\xampp\htdocs\frontend\resources\views/partials/card-fleet.blade.php ENDPATH**/ ?>