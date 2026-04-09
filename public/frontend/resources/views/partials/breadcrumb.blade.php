<!--<< Breadcrumb Section Start >>-->
<div class="breadcrumb-wrapper bg-cover" style="background-image: url({{ $img ?? 'assets/img/breadcrumb-banner.png' }});">
    <div class="container">
        <div class="page-heading">
            <ul class="breadcrumb-items wow fadeInUp" data-wow-delay=".3s">
                <li>
                    <a href="{{ url('/') }}">
                        {{ $Title ?? 'Home' }}
                    </a>
                </li>
                <li>
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li>
                    {{ $Title2 ?? '' }}
                </li>
            </ul>
            <h1 class="wow fadeInUp" data-wow-delay=".5s">{{ $SubTitle ?? '' }}</h1>
        </div>
    </div>
</div>