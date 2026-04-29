<head>
    <?php
        $baseHref = rtrim(request()->getBaseUrl(), '/').'/';
        $defaultTitleSuffix = 'Best option for Premium airport transfer services';
        $defaultDescription = 'A1 Airport Cars - Best option for Premium airport transfer services';

        $rawHeadTitle = is_string($headTitle ?? null) ? trim($headTitle) : '';
        $metaTitle = is_string($seoMetaTitle ?? null) ? trim($seoMetaTitle) : '';
        $metaDescription = is_string($seoMetaDescription ?? null) ? trim($seoMetaDescription) : '';
        $metaKeywords = is_string($seoMetaKeywords ?? null) ? trim($seoMetaKeywords) : '';
        $canonical = is_string($seoCanonical ?? null) ? trim($seoCanonical) : '';
        $schemaScript = is_string($seoSchemaScript ?? null) ? trim($seoSchemaScript) : '';
        $robots = is_string($seoRobots ?? null) ? trim($seoRobots) : '';
        $ogTitle = is_string($seoOgTitle ?? null) ? trim($seoOgTitle) : '';
        $ogDescription = is_string($seoOgDescription ?? null) ? trim($seoOgDescription) : '';
        $ogImage = is_string($seoOgImage ?? null) ? trim($seoOgImage) : '';

        if ($metaTitle === '') {
            $metaTitle = $rawHeadTitle !== ''
                ? $rawHeadTitle.' - '.$defaultTitleSuffix
                : 'A1 Airport Cars - '.$defaultTitleSuffix;
        }

        if ($metaDescription === '') {
            $metaDescription = $defaultDescription;
        }

        if ($canonical === '') {
            $canonical = url()->current();
        }

        if ($robots === '') {
            $robots = 'index,follow';
        }

        if ($ogTitle === '') {
            $ogTitle = $metaTitle;
        }

        if ($ogDescription === '') {
            $ogDescription = $metaDescription;
        }

        $ogImageUrl = '';
        if ($ogImage !== '') {
            $ogImageUrl = \App\Support\GalleryPath::asset($ogImage);

            if (!str_starts_with($ogImageUrl, 'http://') && !str_starts_with($ogImageUrl, 'https://')) {
                $ogImageUrl = url($ogImageUrl);
            }
        }
    ?>
    <!-- ========== Meta Tags ========== -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="author" content="pixydrops">
    <meta name="description" content="<?php echo e($metaDescription); ?>">
    <?php if($metaKeywords !== ''): ?>
        <meta name="keywords" content="<?php echo e($metaKeywords); ?>">
    <?php endif; ?>
    <meta name="robots" content="<?php echo e($robots); ?>">
    <!-- ======== Page title ============ -->
    <title><?php echo e($metaTitle); ?></title>
    <link rel="canonical" href="<?php echo e($canonical); ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($ogTitle); ?>">
    <meta property="og:description" content="<?php echo e($ogDescription); ?>">
    <meta property="og:url" content="<?php echo e($canonical); ?>">
    <?php if($ogImageUrl !== ''): ?>
        <meta property="og:image" content="<?php echo e($ogImageUrl); ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($ogTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($ogDescription); ?>">
    <?php if($ogImageUrl !== ''): ?>
        <meta name="twitter:image" content="<?php echo e($ogImageUrl); ?>">
    <?php endif; ?>
    <base href="<?php echo e($baseHref); ?>">
    <!--<< Favcion >>-->
    <link rel="shortcut icon" href="<?php echo e(\App\Support\GalleryPath::asset('i/153')); ?>">
    <!--<< Bootstrap min.css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <!--<< All Min Css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/all.min.css')); ?>">
    <!--<< Animate.css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/animate.css')); ?>">
    <!--<< Magnific Popup.css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/magnific-popup.css')); ?>">
    <!--<< MeanMenu.css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/meanmenu.css')); ?>">
    <!--<< DatePicker.css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/datepickerboot.css')); ?>">
    <!--<< Swiper Bundle.css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/swiper-bundle.min.css')); ?>">
    <!--<< Nice Select.css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/nice-select.css')); ?>">

    <?php echo $css ?? ''; ?>

    <?php if($schemaScript !== ''): ?>
        <?php if(str_starts_with(strtolower($schemaScript), '<script')): ?>
            <?php echo $schemaScript; ?>

        <?php else: ?>
            <script type="application/ld+json"><?php echo $schemaScript; ?></script>
        <?php endif; ?>
    <?php endif; ?>
    <!--<< Main.css >>-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/main.css')); ?>?v=<?php echo e(@filemtime(public_path('assets/css/main.css'))); ?>">
</head><?php /**PATH /home/executiveairport/public_html/frontend/resources/views/partials/head.blade.php ENDPATH**/ ?>