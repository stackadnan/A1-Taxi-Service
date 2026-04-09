<section class="testimonial-section fix section-padding">
    <div class="testimonial-bg-shape">
        <img src="assets/img/testimonial/testimonial-bg.jpg" alt="shape-img">
    </div>
    <div class="container">
        <div class="section-title-area">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="section-title">
                        <img src="assets/img/sub-icon.png" alt="icon-img" class="wow fadeInUp">
                        <span class="wow fadeInUp" data-wow-delay=".2s">{{ $testimonialSectionTitle ?? 'Our Testimonials' }}</span>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">
                            {{ $testimonialSectionHeading ?? 'What They’re Saying About A1 Airport Cars' }}
                        </h2>
                    </div>
                </div>
                <div class="col-lg-6">
                    <p class="wow fadeInUp" data-wow-delay=".5s">
                        {{ $testimonialSectionDescription ?? 'Hear from our satisfied customers who trust A1 Airport Cars for reliable, comfortable, and on-time airport transfers. We pride ourselves on delivering a smooth travel experience from pickup to drop-off.' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="swiper testimonial-slider">
            <div class="swiper-wrapper">
                @forelse($testimonials as $testimonial)
                    <div class="swiper-slide">
                        <div class="testimonial-card-items">
                            <div class="testimoni-bg-shape">
                                <div class="testimonial-items-top">
                                    <p>{{ $testimonial->message }}</p>
                                    <div class="star">
                                        @for ($i = 0; $i < max(0, min(5, $testimonial->rating)); $i++)
                                            <i class="fa-solid fa-star"></i>
                                        @endfor
                                        @for ($i = 0; $i < max(0, 5 - min(5, $testimonial->rating)); $i++)
                                            <i class="fa-regular fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="client-info-items d-flex align-items-center gap-3">
                                <div class="content">
                                    <h5>{{ $testimonial->author }}</h5>
                                    <span>{{ $testimonial->company }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="swiper-slide">
                        <div class="testimonial-card-items">
                            <div class="testimoni-bg-shape">
                                <div class="testimonial-items-top">
                                    <p>No testimonials available at the moment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>