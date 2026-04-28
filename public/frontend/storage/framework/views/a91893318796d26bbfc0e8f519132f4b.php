<section class="faq-section fix section-padding">
        <div class="container">
            <div class="faq-wrapper">
                <div class="row g-4">
                    <!-- <div class="col-lg-6 wow fadeInUp" data-wow-delay=".4s">
                        <div class="faq-image">
                            <img src="<?php echo e(\App\Support\GalleryPath::path('i/143')); ?>" alt="img">
                            <div class="color-shape float-bob-y">
                                <img src="<?php echo e(\App\Support\GalleryPath::path('i/144')); ?>" alt="img">
                            </div>
                        </div>
                    </div> -->
                    <div class="col-lg-12">
                        <div class="faq-content">
                            <div class="section-title">
                                <img src="<?php echo e(\App\Support\GalleryPath::path('i/2')); ?>" alt="icon-img" class="wow fadeInUp">
                                <span class="wow fadeInUp" data-wow-delay=".2s"><?php echo e($faqSubtitle ?? 'Frequently asked questions'); ?></span>
                                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                                    <?php echo e($faqTitle ?? 'Question & Answers'); ?>

                                </h2>
                            </div>
                            <div class="row">
                                <div class="faq-accordion mt-4 mt-md-0 col-lg-6">
                                    <div class="accordion" id="accordion-left">
                                        <?php $__currentLoopData = $faqs->slice(0, ceil($faqs->count() / 2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="accordion-item mb-4 wow fadeInUp" data-wow-delay=".<?php echo e(3 + $index * 2); ?>s">
                                                <h5 class="accordion-header">
                                                    <button class="accordion-button <?php echo e($index !== 0 ? 'collapsed' : ''); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo e($index + 1); ?>" aria-expanded="<?php echo e($index === 0 ? 'true' : 'false'); ?>" aria-controls="faq<?php echo e($index + 1); ?>">
                                                        <?php echo e($faq->question); ?>

                                                    </button>
                                                </h5>
                                                <div id="faq<?php echo e($index + 1); ?>" class="accordion-collapse collapse<?php echo e($index === 0 ? ' show' : ''); ?>" data-bs-parent="#accordion-left">
                                                    <div class="accordion-body">
                                                        <?php echo e($faq->answer); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <div class="faq-accordion mt-4 mt-md-0 col-lg-6">
                                    <div class="accordion" id="accordion-right">
                                        <?php $__currentLoopData = $faqs->slice(ceil($faqs->count() / 2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="accordion-item mb-4 wow fadeInUp" data-wow-delay=".<?php echo e(3 + $index * 2); ?>s">
                                                <h5 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo e($index + 1 + ceil($faqs->count() / 2)); ?>" aria-expanded="false" aria-controls="faq<?php echo e($index + 1 + ceil($faqs->count() / 2)); ?>">
                                                        <?php echo e($faq->question); ?>

                                                    </button>
                                                </h5>
                                                <div id="faq<?php echo e($index + 1 + ceil($faqs->count() / 2)); ?>" class="accordion-collapse collapse" data-bs-parent="#accordion-right">
                                                    <div class="accordion-body">
                                                        <?php echo e($faq->answer); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><?php /**PATH C:\xampp\htdocs\frontend\resources\views/partials/faq.blade.php ENDPATH**/ ?>