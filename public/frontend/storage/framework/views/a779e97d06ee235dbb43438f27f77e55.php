<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pages</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Page Manager</h1>
            <p class="text-muted mb-0">Manage dynamic page content and URL mappings.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="<?php echo e(url('/')); ?>" target="_blank">Open Website</a>
            <form action="<?php echo e(route('admin.logout')); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
            <a class="btn btn-primary" href="<?php echo e(route('admin.pages.create')); ?>">Add Page</a>
        </div>
    </div>

    <?php if(session('status')): ?>
        <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Rows Pattern</th>
                    <th>Primary URL</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($primaryUrl = $page->urls->first()); ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?php echo e($page->name); ?></div>
                            <small class="text-muted">ID: <?php echo e($page->id); ?></small>
                        </td>
                        <td><code><?php echo e($page->number_of_rows ?: '1'); ?></code></td>
                        <td>
                            <?php if($primaryUrl): ?>
                                <a href="<?php echo e(url('/'.$primaryUrl->group_slug.'/'.$primaryUrl->slug)); ?>" target="_blank">
                                    /<?php echo e($primaryUrl->group_slug); ?>/<?php echo e($primaryUrl->slug); ?>

                                </a>
                                <?php if(!$primaryUrl->is_active): ?>
                                    <span class="badge text-bg-warning ms-1">Inactive</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Not mapped</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('admin.pages.edit', $page)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="<?php echo e(route('admin.pages.destroy', $page)); ?>" method="POST" onsubmit="return confirm('Delete this page?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No pages found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <?php echo e($pages->links()); ?>

    </div>
</div>
</body>
</html>
<?php /**PATH /home/executiveairport/public_html/frontend/resources/views/admin/pages/index.blade.php ENDPATH**/ ?>