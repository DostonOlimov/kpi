@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/checking.css') }}">

@section('content')
    <div class="section">
        <!-- PAGE-HEADER -->
        <div class="page-header mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-user-circle mr-1"></i>&nbsp; Xodimning shaxsiy ko‘rsatkichlari
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <!-- Statistics Cards -->
                        <div class="stats-container fade-in">
                            <div class="stat-card total">
                                <div class="stat-number" id="total-tasks">{{ $total ?? 0 }}</div>
                                <div class="stat-label"><i class="fa fa-tasks"></i> Umumiy vazifalar</div>
                            </div>
                            <div class="stat-card pending">
                                <div class="stat-number" id="pending-reviews">{{ $total - $checked ?? 0 }}</div>
                                <div class="stat-label"><i class="fa fa-clock-o"></i> Baholanishi kutilyotgan</div>
                            </div>
                            <div class="stat-card completed">
                                <div class="stat-number" id="reviewed-tasks">{{ $checked ?? 0 }}</div>
                                <div class="stat-label"><i class="fa fa-check-circle"></i> Baholanganlar</div>
                            </div>
                            <div class="stat-card scored">
                                <div class="stat-number" id="scored-kpis">{{ $scoredKPIs ?? 0 }}</div>
                                <div class="stat-label"><i class="fa fa-star"></i> Baholangan KPIlar</div>
                            </div>
                        </div>

                        <!-- Success Alert -->
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i>
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
                                                <h5><i class="fa fa-bars"></i> {{ $child->name }}</h5>
                                            </div>
                                            <div class="child-stats">
                                                <span><i class="fa fa-tasks"></i> {{ $child->tasks->count() }} ta vazifa</span>
                                                <div class="child-score">
                                                    <i class="fa fa-star-half-o"></i> Baho: {{ $child->score ?? 'Baholanmagan' }}
                                                </div>
                                                <span class="expand-icon"><i class="fa fa-chevron-down"></i></span>
                                            </div>
                                        </div>

                                        <div class="tasks-section collapse" id="child-{{ $child->id }}">
                                            <!-- Tasks Grid -->
                                            <div class="tasks-grid">
                                                @forelse ($child->tasks as $task)
                                                    <div class="task-card" data-task-id="{{ $task->id }}">
                                                        <div class="task-header-info">
                                                            <div class="task-user">
                                                                <i class="fa fa-user"></i> {{ $user->first_name . ' '. $user->last_name }}
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $task->created_at->format('Y-m-d') }}
                                                            </small>
                                                        </div>

                                                        <div class="task-content">
                                                            <h6><i class="fa fa-file-text-o"></i> {{ $task->name }}</h6>
                                                            <p class="task-description">{{ $task->description }}</p>

                                                            @if ($task->file_path)
                                                                <a href="{{ asset('storage/' . $task->file_path) }}"
                                                                   target="_blank"
                                                                   class="task-file">
                                                                    <i class="fa fa-paperclip"></i> Ilovani ko‘rish
                                                                </a>
                                                            @endif
                                                        </div>

                                                        <!-- Comments Section -->
                                                        <div class="comments-section">
                                                            <h6 class="comments-title"><i class="fa fa-comments"></i> Izohlar</h6>

                                                            <div class="comments-list" id="comments-{{ $task->id }}">
                                                                @forelse ($task->comments ?? [] as $comment)
                                                                    <div class="comment-item">
                                                                        <div class="comment-header">
                                                                        <span class="comment-author">
                                                                            <i class="fa fa-user-circle-o"></i> {{ $comment->user->first_name . ' ' . $comment->user->last_name }}
                                                                        </span>
                                                                            <span class="comment-date">
                                                                            <i class="fa fa-calendar"></i> {{ $comment->created_at->format('Y-m-d H:i') }}
                                                                        </span>
                                                                        </div>
                                                                        <p class="comment-text">{{ $comment->comment }}</p>
                                                                    </div>
                                                                @empty
                                                                    <p class="text-muted">Hozircha izohlar mavjud emas.</p>
                                                                @endforelse
                                                            </div>

                                                            <!-- Add Comment Form -->
                                                            <form class="comment-form" data-task-id="{{ $task->id }}">
                                                                @csrf
                                                                <div class="mb-3">
                                                                <textarea name="comment"
                                                                          class="form-control"
                                                                          placeholder="Vazifaga izoh kiriting..."
                                                                          required></textarea>
                                                                </div>
                                                                <button type="submit" class="btn btn-modern btn-comment">
                                                                    <i class="fa fa-comment"></i> Izoh qoldirish
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-4 text-muted">
                                                        <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="fa fa-sticky-note-o"></i></div>
                                                        <p>Bu KPI uchun hali hech qanday vazifa yuklanmagan.</p>
                                                    </div>
                                                @endforelse
                                            </div>

                                            <!-- Scoring Section -->
                                            @php
                                                $userScore = $child->kpi_scores
                                                    ->where('user_id', $user->id)
                                                    ->where('type', 'task') // change 'task' to your actual type if needed
                                                    ->first();
                                            @endphp

                                                <!-- Scoring Section -->
                                            <div class="scoring-section">
                                                <h6 class="scoring-title">
                                                    <i class="fa fa-pencil-square-o"></i> KPI ni baholash
                                                </h6>

                                                @if ($userScore)
                                                    <!-- Show current score with Edit button -->
                                                    <div class="current-score mt-2">
                                                        <span>Joriy baho:</span>
                                                        <strong>{{ $userScore->score }}/{{ $child->max_score }}</strong>
                                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2 btn-edit-score" data-child-id="{{ $child->id }}">
                                                            <i class="fa fa-edit"></i> Tahrirlash
                                                        </button>
                                                    </div>

                                                    <!-- Hidden form initially, shown when Edit is clicked -->
                                                    <form class="scoring-form d-none mt-3" data-child-id="{{ $child->id }}" data-max-score="{{ $child->max_score }}">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                        <div class="score-input-group">
                                                            <div class="score-input">
                                                                <label for="score-{{ $child->id }}">Bahosi (0-{{ $child->max_score }})</label>
                                                                <input type="number"
                                                                       id="score-{{ $child->id }}"
                                                                       name="score"
                                                                       class="form-control"
                                                                       min="0"
                                                                       max="{{ $child->max_score }}"
                                                                       value="{{ $userScore->score }}"
                                                                       placeholder="Bahoni kiriting">
                                                            </div>
                                                            <div class="score-input">
                                                                <label for="feedback-{{ $child->id }}">Fikr</label>
                                                                <textarea name="feedback"
                                                                          id="feedback-{{ $child->id }}"
                                                                          class="form-control"
                                                                          placeholder="Ixtiyoriy fikr..."
                                                                          rows="2">{{ $userScore->feedback }}</textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-modern btn-score">
                                                                <i class="fa fa-star"></i> Bahoni saqlash
                                                            </button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <!-- Show scoring form if no score exists -->
                                                    <form class="scoring-form mt-3" data-child-id="{{ $child->id }}" data-max-score="{{ $child->max_score }}">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                        <div class="score-input-group">
                                                            <div class="score-input">
                                                                <label for="score-{{ $child->id }}">Bahosi (0-{{ $child->max_score }})</label>
                                                                <input type="number"
                                                                       id="score-{{ $child->id }}"
                                                                       name="score"
                                                                       class="form-control"
                                                                       min="0"
                                                                       max="{{ $child->max_score }}"
                                                                       placeholder="Bahoni kiriting">
                                                            </div>
                                                            <div class="score-input">
                                                                <label for="feedback-{{ $child->id }}">Fikr</label>
                                                                <textarea name="feedback"
                                                                          id="feedback-{{ $child->id }}"
                                                                          class="form-control"
                                                                          placeholder="Ixtiyoriy fikr..."
                                                                          rows="2"></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-modern btn-score">
                                                                <i class="fa fa-star"></i> Bahoni saqlash
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Farzand bo'limlarini animatsiya bilan ochib-yopish
            $('.child-header').on('click', function () {
                $(this).toggleClass('expanded');
                let targetId = $(this).data('bs-target');
                let targetSection = $(targetId);

                if (targetSection.hasClass('show')) {
                    targetSection.removeClass('show').slideUp(300);
                } else {
                    targetSection.addClass('show').slideDown(300);
                }
            });

            // Izoh qo‘shish funksiyasi
            $('.comment-form').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let taskId = form.data('task-id');
                let formData = new FormData(this);
                let submitBtn = form.find('button[type="submit"]');
                let originalBtnText = submitBtn.html();
                let commentsList = $(`#comments-${taskId}`);

                // Yuklanayotgan holatni ko'rsatish
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Qo‘shilmoqda...');

                $.ajax({
                    url: `/commission-profile/tasks/${taskId}/comment`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        commentsList.find('.text-muted').remove();

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

                        showNotification('Izoh muvaffaqiyatli qo‘shildi!', 'success');
                        updateStats();
                    },
                    error: function (xhr) {
                        showNotification('Izoh qo‘shishda xatolik. Iltimos, qaytadan urinib ko‘ring.', 'error');
                        console.error('Xatolik:', xhr.responseText);
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // KPI ball qo‘yish funksiyasi
            $('.scoring-form').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let childId = form.data('child-id');
                let maxScore = parseInt(form.data('max-score'), 10); // maksimal ball formadan olinadi
                let userId = {{ $user->id }};

                let formData = new FormData(this);
                formData.append('user_id', userId);
                formData.append('type', 1);

                let submitBtn = form.find('button[type="submit"]');
                let originalBtnText = submitBtn.html();

                let score = parseFloat(form.find('input[name="score"]').val());
                if (isNaN(score) || score < 0 || score > maxScore) {
                    showNotification(`Iltimos, 0 dan ${maxScore} gacha bo‘lgan haqiqiy ball kiriting.`, 'error');
                    return;
                }

                // Yuklanayotgan holat
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saqlanmoqda...');

                $.ajax({
                    url: `/commission-profile/kpi/${childId}/score`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        let childHeader = $(`.child-header[data-bs-target="#child-${childId}"]`);
                        childHeader.find('.child-score').html(`Ball: ${response.score}/${maxScore}`);

                        let currentScoreDiv = form.siblings('.current-score');
                        if (currentScoreDiv.length) {
                            currentScoreDiv.html(`
                            <span>Joriy ball:</span>
                            <strong>${response.score}/${maxScore}</strong>
                        `);
                        } else {
                            form.after(`
                            <div class="current-score mt-3 fade-in">
                                <span>Joriy ball:</span>
                                <strong>${response.score}/${maxScore}</strong>
                            </div>
                        `);
                        }

                        showNotification('Ball muvaffaqiyatli saqlandi!', 'success');
                        updateStats();
                    },
                    error: function (xhr) {
                        showNotification('Ballni saqlashda xatolik. Iltimos, yana urinib ko‘ring.', 'error');
                        console.error('Xatolik:', xhr.responseText);
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // Avtomatik textarea balandligini sozlash
            $('textarea').on('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Statistikani yangilash funksiyasi
            function updateStats() {
                $.ajax({
                    url: '/commission/stats',
                    type: 'GET',
                    success: function (stats) {
                        $('#total-tasks').text(stats.total_tasks);
                        $('#pending-reviews').text(stats.pending_reviews);
                        $('#reviewed-tasks').text(stats.reviewed_tasks);
                        $('#scored-kpis').text(stats.scored_kpis);

                        $('.stat-number').addClass('pulse');
                        setTimeout(() => $('.stat-number').removeClass('pulse'), 2000);
                    }
                });
            }

            // E'lon (xabar) ko‘rsatish funksiyasi
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
                setTimeout(function () {
                    notification.alert('close');
                }, 5000);
            }

            // Bootstrap bo‘lsa, tooltiplarni ishga tushurish
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Yumushoq skroll qilish
            $('a[href^="#"]').on('click', function (event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });

            // Har 30 soniyada statistikani avtomatik yangilash
            setInterval(updateStats, 30000);
        });
    </script>
@endsection
