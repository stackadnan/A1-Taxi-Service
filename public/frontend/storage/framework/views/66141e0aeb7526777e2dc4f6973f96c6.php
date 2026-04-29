<!DOCTYPE html>
<html lang="en">
<?php $headTitle = $airportHeadTitle ?? 'A1 Airport Cars '; ?>
<?php
    $enabledPartials = $enabledPartials ?? [
        'head',
        'preloader',
        'scroll-up',
        'offcanvas',
        'header',
        'breadcrumb',
        'quotes',
        'testimonials',
        'why-us',
        'card-fleet',
        'steps',
        'card-blog',
        'faq',
        'footer',
        'script',
    ];
?>
<?php if(in_array('head', $enabledPartials, true)): ?>
<?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>


<body>

    <!-- Preloader Start -->
    <?php if(in_array('preloader', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.preloader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- Back To Top Start -->
    <?php if(in_array('scroll-up', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.scroll-up', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- Offcanvas Area Start -->
    <?php if(in_array('offcanvas', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.offcanvas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- Header Section Start -->
    <?php if(in_array('header', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <?php if(in_array('breadcrumb', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.breadcrumb', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    <!-- Search Area Start -->

    <!-- Hero Section Start -->
    <?php if(in_array('quotes', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.quotes', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- testimonials Section Start -->
    <?php if(in_array('testimonials', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.testimonials', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- Why us Start -->
    <?php if(in_array('why-us', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.why-us', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- content Section Start -->
    <section class="about-section fix section-padding">
        <div class="container">
            <div class="about-wrapper-2">
                <?php if(!empty($airportContentHtml)): ?>
                    <?php echo $airportContentHtml; ?>

                <?php else: ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="about-content pt-4">
                                <div class="section-title-content">
                                    <h3 class="wow fadeInUp" data-wow-delay=".4s">
                                        Content Not Configured
                                    </h3>
                                </div>
                                <p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">
                                    Add HTML in one_column, two_column, three_column and set number_of_rows like 1,2,3 or 1,2,1.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <!-- fleet Section Start -->
    <?php if(in_array('card-fleet', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.card-fleet', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- Gallery Section Start -->
    <?php if(in_array('steps', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.steps', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- News Section Start -->
    <?php if(in_array('card-blog', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.card-blog', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- Faq Section Start -->
    <?php if(in_array('faq', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.faq', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- footer cta Section Start -->
    <?php if(in_array('footer', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <?php if(in_array('script', $enabledPartials, true)): ?>
    <?php echo $__env->make('partials.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</body>

</html>
<?php /**PATH /home/executiveairport/public_html/frontend/resources/views/airport.blade.php ENDPATH**/ ?>