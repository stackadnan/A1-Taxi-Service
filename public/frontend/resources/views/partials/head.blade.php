<head>
    @php
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
    @endphp
    <!-- ========== Meta Tags ========== -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="pixydrops">
    <meta name="description" content="{{ $metaDescription }}">
    @if($metaKeywords !== '')
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <meta name="robots" content="{{ $robots }}">
    <!-- ======== Page title ============ -->
    <title>{{ $metaTitle }}</title>
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if($ogImageUrl !== '')
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    @if($ogImageUrl !== '')
        <meta name="twitter:image" content="{{ $ogImageUrl }}">
    @endif
    <base href="{{ $baseHref }}">
    <!--<< Favcion >>-->
    <link rel="shortcut icon" href="{{ \App\Support\GalleryPath::asset('i/153') }}">
    <!--<< Bootstrap min.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!--<< All Min Css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <!--<< Animate.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <!--<< Magnific Popup.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
    <!--<< MeanMenu.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/meanmenu.css') }}">
    <!--<< DatePicker.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/datepickerboot.css') }}">
    <!--<< Swiper Bundle.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <!--<< Nice Select.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">

    {!! $css ?? '' !!}
    @if($schemaScript !== '')
        @if(str_starts_with(strtolower($schemaScript), '<script'))
            {!! $schemaScript !!}
        @else
            <script type="application/ld+json">{!! $schemaScript !!}</script>
        @endif
    @endif
    <!--<< Main.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}?v={{ @filemtime(public_path('assets/css/main.css')) }}">
</head>