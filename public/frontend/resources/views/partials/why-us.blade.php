<section class="about-section fix section-padding">
    <div class="container">
        <div class="about-wrapper-2">
            <div class="row g-4">

                <div class="col-lg-12">
                    <div class="about-content">
                        <div class="section-title-content">
                            <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                            <span class="wow fadeInUp" data-wow-delay=".2s">{{ $sectionTitle ?? 'Why Choose Us' }}</span>
                            <h3 class="wow fadeInUp" data-wow-delay=".4s">
                                {{ $sectionSubtitle ?? 'Why Book an Airport Taxi with Us?' }}
                            </h3>
                        </div>

                        <div class="about-list-wrapper">

                            <!-- LEFT COLUMN -->
                            <div class="about-list-items wow fadeInUp col-md-8" data-wow-delay=".3s">
                                <div class="row">

                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            @foreach(array_slice($leftItems ?? [], 0, ceil(count($leftItems ?? []) / 2)) as $item)
                                                <li>
                                                    <i class="fa-solid fa-arrow-right"></i>
                                                    {{ $item }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            @foreach(array_slice($leftItems ?? [], ceil(count($leftItems ?? []) / 2)) as $item)
                                                <li>
                                                    <i class="fa-solid fa-arrow-right"></i>
                                                    {{ $item }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </div>
                            </div>

                            <!-- RIGHT COLUMN -->
                            <div class="author-items wow fadeInUp col-md-4" data-wow-delay=".5s">
                                <ul class="list-unstyled">
                                    @foreach($rightItems ?? [] as $item)
                                        <li>
                                            <i class="fa-solid fa-arrow-right"></i>
                                            {!! $item !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>