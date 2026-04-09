<section class="news-section fix">
        <div class="container">
            <div class="section-title text-center">
                <img src="assets/img/sub-icon.png" alt="icon-img" class="wow fadeInUp">
                <span class="wow fadeInUp" data-wow-delay=".2s">From the Blog</span>
                <h2 class="wow fadeInUp" data-wow-delay=".4s">
                    Latest News & <br>
                    Articles From the Blog
                </h2>
            </div>
            <div class="row">
                @forelse($blogItems as $item)
                    <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
                        <div class="news-card-items">
                            <div class="news-image">
                                <img src="{{ $item->image }}" alt="news-img">
                                <div class="post-date">
                                    <h6>
                                        {{ $item->post_date->format('d') }} <br>
                                        {{ $item->post_date->format('M') }}
                                    </h6>
                                </div>
                            </div>
                            <div class="news-content">
                                <div class="post-client">
                                    <img src="assets/img/news/client.png" alt="img">
                                </div>
                                <div class="news-cont">
                                    <span>by {{ $item->author }}</span>
                                    <h3><a href="{{ $item->link }}">{{ $item->title }}</a></h3>
                                    <p>{{ $item->body }}</p>
                                </div>
                                <ul>
                                    <li>
                                        <i class="fa-solid fa-comments"></i>
                                        {{ $item->comments }} Comments
                                    </li>
                                    <li>
                                        <a href="{{ $item->link }}">
                                            <i class="fa-solid fa-arrow-right-long"></i>
                                            More
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p>No blog articles available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

