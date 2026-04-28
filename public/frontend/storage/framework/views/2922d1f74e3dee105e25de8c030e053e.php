<section class="news-section fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="<?php echo e(\App\Support\GalleryPath::path('i/2')); ?>" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">From the Blog</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    Latest News & <br>
                    Articles From the Blog
                </h2>
            </div>
            <div class="row">
                <?php $__empty_1 = true; $__currentLoopData = $blogItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                        <div class="news-card-items">
                            <div class="news-image">
                                <img src="<?php echo e(\App\Support\GalleryPath::path($item->image)); ?>" alt="news-img">
                                <div class="post-date">
                                    <h6>
                                        <?php echo e($item->post_date->format('d')); ?> <br>
                                        <?php echo e($item->post_date->format('M')); ?>

                                    </h6>
                                </div>
                            </div>
                            <div class="news-content">
                                <div class="post-client">
                                    <img src="<?php echo e(\App\Support\GalleryPath::path('i/63')); ?>" alt="img">
                                </div>
                                <div class="news-cont">
                                    <span>by <?php echo e($item->author); ?></span>
                                    <h3><a href="<?php echo e($item->link); ?>"><?php echo e($item->title); ?></a></h3>
                                    <p><?php echo e($item->body); ?></p>
                                </div>
                                <ul>
                                    <li>
                                        <i class="fa-solid fa-comments"></i>
                                        <?php echo e($item->comments); ?> Comments
                                    </li>
                                    <li>
                                        <a href="<?php echo e($item->link); ?>">
                                            <i class="fa-solid fa-arrow-right-long"></i>
                                            More
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <p>No blog articles available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php /**PATH /home/executiveairport/public_html/frontend/resources/views/partials/card-blog.blade.php ENDPATH**/ ?>