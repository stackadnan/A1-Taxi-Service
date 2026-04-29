<!--<< Breadcrumb Section Start >>-->
<div class="breadcrumb-wrapper bg-cover" style="background-image: url(<?php echo e(\App\Support\GalleryPath::path($img ?? 'i/151')); ?>);">
    <div class="container">
        <div class="page-heading">
            <ul class="breadcrumb-items wow fadeInUp" data-wow-delay=".3s">
                <li>
                    <a href="<?php echo e(url('/')); ?>">
                        <?php echo e($Title ?? 'Home'); ?>

                    </a>
                </li>
                <li>
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li>
                    <?php echo e($Title2 ?? ''); ?>

                </li>
            </ul>
            <h1 class="wow fadeInUp" data-wow-delay=".5s"><?php echo e($SubTitle ?? ''); ?></h1>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\frontend\resources\views/partials/breadcrumb.blade.php ENDPATH**/ ?>