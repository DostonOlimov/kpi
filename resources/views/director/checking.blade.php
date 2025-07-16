@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/checking.css') }}">
@section('content')
    <div class="section">
        <!-- PAGE-HEADER -->
        <div class="page-header mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-user-circle mr-1"></i>&nbsp; Xodimning shaxsiy ko'rsatkichlari
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <!-- Statistics Cards -->
                        <div class="stats-container fade-in mb-4">
                            <div class="stat-card total">
                                <div class="stat-number" id="total-kpis">
                                    {{ $user_kpis->count() ?? 0 }} / {{ $user_kpis->sum('target_score') ?? 0 }}
                                </div>
                                <div class="stat-label">
                                    <i class="fa fa-tasks"></i> Umumiy KPI ko'rsatkichlari
                                </div>
                            </div>
                            <div class="stat-card pending">
                                <div class="stat-number" id="total-tasks">
                                    {{ $user_kpis->sum(function($user_kpi) { return $user_kpi->tasks->count(); }) }}
                                </div>
                                <div class="stat-label">
                                    <i class="fa fa-clock-o"></i> Bajarilgan topshiriqlar soni
                                </div>
                            </div>
                            <div class="stat-card completed">
                                <div class="stat-number" id="reviewed-tasks">{{ $reviewed_tasks }}</div>
                                <div class="stat-label">
                                    <i class="fa fa-check-circle"></i> Baholangan topshiriqlar
                                </div>
                            </div>
                            <div class="stat-card scored">
                                <div class="stat-number" id="scored-kpis">
                                    {{ $user_kpis->whereNotNull('current_score')->count() }} / {{ number_format($user_kpis->sum('current_score'), 1) }}
                                </div>
                                <div class="stat-label">
                                    <i class="fa fa-star"></i> Baholangan KPIlar
                                </div>
                            </div>
                        </div>

                        <!-- Success Alert -->
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i>
                                {{ $message }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Error Alert -->
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i>
                                {{ $message }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- KPI Categories -->
                        <div class="kpi-container">
                            @forelse ($user_kpis as $user_kpi)
                                    <!-- Enhanced KPI Completion Actions -->
                                    <div class="kpi-actions-enhanced mt-3 mb-3">
                                        <div class="kpi-status-card">
                                            <!-- Status Header -->
                                            <div class="child-kpi-card mb-4" data-kpi-id="{{ $user_kpi->id }}">
                                                <div class="child-header"
                                                     data-bs-toggle="collapse"
                                                     data-bs-target="#child-{{ $user_kpi->kpi?->id }}"
                                                     aria-expanded="false"
                                                     aria-controls="child-{{ $user_kpi->kpi?->id }}">
                                                    <div class="child-info">
                                                        <h5>
                                                            <i class="fa fa-bars"></i>
                                                            {{ $user_kpi->kpi?->name }}
                                                            @if($user_kpi->status === 'completed')
                                                                <span class="badge bg-success ms-2">
                                                    <i class="fa fa-check"></i> Yakunlangan
                                                </span>
                                                            @endif
                                                        </h5>
                                                    </div>
                                                    <div class="child-stats">
                                        <span class="task-count">
                                            <i class="fa fa-tasks"></i>
                                            {{ $user_kpi->tasks->count() }} ta vazifa
                                        </span>
                                                        <div class="child-score">
                                                            <i class="fa fa-star-half-o"></i>
                                                            Baho: {{ $user_kpi->current_score ? number_format($user_kpi->current_score, 1) : 'Baholanmagan' }}
                                                        </div>
                                                        <span class="expand-icon">
                                            <i class="fa fa-chevron-down"></i>
                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="status-header d-flex justify-content-between align-items-center mb-3">
                                                <div class="status-info">
                                                    <small class="text-muted">
                                                        Yaratilgan: {{ $user_kpi->created_at->format('d.m.Y') }}
                                                    </small>
                                                </div>

                                                <!-- Status Badge -->
                                                <div class="status-badge">
                                                    @if($user_kpi->status === 'completed')
                                                        <span class="badge bg-success fs-6 px-3 py-2">
                        <i class="fa fa-check-circle me-1"></i>
                        Yakunlangan
                    </span>
                                                    @elseif($user_kpi->current_score)
                                                        <span class="badge bg-warning fs-6 px-3 py-2">
                        <i class="fa fa-clock me-1"></i>
                        Baholangan
                    </span>
                                                    @else
                                                        <span class="badge bg-info fs-6 px-3 py-2">
                        <i class="fa fa-play-circle me-1"></i>
                        Jarayonda
                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Progress Section -->
                                            <div class="progress-section mb-3">
                                                @php
                                                    $totalTasks = $user_kpi->tasks->count();
                                                    $scoredTasks = $user_kpi->tasks->filter(function($task) {
                                                        return $task->score !== null;
                                                    })->count();
                                                    $progressPercentage = $totalTasks > 0 ? ($scoredTasks / $totalTasks) * 100 : 0;
                                                @endphp

                                                <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-medium">
                    <i class="fa fa-tasks me-1"></i>
                    Vazifalar jarayoni
                </span>
                                                    <span class="text-primary fw-bold">
                    {{ $scoredTasks }}/{{ $totalTasks }}
                </span>
                                                </div>

                                                <div class="progress progress-enhanced mb-2" style="height: 8px;">
                                                    <div class="progress-bar bg-gradient-primary"
                                                         role="progressbar"
                                                         style="width: {{ $progressPercentage }}%"
                                                         aria-valuenow="{{ $progressPercentage }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>

                                                <div class="progress-stats d-flex justify-content-between">
                                                    <small class="text-success">
                                                        <i class="fa fa-check-circle me-1"></i>
                                                        {{ $scoredTasks }} baholangan
                                                    </small>
                                                    @if($totalTasks - $scoredTasks > 0)
                                                        <small class="text-warning">
                                                            <i class="fa fa-clock me-1"></i>
                                                            {{ $totalTasks - $scoredTasks }} kutilmoqda
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Score Display -->
                                            @if($user_kpi->current_score)
                                                <div class="score-display mb-3">
                                                    <div class="score-card bg-light rounded p-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-6">
                                                                <div class="score-info">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fa fa-star text-warning fs-4 me-2"></i>
                                                                        <div>
                                                                            <h4 class="mb-0 text-primary fw-bold">
                                                                                {{ number_format($user_kpi->current_score, 1) }}
                                                                            </h4>
                                                                            <small class="text-muted">
                                                                                / {{ $user_kpi->kpi?->max_score ?? 100 }} ball
                                                                            </small>
                                                                        </div>
                                                                    </div>

                                                                    @php
                                                                        $scorePercentage = (($user_kpi->current_score / ($user_kpi->kpi?->max_score ?? 100)) * 100);
                                                                        $scoreClass = $scorePercentage >= 80 ? 'success' : ($scorePercentage >= 60 ? 'warning' : 'danger');
                                                                    @endphp

                                                                    <div class="score-progress">
                                                                        <div class="progress mb-1" style="height: 6px;">
                                                                            <div class="progress-bar bg-{{ $scoreClass }}"
                                                                                 style="width: {{ $scorePercentage }}%"></div>
                                                                        </div>
                                                                        <small class="text-{{ $scoreClass }} fw-medium">
                                                                            {{ number_format($scorePercentage, 1) }}% natija
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="score-details">
                                                                    @if($user_kpi->feedback)
                                                                        <div class="feedback-preview">
                                                                            <small class="text-muted d-block mb-1">
                                                                                <i class="fa fa-comment me-1"></i>
                                                                                Fikr:
                                                                            </small>
                                                                            <p class="mb-0 text-dark small">
                                                                                {{ Str::limit($user_kpi->feedback, 100) }}
                                                                            </p>
                                                                        </div>
                                                                    @endif

                                                                    @if($user_kpi->completed_at)
                                                                        <small class="text-success d-block mt-2">
                                                                            <i class="fa fa-calendar-check me-1"></i>
                                                                            Yakunlangan: {{ $user_kpi->completed_at->format('d.m.Y H:i') }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Action Buttons -->
                                            @if($user_kpi->status !== 'completed')
                                                <div class="action-buttons">
                                                    <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                                        @if($user_kpi->current_score)
                                                            <!-- Edit Actions -->
                                                            <button class="btn btn-outline-primary btn-sm edit-kpi-btn"
                                                                    data-kpi-id="{{ $user_kpi->id }}"
                                                                    data-current-score="{{ $user_kpi->current_score }}"
                                                                    data-current-feedback="{{ $user_kpi->feedback ?? '' }}"
                                                                    data-kpi-name="{{ $user_kpi->kpi?->name }}"
                                                                    data-bs-toggle="tooltip"
                                                                    title="KPI bahosini tahrirlash">
                                                                <i class="fa fa-edit me-1"></i>
                                                                Tahrirlash
                                                            </button>

                                                            <button class="btn btn-outline-secondary btn-sm view-details-btn"
                                                                    data-kpi-id="{{ $user_kpi->id }}"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Batafsil ma'lumot">
                                                                <i class="fa fa-eye me-1"></i>
                                                                Ko'rish
                                                            </button>
                                                        @else
                                                            <!-- Initial Actions -->
                                                            <div class="initial-actions">
                                                                @if($progressPercentage > 0)
                                                                    <button class="btn btn-primary complete-kpi-btn"
                                                                            data-kpi-id="{{ $user_kpi->id }}"
                                                                            data-kpi-name="{{ $user_kpi->kpi?->name }}"
                                                                            data-bs-toggle="tooltip"
                                                                            title="Jarayonni yakunlash">
                                                                        <i class="fa fa-check-circle me-1"></i>
                                                                        Jarayonni yakunlash
                                                                    </button>
                                                                @else
                                                                    <div class="alert alert-info alert-sm mb-0 py-2">
                                                                        <i class="fa fa-info-circle me-1"></i>
                                                                        <small>
                                                                            Avval vazifalarni bajaring va baholang
                                                                        </small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Completed Status -->
                                                <div class="completed-status">
                                                    <div class="alert alert-success mb-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="fa fa-check-circle me-2"></i>
                                                                <strong>KPI muvaffaqiyatli yakunlandi!</strong>
                                                            </div>
                                                            <div class="completed-actions">
                                                                <button class="btn btn-outline-success btn-sm view-details-btn"
                                                                        data-kpi-id="{{ $user_kpi->id }}">
                                                                    <i class="fa fa-eye me-1"></i>
                                                                    Batafsil
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                    <!-- Tasks Section -->
                                    <div class="tasks-section collapse" id="child-{{ $user_kpi->kpi?->id }}">
                                        <div class="tasks-grid">
                                            @forelse ($user_kpi->tasks as $task)
                                                <div class="task-card" data-task-id="{{ $task->id }}">
                                                    <div class="task-header-info">
                                                        <div class="task-user">
                                                            <i class="fa fa-user"></i>
                                                            {{ $user->first_name . ' '. $user->last_name }}
                                                        </div>
                                                        <small class="text-muted">
                                                            <i class="fa fa-calendar"></i>
                                                            {{ $task->created_at->format('d.m.Y') }}
                                                        </small>
                                                    </div>

                                                    <div class="task-content">
                                                        <h6>
                                                            <i class="fa fa-file-text-o"></i>
                                                            {{ $task->name }}
                                                        </h6>
                                                        <p class="task-description">{{ $task->description }}</p>

                                                        @if ($task->file_path)
                                                            <a href="{{ asset('storage/' . $task->file_path) }}"
                                                               target="_blank"
                                                               class="task-file btn btn-outline-info btn-sm">
                                                                <i class="fa fa-paperclip"></i> Ilovani ko'rish
                                                            </a>
                                                        @endif
                                                    </div>

                                                    <!-- Score Section -->
                                                    <div class="score-section mt-3">
                                                        <div class="score-details" id="score-{{ $task->id }}">
                                                            @if ($task->score)
                                                                <h6 class="score-title">
                                                                    <i class="fa fa-star text-warning"></i> Baho
                                                                </h6>
                                                                <div class="score-item p-3 rounded bg-light border">
                                                                    <div class="d-flex justify-content-between mb-2">
                                                                    <span class="score-author text-muted">
                                                                        <i class="fa fa-user-circle-o"></i>
                                                                        {{ $task->score->user ?? 'AI Baholovchi' }}
                                                                    </span>
                                                                        <span class="score-date text-muted">
                                                                        <i class="fa fa-calendar"></i>
                                                                        {{ $task->task_score->created_at?->format('d.m.Y') ?? 'Sana yo\'q' }}
                                                                    </span>
                                                                    </div>
                                                                    <p class="mb-1">
                                                                        <strong>Baho:</strong>
                                                                        {{ round($task->score) }}/{{ $task->userKpi->kpi->max_score ?? '10' }}
                                                                    </p>
                                                                    @if ($task->task_score->feedback)
                                                                        <p class="mb-0">
                                                                            <strong>Fikr:</strong> {{ $task->task_score->feedback }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div class="text-center mt-2 no-score-section">
                                                                    <div class="alert alert-info">
                                                                        <i class="fa fa-info-circle"></i>
                                                                        Hozircha baho mavjud emas
                                                                    </div>
                                                                    <button class="btn btn-outline-primary ai-score-btn"
                                                                            data-task-id="{{ $task->id }}">
                                                                        <i class="fa fa-magic"></i> AI baholash
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-5 text-muted">
                                                    <div class="empty-state">
                                                        <i class="fa fa-sticky-note-o" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                                                        <p>Bu KPI uchun hali hech qanday vazifa yuklanmagan.</p>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fa fa-chart-bar" style="font-size: 4rem; color: #6c757d; margin-bottom: 2rem;"></i>
                                        <h4 class="text-muted">KPI ko'rsatkichlari topilmadi</h4>
                                        <p class="text-muted">Hozircha sizga tayinlangan KPI ko'rsatkichlari mavjud emas.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Completion Modal -->
    <div class="modal fade" id="kpiCompletionModal" tabindex="-1" aria-labelledby="kpiCompletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="kpiCompletionModalLabel">
                        <i class="fa fa-check-circle"></i> KPI ni yakunlash
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="kpiCompletionForm">
                    <div class="modal-body">
                        <input type="hidden" id="kpiId" name="kpi_id">

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle" ></i>
                            <strong id="kpiName"></strong> KPI sini yakunlash
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kpiScore" class="form-label">
                                        <i class="fa fa-star text-warning"></i> Umumiy baho
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control form-control-lg"
                                               id="kpiScore"
                                               name="score"
                                               min="0"
                                               max="100"
                                               step="0.01">
                                        <span class="input-group-text">/<span id="kpiMaxScore"></span></span>
                                    </div>
                                    <div class="form-text">
                                        <i class="fa fa-calculator"></i>
                                        Bu baho barcha vazifalar bahosining o'rtacha qiymati
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fa fa-tasks"></i> Vazifalar holati
                                    </label>
                                    <div id="tasksSummary" class="border rounded p-3 bg-light">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="kpiFeedback" class="form-label">
                                <i class="fa fa-comment"></i> Fikr va tavsiyalar
                            </label>
                            <textarea class="form-control"
                                      id="kpiFeedback"
                                      name="feedback"
                                      rows="4"
                                      placeholder="KPI bo'yicha umumiy fikr va tavsiyalarni kiriting..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i> Bekor qilish
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check"></i> Yakunlash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yuklanmoqda...</span>
            </div>
            <p class="mt-2">Iltimos kuting...</p>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            'use strict';

            // Initialize variables
            const $ = window.jQuery;
            const Swal = window.Swal;

            // CSRF Setup
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (csrfToken) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            }

            // ===========================================
            // KPI COMPLETION HANDLERS
            // ===========================================

            // Complete KPI Button Handler
            $(document).on('click', '.complete-kpi-btn', function() {
                const kpiId = $(this).data('kpi-id');
                const kpiName = $(this).data('kpi-name');
                const $btn = $(this);

                setButtonLoading($btn, true, 'Tekshirilmoqda...');

                $.ajax({
                    url: `/user-kpi/${kpiId}/check-completion`,
                    method: 'GET',
                    success: function(response) {
                        if (response.can_complete) {
                            showKpiModal({
                                id: kpiId,
                                name: kpiName,
                                score: response.average_score.toFixed(2),
                                max_score: response.max_score,
                                feedback: '',
                                title: '<i class="fa fa-check-circle"></i> KPI ni yakunlash',
                                tasksSummary: response.tasks_summary
                            });
                        } else {
                            showWarningAlert(
                                'Diqqat!',
                                `${response.unscored_count} ta vazifa hali baholanmagan. Barcha vazifalarni baholashdan keyin KPI ni yakunlashingiz mumkin.`
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error('Check completion error:', xhr);
                        showErrorAlert('Tekshirishda xatolik yuz berdi. Iltimos, qayta urinib ko\'ring.');
                    },
                    complete: function() {
                        setButtonLoading($btn, false, '<i class="fa fa-check-circle"></i> Jarayonni yakunlash');
                    }
                });
            });

            // Edit KPI Button Handler
            $(document).on('click', '.edit-kpi-btn', function() {
                const kpiId = $(this).data('kpi-id');
                const kpiName = $(this).data('kpi-name');
                const currentScore = $(this).data('current-score');
                const currentFeedback = $(this).data('current-feedback');

                showKpiModal({
                    id: kpiId,
                    name: kpiName,
                    score: currentScore,
                    feedback: currentFeedback,
                    title: '<i class="fa fa-edit"></i> KPI bahosini tahrirlash'
                });
            });

            // KPI Form Submission
            $('#kpiCompletionForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    kpi_id: $('#kpiId').val(),
                    score: $('#kpiScore').val(),
                    feedback: $('#kpiFeedback').val()
                };

                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.html();

                setButtonLoading($submitBtn, true, 'Saqlanmoqda...');

                $.ajax({
                    url: '/user-kpi/complete',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#kpiCompletionModal').modal('hide');
                            showSuccessAlert('Muvaffaqiyat!', response.message).then(() => {
                                location.reload();
                            });
                        } else {
                            $('#kpiCompletionModal').modal('hide');
                            showErrorAlert(response.message || 'Saqlashda xatolik yuz berdi.');
                        }
                    },
                    error: function(xhr) {
                        $('#kpiCompletionModal').modal('hide');
                        const errorMessage = getErrorMessage(xhr);
                        showErrorAlert(errorMessage);
                    },
                    complete: function() {
                        setButtonLoading($submitBtn, false, originalText);
                    }
                });
            });

            // ===========================================
            // AI SCORING HANDLER
            // ===========================================

            $(document).on('click', '.ai-score-btn', function() {
                const $button = $(this);
                const taskId = $button.data('task-id');
                const $scoreContainer = $(`#score-${taskId}`);

                setButtonLoading($button, true, 'Baholanmoqda...');

                $.ajax({
                    url: `/tasks/${taskId}/ai-score`,
                    method: 'POST',
                    success: function(response) {
                        const scoreHtml = generateScoreHtml(response);
                        $scoreContainer.html(scoreHtml);
                        updateStats();
                        showNotification('AI tomonidan baho qo\'yildi!', 'success');
                    },
                    error: function(xhr) {
                        console.error('AI scoring error:', xhr);
                        showErrorAlert('AI baholashda xatolik yuz berdi.');
                    },
                    complete: function() {
                        setButtonLoading($button, false, '<i class="fa fa-magic"></i> AI baholash');
                    }
                });
            });

            // ===========================================
            // MODAL HANDLERS
            // ===========================================

            // Modal close handlers
            $('#kpiCompletionModal').on('hidden.bs.modal', function() {
                resetModalForm();
            });

            // Handle modal close button clicks
            $('[data-bs-dismiss="modal"]').on('click', function() {
                const modalId = $(this).closest('.modal').attr('id');
                $(`#${modalId}`).modal('hide');
            });

            // ===========================================
            // COLLAPSE HANDLERS
            // ===========================================

            $('.child-header').on('click', function() {
                const $this = $(this);
                const targetId = $this.data('bs-target');
                const $targetSection = $(targetId);

                $this.toggleClass('expanded');

                if ($targetSection.hasClass('show')) {
                    $targetSection.removeClass('show').slideUp(300);
                } else {
                    $targetSection.addClass('show').slideDown(300);
                }
            });

            // ===========================================
            // UTILITY FUNCTIONS
            // ===========================================

            function showKpiModal(options) {
                $('#kpiId').val(options.id);
                $('#kpiScore').val(options.score);
                $('#kpiFeedback').val(options.feedback || '');
                $('#kpiCompletionModalLabel').html(options.title);
                $('#kpiName').text(options.name || '');
                $('#kpiMaxScore').text(options.max_score || '');

                if (options.tasksSummary) {
                    $('#tasksSummary').html(generateTasksSummaryHtml(options.tasksSummary));
                }

                $('#kpiCompletionModal').modal('show');
            }

            function resetModalForm() {
                $('#kpiCompletionForm')[0].reset();
                $('#kpiId').val('');
                $('#tasksSummary').empty();
            }

            function setButtonLoading($button, isLoading, text) {
                if (isLoading) {
                    $button.prop('disabled', true).html(`<i class="fa fa-spinner fa-spin"></i> ${text}`);
                } else {
                    $button.prop('disabled', false).html(text);
                }
            }

            function showSuccessAlert(title, text) {
                return Swal.fire({
                    icon: 'success',
                    title: title,
                    text: text,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
            }

            function showErrorAlert(text) {
                Swal.fire({
                    icon: 'error',
                    title: 'Xatolik!',
                    text: text,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            }

            function showWarningAlert(title, text) {
                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: text,
                    confirmButtonText: 'Tushundim',
                    confirmButtonColor: '#ffc107'
                });
            }

            function getErrorMessage(xhr) {
                let errorMessage = 'Xatolik yuz berdi.';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('\n');
                    }
                }

                return errorMessage;
            }

            function generateScoreHtml(response) {
                const score = response.score || '-';
                const feedback = response.feedback || 'Fikr mavjud emas';
                const maxScore = response.max_score || '10';
                const currentDate = new Date().toLocaleDateString('uz-UZ');

                return `
            <h6 class="score-title">
                <i class="fa fa-star text-warning"></i> Baho
            </h6>
            <div class="score-item p-3 rounded bg-light border fade-in">
                <div class="d-flex justify-content-between mb-2">
                    <span class="score-author text-muted">
                        <i class="fa fa-robot"></i> AI Baholovchi
                    </span>
                    <span class="score-date text-muted">
                        <i class="fa fa-calendar"></i> ${currentDate}
                    </span>
                </div>
                <p class="mb-1">
                    <strong>Baho:</strong> ${score}/${maxScore}
                </p>
                <p class="mb-0">
                    <strong>Fikr:</strong> ${feedback}
                </p>
            </div>
        `;
            }

            function generateTasksSummaryHtml(summary) {
                return `
            <div class="row text-center">
                <div class="col-4">
                    <div class="text-primary">
                        <i class="fa fa-tasks fa-2x"></i>
                        <div class="mt-1">
                            <strong>${summary.total || 0}</strong>
                            <small class="d-block text-muted">Jami</small>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-success">
                        <i class="fa fa-check-circle fa-2x"></i>
                        <div class="mt-1">
                            <strong>${summary.scored || 0}</strong>
                            <small class="d-block text-muted">Baholangan</small>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-warning">
                        <i class="fa fa-clock-o fa-2x"></i>
                        <div class="mt-1">
                            <strong>${summary.unscored || 0}</strong>
                            <small class="d-block text-muted">Kutilmoqda</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
            }

            function updateStats() {
                const userId = {{ $user->id ?? 'null' }};
                if (!userId) return;

                $.ajax({
                    url: '/director-profile/stats',
                    method: 'GET',
                    data: { user_id: userId },
                    success: function(stats) {
                        $('#reviewed-tasks').text(stats.reviewed_tasks);
                        $('#total-tasks').text(stats.total_tasks);
                        $('#scored-kpis').text(`${stats.scored_kpis} / ${stats.total_score}`);

                    },
                    error: function(xhr) {
                        console.error('Stats update error:', xhr);
                    }
                });
            }

            function showNotification(message, type = 'info') {
                const alertClass = type === 'success' ? 'alert-success' :
                    type === 'error' ? 'alert-danger' : 'alert-info';
                const icon = type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';

                const $notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show notification-toast"
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <i class="fa ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);

                $('body').append($notification);

                setTimeout(() => {
                    $notification.alert('close');
                }, 5000);
            }

            function showLoadingOverlay(show) {
                if (show) {
                    $('#loadingOverlay').fadeIn(300);
                } else {
                    $('#loadingOverlay').fadeOut(300);
                }
            }

            // Auto-resize textareas
            $('textarea').on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Initialize tooltips if Bootstrap is available
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Smooth scroll for anchor links
            $('a[href^="#"]').on('click', function(event) {
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });

            // Auto-update stats every 30 seconds
            setInterval(updateStats, 30000);

            // Initial stats update
            updateStats();
        });
    </script>
@endsection
