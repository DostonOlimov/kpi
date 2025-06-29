@extends('layouts.app')

<style>
:root {
    --primary-color: #667eea;
    --primary-dark: #764ba2;
    --success-color: #28a745;
    --success-light: #20c997;
    --warning-color: #f57c00;
    --danger-color: #d32f2f;
    --info-color: #1976d2;
    --purple-color: #9c27b0;
    --light-bg: #f8f9fa;
    --border-radius: 12px;
    --border-radius-sm: 8px;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-hover: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Main Container */
.results-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

/* Header Section */
.results-header {
    background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
    color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.results-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="stars" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.2"/><circle cx="5" cy="15" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23stars)"/></svg>');
}

.results-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
}

.results-title::before {
    content: '🏆';
    margin-right: 1rem;
    font-size: 2.5rem;
}

.results-subtitle {
    opacity: 0.9;
    margin-top: 0.5rem;
    position: relative;
    z-index: 1;
    font-size: 1.1rem;
}

/* Performance Overview Cards */
.performance-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.performance-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
    text-align: center;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.performance-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
}

.performance-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.performance-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.performance-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-dark);
    margin-bottom: 0.5rem;
}

.performance-label {
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
}

.performance-description {
    color: #8e8e8e;
    font-size: 0.85rem;
    margin-top: 0.5rem;
}

/* Overall Score Card */
.overall-score-card {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    border: 2px solid var(--warning-color);
    grid-column: span 2;
}

.score-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(var(--warning-color) 0deg, var(--warning-color) calc(var(--score) * 3.6deg), #e0e0e0 calc(var(--score) * 3.6deg));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    position: relative;
}

.score-circle::before {
    content: '';
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
    position: absolute;
}

.score-text {
    position: relative;
    z-index: 1;
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--warning-color);
}

/* KPI Results Section */
.kpi-results-section {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
    overflow: hidden;
}

.kpi-results-header {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.kpi-results-title {
    color: var(--success-color);
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0;
    display: flex;
    align-items: center;
}

.kpi-results-title::before {
    content: '📊';
    margin-right: 0.75rem;
    font-size: 1.8rem;
}

/* Category Result Card */
.category-result-card {
    border-bottom: 1px solid #e9ecef;
    transition: var(--transition);
}

.category-result-card:last-child {
    border-bottom: none;
}

.category-result-card:hover {
    background-color: #f8f9fa;
}

.category-result-header {
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.category-info h4 {
    color: var(--primary-dark);
    font-weight: 600;
    margin: 0;
    font-size: 1.3rem;
}

.category-stats {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.category-score {
    text-align: center;
}

.score-badge {
    background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-block;
    min-width: 80px;
}

.score-badge.no-score {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.score-badge.low-score {
    background: linear-gradient(135deg, var(--danger-color) 0%, #e53935 100%);
}

.score-badge.medium-score {
    background: linear-gradient(135deg, var(--warning-color) 0%, #ff9800 100%);
}

.score-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.task-count {
    text-align: center;
    color: var(--info-color);
    font-weight: 600;
}

.expand-icon {
    transition: var(--transition);
    font-size: 1.2rem;
    color: var(--primary-color);
}

.category-result-header.expanded .expand-icon {
    transform: rotate(180deg);
}

/* Category Details */
.category-details {
    padding: 2rem;
    display: none;
    background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
}

.category-details.show {
    display: block;
    animation: slideDown 0.3s ease-out;
}

/* Feedback Section */
.feedback-section {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    border-radius: var(--border-radius-sm);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-left: 4px solid var(--info-color);
}

.feedback-title {
    color: var(--info-color);
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-size: 1.1rem;
}

.feedback-title::before {
    content: '💭';
    margin-right: 0.5rem;
    font-size: 1.3rem;
}

.feedback-text {
    color: #495057;
    line-height: 1.6;
    font-style: italic;
    margin: 0;
}

.no-feedback {
    color: #6c757d;
    font-style: italic;
}

/* Tasks Section */
.tasks-section {
    margin-top: 2rem;
}

.tasks-title {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    font-size: 1.2rem;
}

.tasks-title::before {
    content: '📝';
    margin-right: 0.5rem;
    font-size: 1.4rem;
}

.task-result-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius-sm);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: var(--transition);
    position: relative;
}

.task-result-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
}

.task-result-card:hover {
    border-color: var(--info-color);
    box-shadow: var(--shadow);
    transform: translateX(2px);
}

.task-result-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.task-title {
    color: var(--primary-dark);
    font-weight: 600;
    margin: 0;
    font-size: 1.1rem;
}

.task-status {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-reviewed {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    color: var(--success-color);
}

.status-pending {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    color: var(--warning-color);
}

.task-description {
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.task-file {
    display: inline-flex;
    align-items: center;
    color: var(--info-color);
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 1rem;
    transition: var(--transition);
}

.task-file:hover {
    color: var(--primary-dark);
    text-decoration: none;
}

/* Comments Section */
.comments-section {
    border-top: 1px solid #e9ecef;
    padding-top: 1rem;
    margin-top: 1rem;
}

.comments-title {
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-size: 1rem;
}

.comments-title::before {
    content: '💬';
    margin-right: 0.5rem;
}

.comment-item {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--border-radius-sm);
    padding: 1rem;
    margin-bottom: 0.75rem;
    border-left: 3px solid var(--info-color);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.comment-author {
    font-weight: 600;
    color: var(--primary-dark);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.comment-author::before {
    content: '👤';
    margin-right: 0.5rem;
}

.comment-date {
    color: #6c757d;
    font-size: 0.8rem;
}

.comment-text {
    color: #495057;
    line-height: 1.4;
    margin: 0;
}

.no-comments {
    color: #6c757d;
    font-style: italic;
    text-align: center;
    padding: 1rem;
}

/* Progress Indicators */
.progress-section {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 2rem;
    margin-bottom: 2rem;
}

.progress-title {
    color: var(--primary-dark);
    font-weight: 700;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    font-size: 1.4rem;
}

.progress-title::before {
    content: '📈';
    margin-right: 0.75rem;
    font-size: 1.6rem;
}

.progress-item {
    margin-bottom: 1.5rem;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--primary-dark);
}

.progress-bar-container {
    background: #e9ecef;
    border-radius: 10px;
    height: 12px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    border-radius: 10px;
    transition: width 1s ease-in-out;
    background: linear-gradient(90deg, var(--success-color), var(--success-light));
}

.progress-bar.medium {
    background: linear-gradient(90deg, var(--warning-color), #ff9800);
}

.progress-bar.low {
    background: linear-gradient(90deg, var(--danger-color), #e53935);
}

/* Responsive Design */
@media (max-width: 768px) {
    .results-container {
        padding: 1rem 0;
    }

    .results-header {
        padding: 1.5rem;
    }

    .results-title {
        font-size: 1.8rem;
    }

    .performance-overview {
        grid-template-columns: 1fr;
    }

    .overall-score-card {
        grid-column: span 1;
    }

    .category-result-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .category-stats {
        justify-content: center;
    }

    .task-result-header {
        flex-direction: column;
        gap: 0.5rem;
    }
}

/* Animation Classes */
.fade-in {
    animation: fadeIn 0.6s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 2000px;
    }
}

.bounce {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}
</style>

@section('content')
<div class="results-container">
    <div class="container-fluid">
        <!-- Results Header -->
        <div class="results-header fade-in">
            <h1 class="results-title">My Performance Results</h1>
            <p class="results-subtitle">Track your KPI performance, view feedback, and monitor your progress</p>
        </div>

        <!-- Performance Overview -->
        <div class="performance-overview fade-in">
            <div class="performance-card">
                <span class="performance-icon">📝</span>
                <div class="performance-number">{{ $userStats['total_tasks'] ?? 0 }}</div>
                <div class="performance-label">Tasks Submitted</div>
                <div class="performance-description">Total tasks you've completed</div>
            </div>

            <div class="performance-card">
                <span class="performance-icon">💬</span>
                <div class="performance-number">{{ $userStats['reviewed_tasks'] ?? 0 }}</div>
                <div class="performance-label">Tasks Reviewed</div>
                <div class="performance-description">Tasks with commissioner feedback</div>
            </div>

            <div class="performance-card">
                <span class="performance-icon">⭐</span>
                <div class="performance-number">{{ $userStats['scored_kpis'] ?? 0 }}</div>
                <div class="performance-label">KPIs Scored</div>
                <div class="performance-description">Categories with official scores</div>
            </div>

            <div class="performance-card overall-score-card">
                <div class="score-circle" style="--score: {{ $userStats['average_score'] ?? 0 }}">
                    <div class="score-text">{{ number_format($userStats['average_score'] ?? 0, 1) }}%</div>
                </div>
                <div class="performance-label">Overall Score</div>
                <div class="performance-description">Your average performance across all KPIs</div>
            </div>
        </div>

        <!-- Progress Section -->
        <div class="progress-section fade-in">
            <h3 class="progress-title">Performance Progress</h3>

            <div class="progress-item">
                <div class="progress-label">
                    <span>Task Completion Rate</span>
                    <span>{{ $userStats['completion_rate'] ?? 0 }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: {{ $userStats['completion_rate'] ?? 0 }}%"></div>
                </div>
            </div>

            <div class="progress-item">
                <div class="progress-label">
                    <span>Review Rate</span>
                    <span>{{ $userStats['review_rate'] ?? 0 }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar {{ $userStats['review_rate'] >= 70 ? '' : ($userStats['review_rate'] >= 40 ? 'medium' : 'low') }}"
                         style="width: {{ $userStats['review_rate'] ?? 0 }}%"></div>
                </div>
            </div>

            <div class="progress-item">
                <div class="progress-label">
                    <span>Scoring Progress</span>
                    <span>{{ $userStats['scoring_progress'] ?? 0 }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar {{ $userStats['scoring_progress'] >= 70 ? '' : ($userStats['scoring_progress'] >= 40 ? 'medium' : 'low') }}"
                         style="width: {{ $userStats['scoring_progress'] ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- KPI Results -->
        <div class="kpi-results-section fade-in">
            <div class="kpi-results-header">
                <h2 class="kpi-results-title">KPI Category Results</h2>
            </div>

            @foreach ($kpis as $category)
                <div class="category-result-card">
                    <div class="category-result-header" data-bs-toggle="collapse" data-bs-target="#category-{{ $category->id }}">
                        <div class="category-info">
                            <h4>{{ $category->name }}</h4>
                        </div>
                        <div class="category-stats">
                            <div class="task-count">
                                <div>{{ $category->user_tasks_count ?? 0 }}</div>
                                <div class="score-label">Tasks</div>
                            </div>
                            <div class="category-score">
                                @if($category->average_score)
                                    <span class="score-badge {{ $category->average_score >= 70 ? '' : ($category->average_score >= 40 ? 'medium-score' : 'low-score') }}">
                                        {{ number_format($category->average_score, 1) }}/100
                                    </span>
                                @else
                                    <span class="score-badge no-score">Not Scored</span>
                                @endif
                                <div class="score-label">Average Score</div>
                            </div>
                            <span class="expand-icon">▼</span>
                        </div>
                    </div>

                    <div class="category-details collapse" id="category-{{ $category->id }}">
                        @foreach ($category->children as $child)
                            <div style="margin-bottom: 2rem; padding: 1.5rem; background: white; border-radius: 8px; border-left: 4px solid #007bff;">
                                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                                    <h5 style="color: #495057; margin: 0;">{{ $child->name }}</h5>
                                    <div style="text-align: right;">
                                        @if($child->score)
                                            <span class="score-badge {{ $child->score >= 70 ? '' : ($child->score >= 40 ? 'medium-score' : 'low-score') }}">
                                                {{ $child->score }}/100
                                            </span>
                                        @else
                                            <span class="score-badge no-score">Not Scored</span>
                                        @endif
                                    </div>
                                </div>

                                @if($child->feedback)
                                    <div class="feedback-section">
                                        <h6 class="feedback-title">Commissioner Feedback</h6>
                                        <p class="feedback-text">{{ $child->feedback }}</p>
                                    </div>
                                @endif

                                <div class="tasks-section">
                                    <h6 class="tasks-title">Your Tasks ({{ $child->user_tasks->count() }})</h6>

                                    @forelse ($child->user_tasks as $task)
                                        <div class="task-result-card">
                                            <div class="task-result-header">
                                                <h6 class="task-title">{{ $task->title }}</h6>
                                                <span class="task-status {{ $task->comments->count() > 0 ? 'status-reviewed' : 'status-pending' }}">
                                                    {{ $task->comments->count() > 0 ? '✅ Reviewed' : '⏳ Pending Review' }}
                                                </span>
                                            </div>

                                            <p class="task-description">{{ $task->description }}</p>

                                            @if ($task->file_path)
                                                <a href="{{ asset('storage/' . $task->file_path) }}"
                                                   target="_blank"
                                                   class="task-file">
                                                    📎 View Your Attachment
                                                </a>
                                            @endif

                                            @if($task->comments->count() > 0)
                                                <div class="comments-section">
                                                    <h6 class="comments-title">Commissioner Comments</h6>

                                                    @foreach ($task->comments as $comment)
                                                        <div class="comment-item">
                                                            <div class="comment-header">
                                                                <span class="comment-author">{{ $comment->user->name ?? 'Commissioner' }}</span>
                                                                <span class="comment-date">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                                            </div>
                                                            <p class="comment-text">{{ $comment->comment }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="no-comments">
                                                    <p>No comments from commissioners yet. Your task is pending review.</p>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted">
                                            <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
                                            <p>You haven't submitted any tasks for this KPI yet.</p>
                                            <a href="{{ route('kpi.management') }}" class="btn btn-primary btn-modern">
                                                Submit Your First Task
                                            </a>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Achievement Section -->
        @if($achievements ?? false)
            <div class="progress-section fade-in">
                <h3 class="progress-title">🏆 Your Achievements</h3>

                <div class="row">
                    @foreach($achievements as $achievement)
                        <div class="col-md-4 mb-3">
                            <div class="performance-card bounce">
                                <span class="performance-icon">{{ $achievement['icon'] }}</span>
                                <div class="performance-label">{{ $achievement['title'] }}</div>
                                <div class="performance-description">{{ $achievement['description'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
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
