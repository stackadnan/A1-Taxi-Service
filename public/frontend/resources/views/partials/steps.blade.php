<section class="how-works-section fix pt-0">
    <div class="container">
        <div class="section-title text-center">
            <span class="wow fadeInUp" data-wow-delay=".2s">Simple 4 easy steps</span>
        </div>
        <div class="row">
            @foreach($steps as $index => $step)
                <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".{{ ($index + 2) * 2 }}s">
                    <div class="how-works-items">
                        <h6 class="title"><a href="{{ $step->link ?? 'car-details' }}">{{ $step->title }}</a></h6>
                        <div class="icon-box">
                            <div class="icon">
                                <img src="{{ $step->icon1 ?? 'assets/img/how-work/icon-1.png' }}" alt="img" class="icon-1">
                                <img src="{{ $step->icon2 ?? 'assets/img/how-work/icon-11.png' }}" alt="img" class="icon-2">
                            </div>
                        </div>
                        <p>{{ $step->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<section class="feature-benefit section section-padding fix">
    <div class="container">
        <div class="section-title text-center">
            <h2 class="wow fadeInUp" data-wow-delay=".4s">{{ $featureHeading ?? 'Why You Should Use A1 Airport Cars' }}</h2>
        </div>
        <div class="row">
            @foreach($features as $feature)
                <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".{{ ($loop->index + 3) * 2 }}s">
                    <div class="feature-benefit-items">
                        <div class="icon-box-shape">
                            <img src="assets/img/feature-benefit/box-icon-bg{{ $loop->iteration }}.png" alt="shape-img">
                        </div>
                        <div class="bg-button-shape">
                            <img src="assets/img/feature-benefit/bg-button-iconbox.png" alt="shape-img">
                        </div>
                        <div class="feature-content">
                            <h4>{!! nl2br(e($feature->title)) !!}</h4>
                            <p>{{ $feature->description }}</p>
                            <div class="icon">
                                <img src="{{ $feature->icon ?? 'assets/img/feature-benefit/icon-1.png' }}" alt="icon-img">
                            </div>
                        </div>
                        <div class="feature-button"></div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>