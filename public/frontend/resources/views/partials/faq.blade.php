<section class="faq-section fix section-padding">
        <div class="container">
            <div class="faq-wrapper">
                <div class="row g-4">
                    <!-- <div class="col-lg-6 wow fadeInUp" data-wow-delay=".4s">
                        <div class="faq-image">
                            <img src="assets/img/faq.png" alt="img">
                            <div class="color-shape float-bob-y">
                                <img src="assets/img/faq-color-shape.png" alt="img">
                            </div>
                        </div>
                    </div> -->
                    <div class="col-lg-12">
                        <div class="faq-content">
                            <div class="section-title">
                                <img src="assets/img/sub-icon.png" alt="icon-img" class="wow fadeInUp">
                                <span class="wow fadeInUp" data-wow-delay=".2s">{{ $faqSubtitle ?? 'Frequently asked questions' }}</span>
                                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                                    {{ $faqTitle ?? 'Question & Answers' }}
                                </h2>
                            </div>
                            <div class="row">
                                <div class="faq-accordion mt-4 mt-md-0 col-lg-6">
                                    <div class="accordion" id="accordion-left">
                                        @foreach($faqs->slice(0, ceil($faqs->count() / 2)) as $index => $faq)
                                            <div class="accordion-item mb-4 wow fadeInUp" data-wow-delay=".{{ 3 + $index * 2 }}s">
                                                <h5 class="accordion-header">
                                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq{{ $index + 1 }}">
                                                        {{ $faq->question }}
                                                    </button>
                                                </h5>
                                                <div id="faq{{ $index + 1 }}" class="accordion-collapse collapse{{ $index === 0 ? ' show' : '' }}" data-bs-parent="#accordion-left">
                                                    <div class="accordion-body">
                                                        {{ $faq->answer }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="faq-accordion mt-4 mt-md-0 col-lg-6">
                                    <div class="accordion" id="accordion-right">
                                        @foreach($faqs->slice(ceil($faqs->count() / 2)) as $index => $faq)
                                            <div class="accordion-item mb-4 wow fadeInUp" data-wow-delay=".{{ 3 + $index * 2 }}s">
                                                <h5 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 + ceil($faqs->count() / 2) }}" aria-expanded="false" aria-controls="faq{{ $index + 1 + ceil($faqs->count() / 2) }}">
                                                        {{ $faq->question }}
                                                    </button>
                                                </h5>
                                                <div id="faq{{ $index + 1 + ceil($faqs->count() / 2) }}" class="accordion-collapse collapse" data-bs-parent="#accordion-right">
                                                    <div class="accordion-body">
                                                        {{ $faq->answer }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>