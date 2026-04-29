<header class="header-section">
    <?php ($baseUrl = rtrim(request()->getBaseUrl(), '/')); ?>
    <?php ($homeUrl = $baseUrl === '' ? '/' : $baseUrl.'/'); ?>

    <div class="header-top-section style-two">
        <div class="container-fluid">
            <div class="header-top-wrapper style-2">
                <ul class="contact-list">
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:<?php echo e($topEmail); ?>" class="link"><?php echo e($topEmail); ?></a>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo e($topAddress); ?>

                    </li>
                </ul>
                <div class="header-top-right">
                    <ul class="top-list">
                        <?php $__currentLoopData = $topLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><a href="<?php echo e($baseUrl.'/'.ltrim($link['url'], '/')); ?>"><?php echo e($link['label']); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <div class="social-icon d-flex align-items-center">
                        <?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e($social['url']); ?>"><i class="<?php echo e($social['icon']); ?>"></i></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="header-sticky" class="header-3">
        <div class="container-fluid">
            <div class="mega-menu-wrapper">
                <div class="header-main">
                    <div class="header-left">
                        <div class="logo">
                            <a href="<?php echo e($homeUrl); ?>" class="header-logo-1">
                                <img src="<?php echo e(\App\Support\GalleryPath::path($logoLight)); ?>" alt="logo-img">
                            </a>
                            <a href="<?php echo e($homeUrl); ?>" class="header-logo-2">
                                <img src="<?php echo e(\App\Support\GalleryPath::path($logoDark)); ?>" alt="logo-img">
                            </a>
                        </div>
                        <div class="mean__menu-wrapper">
                            <div class="main-menu">
                                <nav id="mobile-menu">
                                    <ul>
                                        <?php $__currentLoopData = ($navGroups ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php ($groupUrl = $baseUrl.'/'.ltrim($group['url'] ?? '/', '/')); ?>
                                            <li>
                                                <a href="<?php echo e($groupUrl); ?>">
                                                    <i class="<?php echo e($group['icon'] ?? 'fas fa-folder-open'); ?>"></i>
                                                    <?php echo e($group['label'] ?? 'Group'); ?>

                                                    <i class="fas fa-angle-down"></i>
                                                </a>
                                                <ul class="submenu <?php echo e($loop->index === 1 ? 'submenu-2' : ''); ?>">
                                                    <?php $__empty_1 = true; $__currentLoopData = ($group['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <li><a href="<?php echo e($baseUrl.'/'.ltrim($item['url'], '/')); ?>"><?php echo e($item['label']); ?></a></li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <li><a href="<?php echo e($groupUrl); ?>">No pages yet</a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <li><a href="<?php echo e($baseUrl.'/fleet'); ?>"><i class="fas fa-car"></i> Fleet</a></li>
                                        <li><a href="<?php echo e($baseUrl.'/faq'); ?>"><i class="fas fa-message"></i> FAQ's</a></li>
                                        <li><a href="<?php echo e($baseUrl.'/complainet/lost-found'); ?>"><i class="fas fa-box-open"></i> Complainet / Lost Found</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="header-right d-flex justify-content-end align-items-center">
                        <div class="icon-items">
                            <div class="icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="content">
                                <p><?php echo e($phoneLabel); ?></p>
                                <h6><a href="tel:<?php echo e(preg_replace('/\D+/', '', $phoneNumber)); ?>"><?php echo e($phoneNumber); ?></a></h6>
                            </div>
                        </div>
                        <div class="header-button">
                            <a href="<?php echo e(url($buttonLink)); ?>" class="theme-btn">
                                <?php echo e($buttonText); ?>

                            </a>
                        </div>
                        <div class="header__hamburger d-xl-none my-auto">
                            <div class="sidebar__toggle">
                                <i class="fas fa-bars"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header><?php /**PATH C:\xampp\htdocs\frontend\resources\views/partials/header.blade.php ENDPATH**/ ?>