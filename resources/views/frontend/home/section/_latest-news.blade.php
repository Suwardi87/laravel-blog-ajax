<!-- Latest News Start -->
<div class="container-fluid latest-news py-5">
    <div class="container py-5">
        <h2 class="mb-4"> Article Terbaru</h2>
        <div class="latest-news-carousel owl-carousel">
            @foreach ($latestArticles as $article)
            <div class="latest-news-item">
                <div class="bg-light rounded">
                    <div class="rounded-top overflow-hidden">
                        <img src="{{ asset('storage/images/' . $article->image) }}"
                            class="img-zoomin img-fluid rounded-top w-100" alt="">
                    </div>
                    <div class="d-flex flex-column p-4">
                        <a href="#" class="h4">{{ $article->title }}</a>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="small text-body link-hover">{{ $article->user->name }}</a>
                            <small class="text-body d-block"><i class="fas fa-calendar-alt me-1"></i> {{ date('Y-m-d H:i:s', $article->created_at) }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
    </div>
</div>
<!-- Latest News End -->
