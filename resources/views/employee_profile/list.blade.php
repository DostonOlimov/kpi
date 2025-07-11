@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/employee_list.css') }}">

@section('content')
    <div class="results-container">
        <div class="container-fluid">
            <!-- Sahifa Sarlavhasi -->
            <div class="page-header mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fa fa-user-circle mr-1"></i>&nbsp; Xodimning shaxsiy ko‘rsatkichlari
                    </li>
                </ol>
            </div>

            <!-- Umumiy Natijalar -->
            <div class="performance-overview fade-in">
                <div class="performance-card">
                    <span class="performance-icon">📝</span>
                    <div class="performance-number">{{ $userStats['total_kpis'] ?? 0 }}</div>
                    <div class="performance-label">Biriktirilgan vazifalar</div>
                    <div class="performance-description">Siz bajargan umumiy vazifalar soni</div>
                </div>
                <div class="performance-card">
                    <span class="performance-icon">✅</span>
                    <div class="performance-number">{{ $userStats['total_tasks'] ?? 0 }}</div>
                    <div class="performance-label">Bajarilgan vazifalar</div>
                    <div class="performance-description">Siz bajargan umumiy vazifalar soni</div>
                </div>

                {{-- <div class="performance-card">
                    <span class="performance-icon">💬</span>
                    <div class="performance-number">{{ $userStats['reviewed_tasks'] ?? 0 }}</div>
                    <div class="performance-label">Ko‘rib chiqilgan vazifalar</div>
                    <div class="performance-description">Komissar fikri bildirilgan vazifalar</div>
                </div> --}}

                <div class="performance-card">
                    <span class="performance-icon">⭐</span>
                    <div class="performance-number">{{ $userStats['scored_kpis'] ?? 0 }}</div>
                    <div class="performance-label">Baholangan KPI lar</div>
                    <div class="performance-description">Rasmiy baho berilgan kategoriyalar</div>
                </div>

                <div class="performance-card overall-score-card">
                    <div class="score-circle" style="--score: {{ $userStats['average_score'] ?? 0 }}">
                        <div class="score-text">{{ number_format($userStats['average_score'] ?? 0, 1) }}%</div>
                    </div>
                    <div class="performance-label">Umumiy baho</div>
                    <div class="performance-description">Barcha KPI lar bo‘yicha o‘rtacha natijangiz</div>
                </div>
            </div>

            <!-- Rivojlanish -->
            <div class="progress-section fade-in">
                <h3 class="progress-title">Ish faoliyati rivojlanishi</h3>

                <div class="progress-item">
                    <div class="progress-label">
                        <span>Vazifalarni bajarish darajasi</span>
                        <span>{{ $userStats['completion_rate'] ?? 0 }}%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: {{ $userStats['completion_rate'] ?? 0 }}%"></div>
                    </div>
                </div>

                <div class="progress-item">
                    <div class="progress-label">
                        <span>Ko‘rib chiqish darajasi</span>
                        <span>{{ $userStats['review_rate'] ?? 0 }}%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar {{ $userStats['review_rate'] >= 70 ? '' : ($userStats['review_rate'] >= 40 ? 'medium' : 'low') }}"
                             style="width: {{ $userStats['review_rate'] ?? 0 }}%"></div>
                    </div>
                </div>

                <div class="progress-item">
                    <div class="progress-label">
                        <span>Baholash jarayoni</span>
                        <span>{{ $userStats['scoring_progress'] ?? 0 }}%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar {{ $userStats['scoring_progress'] >= 70 ? '' : ($userStats['scoring_progress'] >= 40 ? 'medium' : 'low') }}"
                             style="width: {{ $userStats['scoring_progress'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- KPI Natijalari -->
            <div class="kpi-results-section fade-in">
                <div class="kpi-results-header">
                    <h2 class="kpi-results-title">KPI Kategoriyasi Natijalari</h2>
                </div>

                @foreach ($constant_kpis as $category)
                    <div class="category-result-card">
                        <div class="category-result-header" data-bs-toggle="collapse" data-bs-target="#category-{{ $category->id }}">
                            <div class="category-info" style="width: 60%">
                                <h4>{{ $category->name }}</h4>
                            </div>
                            <div style="width: 20%"></div>
                            <div class="category-stats" style="width: 20%">
                                <div class="task-count">
                                    {{--                                    <div>{{ $category->user_tasks_count ?? 0 }}</div>--}}
                                    <div class="score-label">Vazifalar</div>
                                </div>
                                <div class="category-score">
                                    {{--                                    @if(array_key_exists('average_score',$category))--}}
                                    {{--                                        @php $averageScore = $category['average_score']; @endphp--}}
                                    {{--                                        <span class="score-badge {{ $averageScore >= 70 ? '' : ($averageScore >= 40 ? 'medium-score' : 'low-score') }}">--}}
                                    {{--                                            --}}{{-- {{ number_format($category['total_ball'], 1) }}/{{ number_format($category['max_ball'], 1) }} --}}
                                    {{--                                             {{number_format($averageScore)}}%--}}
                                    {{--                                        </span>--}}
                                    {{--                                    @else--}}
                                    {{--                                        <span class="score-badge no-score">Baholanmagan</span>--}}
                                    {{--                                    @endif--}}
                                    <div class="score-label">O‘rtacha baho</div>
                                </div>
                                <span class="expand-icon">▼</span>
                            </div>
                        </div>

                        <div class="category-details collapse" id="category-{{ $category->id }}">
                            @foreach ($category->children as $child)
                                <div style="margin-bottom: 2rem; padding: 1.5rem; background: white; border-radius: 8px; border-left: 4px solid #007bff;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                        <h2 style="color: #495057; margin: 0;">{{ $child->name }}</h2>
                                        <div style="text-align: right;">
                                            {{--                                            @if($child->score?->score)--}}
                                            {{--                                                <span class="score-badge {{ $child->score->score / $child->max_score >= 0.7 ? '' : ($child->score->score/$child->max_score >= 0.4 ? 'medium-score' : 'low-score') }}">--}}
                                            {{--                                                {{ round($child->score->score) }}/{{ $child->max_score }}--}}
                                            {{--                                            </span>--}}
                                            {{--                                            @else--}}
                                            {{--                                                <span class="score-badge no-score">Baholanmagan</span>--}}
                                            {{--                                            @endif--}}
                                        </div>
                                    </div>
                                    {{--                                    @if($child->score?->feedback)--}}
                                    {{--                                        <div class="feedback-section">--}}
                                    {{--                                            <h6 class="feedback-title">Baxo bo'yicha xulosa</h6>--}}
                                    {{--                                            <p class="feedback-text">{{ $child->score->feedback }}</p>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    @endif--}}

                                    {{--                                    @if($child->type == 1)--}}
                                    {{--                                        @php $user_tasks = $child->user_tasks->where('user_id', $userId)->first(); @endphp--}}
                                    {{--                                        <div class="tasks-section">--}}
                                    {{--                                        <h6 class="tasks-title">Siz bajargan vazifalar ({{ $user_tasks?->tasks?->count() }})</h6>--}}

                                    {{--                                        @forelse ($user_tasks->tasks as $task)--}}
                                    {{--                                            <div class="task-result-card">--}}
                                    {{--                                                <div class="task-result-header">--}}
                                    {{--                                                    <h6 class="task-title">{{ $task->name }}</h6>--}}
                                    {{--                                                    <span class="task-status {{ $task->comments->count() > 0 ? 'status-reviewed' : 'status-pending' }}">--}}
                                    {{--                                                    {{ $task->comments->count() > 0 ? '✅ Ko‘rib chiqilgan' : '⏳ Ko‘rib chiqilmoqda' }}--}}
                                    {{--                                                </span>--}}
                                    {{--                                                </div>--}}

                                    {{--                                                <p class="task-description">{{ $task->description }}</p>--}}

                                    {{--                                                @if ($task->file_path)--}}
                                    {{--                                                    <a href="{{ asset('storage/' . $task->file_path) }}"--}}
                                    {{--                                                       target="_blank"--}}
                                    {{--                                                       class="task-file">--}}
                                    {{--                                                        📎 Biriktirilgan fayl--}}
                                    {{--                                                    </a>--}}
                                    {{--                                                @endif--}}

                                    {{--                                                @if($task->comments->count() > 0)--}}
                                    {{--                                                    <div class="comments-section">--}}
                                    {{--                                                        <h6 class="comments-title">Komissar izohlari</h6>--}}

                                    {{--                                                        @foreach ($task->comments as $comment)--}}
                                    {{--                                                            <div class="comment-item">--}}
                                    {{--                                                                <div class="comment-header">--}}
                                    {{--                                                                    <span class="comment-author">{{ $comment->user->name ?? 'Komissar' }}</span>--}}
                                    {{--                                                                    <span class="comment-date">{{ $comment->created_at->format('d M, Y H:i') }}</span>--}}
                                    {{--                                                                </div>--}}
                                    {{--                                                                <p class="comment-text">{{ $comment->comment }}</p>--}}
                                    {{--                                                            </div>--}}
                                    {{--                                                        @endforeach--}}
                                    {{--                                                    </div>--}}
                                    {{--                                                @else--}}
                                    {{--                                                    <div class="no-comments">--}}
                                    {{--                                                        <p>Hozircha komissar izohlari mavjud emas. Vazifa ko‘rib chiqilmoqda.</p>--}}
                                    {{--                                                    </div>--}}
                                    {{--                                                @endif--}}
                                    {{--                                            </div>--}}
                                    {{--                                        @empty--}}
                                    {{--                                            <div class="text-center py-4 text-muted">--}}
                                    {{--                                                <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>--}}
                                    {{--                                                <p>Bu KPI bo‘yicha hali hech qanday vazifa topshirmagansiz.</p>--}}
                                    {{--                                                <a href="#" class="btn btn-primary btn-modern">--}}
                                    {{--                                                    Birinchi vazifani topshirish--}}
                                    {{--                                                </a>--}}
                                    {{--                                            </div>--}}
                                    {{--                                        @endforelse--}}
                                    {{--                                    </div>--}}
                                    {{--                                    @endif--}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @foreach ($constant_kpis as $category)
                    <div class="category-result-card">
                        <div class="category-result-header" data-bs-toggle="collapse" data-bs-target="#category-{{ $category->id }}">
                            <div class="category-info" style="width: 60%">
                                <h4>{{ $category->name }}</h4>
                            </div>
                            <div style="width: 20%"></div>
                            <div class="category-stats" style="width: 20%">
                                <div class="task-count">
                                    <div>{{ $category->user_tasks_count ?? 0 }}</div>
                                    <div class="score-label">Vazifalar</div>
                                </div>
                                <div class="category-score">
                                    @if(array_key_exists('average_score',$category))
                                        @php $averageScore = $category['average_score']; @endphp
                                        <span class="score-badge {{ $averageScore >= 70 ? '' : ($averageScore >= 40 ? 'medium-score' : 'low-score') }}">
                                             {{ number_format($category['total_ball'], 1) }}/{{ number_format($category['max_ball'], 1) }}
                                             {{number_format($averageScore)}}%
                                        </span>
                                    @else
                                        <span class="score-badge no-score">Baholanmagan</span>
                                    @endif
                                    <div class="score-label">O‘rtacha baho</div>
                                </div>
                                    <span class="expand-icon">▼</span>
                            </div>
                        </div>

                        <div class="category-details collapse" id="category-{{ $category->id }}">
                            @foreach ($category->children as $child)
                                <div style="margin-bottom: 2rem; padding: 1.5rem; background: white; border-radius: 8px; border-left: 4px solid #007bff;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                        <h2 style="color: #495057; margin: 0;">{{ $child->name }}</h2>
                                        <div style="text-align: right;">
{{--                                            @if($child->score?->score)--}}
{{--                                                <span class="score-badge {{ $child->score->score / $child->max_score >= 0.7 ? '' : ($child->score->score/$child->max_score >= 0.4 ? 'medium-score' : 'low-score') }}">--}}
{{--                                                {{ round($child->score->score) }}/{{ $child->max_score }}--}}
{{--                                            </span>--}}
{{--                                            @else--}}
{{--                                                <span class="score-badge no-score">Baholanmagan</span>--}}
{{--                                            @endif--}}
                                        </div>
                                    </div>
{{--                                    @if($child->score?->feedback)--}}
{{--                                        <div class="feedback-section">--}}
{{--                                            <h6 class="feedback-title">Baxo bo'yicha xulosa</h6>--}}
{{--                                            <p class="feedback-text">{{ $child->score->feedback }}</p>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}

{{--                                    @if($child->type == 1)--}}
{{--                                        @php $user_tasks = $child->user_tasks->where('user_id', $userId)->first(); @endphp--}}
{{--                                        <div class="tasks-section">--}}
{{--                                        <h6 class="tasks-title">Siz bajargan vazifalar ({{ $user_tasks?->tasks?->count() }})</h6>--}}

{{--                                        @forelse ($user_tasks->tasks as $task)--}}
{{--                                            <div class="task-result-card">--}}
{{--                                                <div class="task-result-header">--}}
{{--                                                    <h6 class="task-title">{{ $task->name }}</h6>--}}
{{--                                                    <span class="task-status {{ $task->comments->count() > 0 ? 'status-reviewed' : 'status-pending' }}">--}}
{{--                                                    {{ $task->comments->count() > 0 ? '✅ Ko‘rib chiqilgan' : '⏳ Ko‘rib chiqilmoqda' }}--}}
{{--                                                </span>--}}
{{--                                                </div>--}}

{{--                                                <p class="task-description">{{ $task->description }}</p>--}}

{{--                                                @if ($task->file_path)--}}
{{--                                                    <a href="{{ asset('storage/' . $task->file_path) }}"--}}
{{--                                                       target="_blank"--}}
{{--                                                       class="task-file">--}}
{{--                                                        📎 Biriktirilgan fayl--}}
{{--                                                    </a>--}}
{{--                                                @endif--}}

{{--                                                @if($task->comments->count() > 0)--}}
{{--                                                    <div class="comments-section">--}}
{{--                                                        <h6 class="comments-title">Komissar izohlari</h6>--}}

{{--                                                        @foreach ($task->comments as $comment)--}}
{{--                                                            <div class="comment-item">--}}
{{--                                                                <div class="comment-header">--}}
{{--                                                                    <span class="comment-author">{{ $comment->user->name ?? 'Komissar' }}</span>--}}
{{--                                                                    <span class="comment-date">{{ $comment->created_at->format('d M, Y H:i') }}</span>--}}
{{--                                                                </div>--}}
{{--                                                                <p class="comment-text">{{ $comment->comment }}</p>--}}
{{--                                                            </div>--}}
{{--                                                        @endforeach--}}
{{--                                                    </div>--}}
{{--                                                @else--}}
{{--                                                    <div class="no-comments">--}}
{{--                                                        <p>Hozircha komissar izohlari mavjud emas. Vazifa ko‘rib chiqilmoqda.</p>--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                        @empty--}}
{{--                                            <div class="text-center py-4 text-muted">--}}
{{--                                                <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>--}}
{{--                                                <p>Bu KPI bo‘yicha hali hech qanday vazifa topshirmagansiz.</p>--}}
{{--                                                <a href="#" class="btn btn-primary btn-modern">--}}
{{--                                                    Birinchi vazifani topshirish--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                        @endforelse--}}
{{--                                    </div>--}}
{{--                                    @endif--}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Yutuqlar -->
{{--            @if($achievements ?? false)--}}
{{--                <div class="progress-section fade-in">--}}
{{--                    <h3 class="progress-title">🏆 Sizning yutuqlaringiz</h3>--}}

{{--                    <div class="row">--}}
{{--                        @foreach($achievements as $achievement)--}}
{{--                            <div class="col-md-4 mb-3">--}}
{{--                                <div class="performance-card bounce">--}}
{{--                                    <span class="performance-icon">{{ $achievement['icon'] }}</span>--}}
{{--                                    <div class="performance-label">{{ $achievement['title'] }}</div>--}}
{{--                                    <div class="performance-description">{{ $achievement['description'] }}</div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endif--}}
        </div>
    </div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle category sections with animation
    $('.category-result-header').on('click', function() {
        $(this).toggleClass('expanded');
        let targetId = $(this).data('bs-target');
        let targetSection = $(targetId);

        if (targetSection.hasClass('show')) {
            targetSection.removeClass('show').slideUp(300);
        } else {
            targetSection.addClass('show').slideDown(300);
        }
    });

    // Animate progress bars on scroll
    function animateProgressBars() {
        $('.progress-bar').each(function() {
            let $this = $(this);
            let width = $this.css('width');

            if (isElementInViewport($this[0])) {
                $this.css('width', '0%').animate({
                    width: width
                }, 1500);
            }
        });
    }

    // Animate score circle
    function animateScoreCircle() {
        $('.score-circle').each(function() {
            let $this = $(this);
            let score = $this.css('--score') || $this.attr('style').match(/--score:\s*(\d+)/)?.[1] || 0;

            if (isElementInViewport($this[0])) {
                $this.css('background', 'conic-gradient(#e0e0e0 0deg, #e0e0e0 360deg)');

                setTimeout(() => {
                    $this.css('background', `conic-gradient(var(--warning-color) 0deg, var(--warning-color) ${score * 3.6}deg, #e0e0e0 ${score * 3.6}deg)`);
                }, 500);
            }
        });
    }

    // Check if element is in viewport
    function isElementInViewport(el) {
        let rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Trigger animations on scroll
    $(window).on('scroll', function() {
        animateProgressBars();
        animateScoreCircle();
    });

    // Initial animation trigger
    setTimeout(() => {
        animateProgressBars();
        animateScoreCircle();
    }, 500);

    // Add hover effects to performance cards
    $('.performance-card').hover(
        function() {
            $(this).find('.performance-icon').addClass('bounce');
        },
        function() {
            $(this).find('.performance-icon').removeClass('bounce');
        }
    );

    // Smooth scrolling for better UX
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });

    // Add tooltips for score badges
    $('.score-badge').each(function() {
        let score = parseFloat($(this).text());
        let tooltip = '';

        if (score >= 90) tooltip = 'Excellent Performance! 🌟';
        else if (score >= 80) tooltip = 'Great Job! 👏';
        else if (score >= 70) tooltip = 'Good Work! 👍';
        else if (score >= 60) tooltip = 'Satisfactory 📈';
        else if (score >= 40) tooltip = 'Needs Improvement 📊';
        else tooltip = 'Requires Attention 🎯';

        $(this).attr('title', tooltip);
    });

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Add celebration effect for high scores
    $('.score-badge').each(function() {
        let score = parseFloat($(this).text());
        if (score >= 90) {
            $(this).addClass('bounce');

            // Add sparkle effect
            setInterval(() => {
                if (Math.random() > 0.7) {
                    $(this).css('box-shadow', '0 0 20px rgba(255, 215, 0, 0.8)');
                    setTimeout(() => {
                        $(this).css('box-shadow', '');
                    }, 500);
                }
            }, 2000);
        }
    });
});
</script>
@endsection
