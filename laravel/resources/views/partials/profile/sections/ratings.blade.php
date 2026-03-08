<div class="profile-card">
    <div class="profile-card__title">Ratings --- nice to have  </div>

    <div class="rating-summary-wrap">
        <div class="rating-summary">
            <div class="rating-summary__score-block">
                <div class="rating-summary__score">{{ $average }}</div>
                <div class="rating-summary__label">Average rating</div>
            </div>

            <div class="rating-summary__meta">
                <div class="rating-summary__stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($average))
                            <i class="fa-solid fa-star"></i>
                        @elseif($i == ceil($average) && $average - floor($average) >= 0.5)
                            <i class="fa-solid fa-star-half-stroke"></i>
                        @else
                            <i class="fa-regular fa-star"></i>
                        @endif
                    @endfor
                </div>

                <div class="rating-summary__text">
                    Based on {{ $count }} {{ \Illuminate\Support\Str::plural('review', $count) }}
                </div>
            </div>

            <div class="rating-summary__extra">
                <div class="rating-summary__extra-title">Customer feedback</div>
                <div class="rating-summary__extra-text">
                    View your rating summary and the most recent feedback.
                </div>
            </div>
        </div>
    </div>

    <div class="profile-divider"></div>

    <div class="rating-list">
        @forelse(($ratings ?? []) as $rating)
            <div class="rating-item">
                <div class="rating-item__top">
                    <div class="rating-item__name">{{ $rating['name'] }}</div>
                    <div class="rating-item__date">{{ $rating['date'] }}</div>
                </div>

                <div class="rating-item__stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating['rating'])
                            <i class="fa-solid fa-star"></i>
                        @else
                            <i class="fa-regular fa-star"></i>
                        @endif
                    @endfor
                </div>

                <div class="rating-item__text">
                    {{ $rating['text'] }}
                </div>
            </div>
        @empty
            <div class="rating-item">
                <div class="rating-item__text">No reviews yet.</div>
            </div>
        @endforelse
    </div>
</div>