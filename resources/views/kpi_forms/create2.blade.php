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

/* Main Container Styles */
.commission-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.commission-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.commission-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
}

.commission-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    position: relative;
    z-index: 1;
}

.commission-subtitle {
    opacity: 0.9;
    margin-top: 0.5rem;
    position: relative;
    z-index: 1;
}

/* Stats Cards */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius-sm);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    text-align: center;
    transition: var(--transition);
    border-left: 4px solid transparent;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.stat-card.total { border-left-color: var(--info-color); }
.stat-card.pending { border-left-color: var(--warning-color); }
.stat-card.completed { border-left-color: var(--success-color); }
.stat-card.scored { border-left-color: var(--purple-color); }

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

/* Category Styles */
.category-section {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
    overflow: hidden;
}

.category-header {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.category-title {
    color: var(--primary-dark);
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0;
    display: flex;
    align-items: center;
}

.category-title::before {
    content: '📊';
    margin-right: 0.75rem;
    font-size: 1.8rem;
}

/* Child KPI Styles */
.child-kpi-card {
    border-bottom: 1px solid #e9ecef;
    transition: var(--transition);
}

.child-kpi-card:last-child {
    border-bottom: none;
}

.child-kpi-card:hover {
    background-color: #f8f9fa;
}

.child-header {
    background: linear-gradient(135deg, var(--info-color) 0%, #1565c0 100%);
    color: white;
    padding: 1.5rem;
    cursor: pointer;
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.child-header:hover {
    background: linear-gradient(135deg, #1565c0 0%, var(--info-color) 100%);
}

.child-info h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.2rem;
}

.child-stats {
    display: flex;
    gap: 1rem;
    align-items: center;
    font-size: 0.9rem;
}

.child-score {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}

.expand-icon {
    transition: var(--transition);
    font-size: 1.2rem;
}

.child-header.expanded .expand-icon {
    transform: rotate(180deg);
}

/* Task Section */
.tasks-section {
    padding: 2rem;
    display: none;
}

.tasks-section.show {
    display: block;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
    }
    to {
        opacity: 1;
        max-height: 1000px;
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
}

.tasks-grid {
    display: grid;
    gap: 1.5rem;
}

/* Task Card Styles */
.task-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius-sm);
    padding: 1.5rem;
    transition: var(--transition);
    position: relative;
}

.task-card:hover {
    border-color: var(--info-color);
    box-shadow: var(--shadow);
}

.task-header-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.task-user {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    color: var(--success-color);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.task-content h6 {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
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
}

.comments-title::before {
    content: '💬';
    margin-right: 0.5rem;
}

.comment-item {
    background: #f8f9fa;
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

/* Comment Form */
.comment-form {
    background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);
    border-radius: var(--border-radius-sm);
    padding: 1rem;
    margin-top: 1rem;
}

.comment-form textarea {
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius-sm);
    padding: 0.75rem;
    transition: var(--transition);
    resize: vertical;
    min-height: 80px;
}

.comment-form textarea:focus {
    border-color: var(--info-color);
    box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25);
}

/* Scoring Section */
.scoring-section {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    border-radius: var(--border-radius-sm);
    padding: 1.5rem;
    margin-top: 2rem;
    border: 2px solid var(--warning-color);
}

.scoring-title {
    font-weight: 700;
    color: var(--warning-color);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-size: 1.1rem;
}

.scoring-title::before {
    content: '⭐';
    margin-right: 0.5rem;
    font-size: 1.3rem;
}

.score-input-group {
    display: flex;
    gap: 1rem;
    align-items: end;
}

.score-input {
    flex: 1;
}

.score-input label {
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 0.5rem;
    display: block;
}

.score-input input {
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius-sm);
    padding: 0.75rem;
    font-size: 1.1rem;
    font-weight: 600;
    text-align: center;
    transition: var(--transition);
}

.score-input input:focus {
    border-color: var(--warning-color);
    box-shadow: 0 0 0 0.2rem rgba(245, 124, 0, 0.25);
}

.current-score {
    background: var(--success-color);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Button Styles */
.btn-modern {
    border-radius: 25px;
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    transition: var(--transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-comment {
    background: linear-gradient(135deg, var(--info-color) 0%, #1565c0 100%);
    color: white;
}

.btn-comment:hover {
    background: linear-gradient(135deg, #1565c0 0%, var(--info-color) 100%);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

.btn-score {
    background: linear-gradient(135deg, var(--warning-color) 0%, #ff9800 100%);
    color: white;
}

.btn-score:hover {
    background: linear-gradient(135deg, #ff9800 0%, var(--warning-color) 100%);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

/* Loading States */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--border-radius-sm);
    z-index: 10;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .commission-container {
        padding: 1rem 0;
    }

    .commission-header {
        padding: 1.5rem;
    }

    .commission-title {
        font-size: 1.5rem;
    }

    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }

    .child-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .child-stats {
        justify-content: center;
    }

    .tasks-section {
        padding: 1rem;
    }

    .score-input-group {
        flex-direction: column;
        gap: 1rem;
    }
}

/* Animation Classes */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

@section('content')
<div class="commission-container">
    <div class="container-fluid">
        <!-- Commission Header -->
        <div class="commission-header fade-in">
            <h1 class="commission-title">Commission Dashboard</h1>
            <p class="commission-subtitle">Review user tasks, add comments, and score KPI performance</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container fade-in">
            <div class="stat-card total">
                <div class="stat-number" id="total-tasks">{{ $totalTasks ?? 0 }}</div>
                <div class="stat-label">Total Tasks</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number" id="pending-reviews">{{ $pendingReviews ?? 0 }}</div>
                <div class="stat-label">Pending Reviews</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-number" id="reviewed-tasks">{{ $reviewedTasks ?? 0 }}</div>
                <div class="stat-label">Reviewed Tasks</div>
            </div>
            <div class="stat-card scored">
                <div class="stat-number" id="scored-kpis">{{ $scoredKPIs ?? 0 }}</div>
                <div class="stat-label">Scored KPIs</div>
            </div>
        </div>

        <!-- Success Alert -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fe fe-check-circle me-2"></i>
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- KPI Categories -->
        @foreach ($kpis as $category)
            <div class="category-section fade-in">
                <div class="category-header">
                    <h2 class="category-title">{{ $category->name }}</h2>
                </div>

                @foreach ($category->children as $child)
                    <div class="child-kpi-card">
                        <div class="child-header" data-bs-toggle="collapse" data-bs-target="#child-{{ $child->id }}">
                            <div class="child-info">
                                <h5>{{ $child->name }}</h5>
                            </div>
                            <div class="child-stats">
                                <span>{{ $child->tasks->count() }} tasks</span>
                                <div class="child-score">
                                    Score: {{ $child->score ?? 'Not Scored' }}
                                </div>
                                <span class="expand-icon">▼</span>
                            </div>
                        </div>

                        <div class="tasks-section collapse" id="child-{{ $child->id }}">
                            <!-- Tasks Grid -->
                            <div class="tasks-grid">
                                @forelse ($child->tasks as $task)
                                    <div class="task-card" data-task-id="{{ $task->id }}">
                                        <div class="task-header-info">
                                            <div class="task-user">
                                                👤 {{ $task->user->name ?? 'Unknown User' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $task->created_at->format('M d, Y') }}
                                            </small>
                                        </div>

                                        <div class="task-content">
                                            <h6>{{ $task->title }}</h6>
                                            <p class="task-description">{{ $task->description }}</p>

                                            @if ($task->file_path)
                                                <a href="{{ asset('storage/' . $task->file_path) }}"
                                                   target="_blank"
                                                   class="task-file">
                                                    📎 View Attachment
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Comments Section -->
                                        <div class="comments-section">
                                            <h6 class="comments-title">Comments</h6>

                                            <div class="comments-list" id="comments-{{ $task->id }}">
                                                @forelse ($task->comments ?? [] as $comment)
                                                    <div class="comment-item">
                                                        <div class="comment-header">
                                                            <span class="comment-author">{{ $comment->user->name ?? 'Commissioner' }}</span>
                                                            <span class="comment-date">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                                        </div>
                                                        <p class="comment-text">{{ $comment->comment }}</p>
                                                    </div>
                                                @empty
                                                    <p class="text-muted">No comments yet.</p>
                                                @endforelse
                                            </div>

                                            <!-- Add Comment Form -->
                                            <form class="comment-form" data-task-id="{{ $task->id }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <textarea name="comment"
                                                              class="form-control"
                                                              placeholder="Add your comment for this task..."
                                                              required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-modern btn-comment">
                                                    💬 Add Comment
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
                                        <p>No tasks submitted for this KPI yet.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Scoring Section -->
                            <div class="scoring-section">
                                <h6 class="scoring-title">Score this KPI Category</h6>

                                <form class="scoring-form" data-child-id="{{ $child->id }}">
                                    @csrf
                                    <div class="score-input-group">
                                        <div class="score-input">
                                            <label for="score-{{ $child->id }}">Score (0-100)</label>
                                            <input type="number"
                                                   id="score-{{ $child->id }}"
                                                   name="score"
                                                   class="form-control"
                                                   min="0"
                                                   max="100"
                                                   value="{{ $child->score ?? '' }}"
                                                   placeholder="Enter score">
                                        </div>
                                        <div class="score-input">
                                            <label for="feedback-{{ $child->id }}">Feedback</label>
                                            <textarea name="feedback"
                                                      id="feedback-{{ $child->id }}"
                                                      class="form-control"
                                                      placeholder="Optional feedback..."
                                                      rows="2">{{ $child->feedback ?? '' }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-modern btn-score">
                                            ⭐ Update Score
                                        </button>
                                    </div>
                                </form>

                                @if ($child->score)
                                    <div class="current-score mt-3">
                                        <span>Current Score:</span>
                                        <strong>{{ $child->score }}/100</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle child sections with animation
    $('.child-header').on('click', function() {
        $(this).toggleClass('expanded');
        let targetId = $(this).data('bs-target');
        let targetSection = $(targetId);

        if (targetSection.hasClass('show')) {
            targetSection.removeClass('show').slideUp(300);
        } else {
            targetSection.addClass('show').slideDown(300);
        }
    });

    // Add Comment functionality
    $('.comment-form').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let taskId = form.data('task-id');
        let formData = new FormData(this);
        let submitBtn = form.find('button[type="submit"]');
        let originalBtnText = submitBtn.html();
        let commentsList = $(`#comments-${taskId}`);

        // Show loading state
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Adding...');

        $.ajax({
            url: `/commission/tasks/${taskId}/comment`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Remove "no comments" message if exists
                commentsList.find('.text-muted').remove();

                // Add new comment with animation
                let commentHtml = `
                    <div class="comment-item fade-in">
                        <div class="comment-header">
                            <span class="comment-author">${response.user_name}</span>
                            <span class="comment-date">${response.formatted_date}</span>
                        </div>
                        <p class="comment-text">${response.comment}</p>
                    </div>`;

                commentsList.append(commentHtml);
                form.find('textarea').val('');

                showNotification('Comment added successfully!', 'success');
                updateStats();
            },
            error: function(xhr) {
                showNotification('Error adding comment. Please try again.', 'error');
                console.error('Error:', xhr.responseText);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Score KPI functionality
    $('.scoring-form').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let childId = form.data('child-id');
        let formData = new FormData(this);
        let submitBtn = form.find('button[type="submit"]');
        let originalBtnText = submitBtn.html();

        // Validate score
        let score = form.find('input[name="score"]').val();
        if (!score || score < 0 || score > 100) {
            showNotification('Please enter a valid score between 0 and 100.', 'error');
            return;
        }

        // Show loading state
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: `/commission/kpi/${childId}/score`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Update score display in header
                let childHeader = $(`.child-header[data-bs-target="#child-${childId}"]`);
                childHeader.find('.child-score').html(`Score: ${response.score}/100`);

                // Update or create current score display
                let currentScoreDiv = form.siblings('.current-score');
                if (currentScoreDiv.length) {
                    currentScoreDiv.html(`
                        <span>Current Score:</span>
                        <strong>${response.score}/100</strong>
                    `);
                } else {
                    form.after(`
                        <div class="current-score mt-3 fade-in">
                            <span>Current Score:</span>
                            <strong>${response.score}/100</strong>
                        </div>
                    `);
                }

                showNotification('Score updated successfully!', 'success');
                updateStats();
            },
            error: function(xhr) {
                showNotification('Error updating score. Please try again.', 'error');
                console.error('Error:', xhr.responseText);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Auto-resize textareas
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Update statistics
    function updateStats() {
        $.ajax({
            url: '/commission/stats',
            type: 'GET',
            success: function(stats) {
                $('#total-tasks').text(stats.total_tasks);
                $('#pending-reviews').text(stats.pending_reviews);
                $('#reviewed-tasks').text(stats.reviewed_tasks);
                $('#scored-kpis').text(stats.scored_kpis);

                // Add pulse animation to updated stats
                $('.stat-number').addClass('pulse');
                setTimeout(() => $('.stat-number').removeClass('pulse'), 2000);
            }
        });
    }

    // Notification function
    function showNotification(message, type) {
        let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        let icon = type === 'success' ? 'fe-check-circle' : 'fe-alert-circle';

        let notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show"
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: var(--shadow-hover);">
                <i class="fe ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        $('body').append(notification);

        setTimeout(function() {
            notification.alert('close');
        }, 5000);
    }

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

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

    // Auto-refresh stats every 30 seconds
    setInterval(updateStats, 30000);
});
</script>
@endsection
