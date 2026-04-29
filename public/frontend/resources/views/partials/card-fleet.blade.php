 <section class="car-rentals-section section-padding fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">Checkout our new cars</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    Our Fleet
                </h2>
            </div>
        </div>
        <div class="car-rentals-wrapper">
            <div class="array-button">
                <button class="array-prev"><i class="far fa-chevron-left"></i></button>
                <button class="array-next"><i class="far fa-chevron-right"></i></button>
            </div>
            <div class="swiper car-rentals-slider">
                <div class="swiper-wrapper">
                    @forelse ($fleetItems as $item)
                        <div class="swiper-slide">
                            <div class="car-rentals-items">
                                <div class="car-image">
                                    <img src="{{ \App\Support\GalleryPath::path($item->image) }}" alt="{{ $item->title }}">
                                </div>
                                <div class="car-content">
                                    <div class="post-cat">
                                        {{ $item->category ?? $item->subtitle }}
                                    </div>
                                    <h4><a href="{{ $item->link ?? 'car-details' }}">{{ $item->title }}</a></h4>
                                    <h6>{{ $item->description }}
                                        @if ($item->passengers || $item->suitcases || $item->cabin_bags)
                                            <span>
                                                These can accommodate
                                                {{ $item->passengers ?? 0 }} passengers,
                                                {{ $item->suitcases ?? 0 }} standard suitcases,
                                                {{ $item->cabin_bags ?? 0 }} cabin bags.
                                            </span>
                                        @endif
                                    </h6>
                                    <ul class="theme-btn bg-color w-100 text-center">
                                        @if($item->passengers)
                                            <i class="fa-solid fa-users" style="color: red; margin-right: 5px;"></i>
                                            {{ $item->passengers }} |
                                        @endif
                                        @if($item->suitcases)
                                            <i class="fa-solid fa-suitcase" style="color: red; margin-right: 5px;"></i>
                                            {{ $item->suitcases }} |
                                        @endif
                                        @if($item->cabin_bags)
                                            <i class="fa-solid fa-suitcase-rolling" style="color: red; margin-right: 5px;"></i>
                                            {{ $item->cabin_bags }}
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="car-rentals-items">
                                <div class="car-content">
                                    <h4>No fleet items available yet.</h4>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

