<section class="how-works-section fix pt-0">
    <div class="container">
        <div class="section-title text-center">
            <span class="wow fadeInUp" data-wow-delay=".2s">Simple 4 easy steps</span>
        </div>
        <div class="row">
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".<?php echo e(($index + 2) * 2); ?>s">
                    <div class="how-works-items">
                        <h6 class="title"><a href="<?php echo e($step->link ?? 'car-details'); ?>"><?php echo e($step->title); ?></a></h6>
                        <div class="icon-box">
                            <div class="icon">
                                <img src="<?php echo e(\App\Support\GalleryPath::path($step->icon1 ?? 'i/90')); ?>" alt="img" class="icon-1">
                                <img src="<?php echo e(\App\Support\GalleryPath::path($step->icon2 ?? 'i/91')); ?>" alt="img" class="icon-2">
                            </div>
                        </div>
                        <p><?php echo e($step->description); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<section class="feature-benefit section section-padding fix">
    <div class="container">
        <div class="section-title text-center">
            <h2 class="wow fadeInUp" data-wow-delay=".4s"><?php echo e($featureHeading ?? 'Why You Should Use A1 Airport Cars'); ?></h2>
        </div>
        <div class="row">
            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".<?php echo e(($loop->index + 3) * 2); ?>s">
                    <div class="feature-benefit-items">
                        <div class="icon-box-shape">
                            <img src="<?php echo e(\App\Support\GalleryPath::path('i/156'.$loop->iteration.'.png')); ?>" alt="shape-img">
                        </div>
                        <div class="bg-button-shape">
                            <img src="<?php echo e(\App\Support\GalleryPath::path('i/115')); ?>" alt="shape-img">
                        </div>
                        <div class="feature-content">
                            <h4><?php echo nl2br(e($feature->title)); ?></h4>
                            <p><?php echo e($feature->description); ?></p>
                            <div class="icon">
                                <img src="<?php echo e(\App\Support\GalleryPath::path($feature->icon ?? 'i/116')); ?>" alt="icon-img">
                            </div>
                        </div>
                        <div class="feature-button"></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>
    </div>
</section><?php /**PATH C:\xampp\htdocs\frontend\resources\views/partials/steps.blade.php ENDPATH**/ ?>