@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/create-task.css') }}">
@section('content')
    <div class="kpi-container">
        <div class="container-fluid">
            <!-- Enhanced Page Header -->
            <div class="page-header fade-in">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-bar-chart-2"></i>
                        {{ __('Maqsadli ko\'rsatkichlarni qo\'shish') }}
                    </li>
                </ol>
            </div>

            <!-- Main Content -->
            <div class="row">
                <div class="col-12">
                    <div class="card main-card fade-in">
                        <div class="card-body main-card-body">
                            <!-- Success Alert -->
                            @if ($message = Session::get('success'))
                                <div class="alert alert-modern alert-success-modern slide-in">
                                    <i class="fe fe-check-circle me-2"></i>
                                    {{ $message }}
                                </div>
                            @endif

                            <!-- KPI Kategoriyalar -->
                            @foreach ($user_kpis as $user_kpi)
                                <div class="kpi-child-card fade-in">
                                    <div class="kpi-child-header">
                                        <h5>{{ $user_kpi->kpi?->name }}</h5>
                                    </div>

                                    <div class="kpi-child-body" data-child-id="{{ $user_kpi->kpi?->id }}">
                                        <h6 class="task-section-title">Sizning bajargan vazifalaringiz</h6>

                                        <div class="task-list">
                                            @forelse ($user_kpi->tasks ?? [] as $task)
                                                <div class="task-item" data-task-id="{{ $task->id }}" data-score="{{ $task->score }}">
                                                    <div class="task-header d-flex justify-content-between">
                                                        <div class="task-content">
                                                            <h6>{{ $task->name }}</h6>
                                                            <p>{{ $task->description }}</p>
                                                            @if ($task->file_path)
                                                                <a href="{{ asset('storage/' . $task->file_path) }}" target="_blank" class="task-file-link">
                                                                    <i class="fa fa-paperclip"></i> Ilovani ko‘rish
                                                                </a>
                                                            @endif

                                                            @if ($task->score)
                                                                <div class="task-score mt-2">
                                                                    <strong>Baho:</strong> {{ round($task->score, 1) }}
                                                                    @if ($task->task_score->feedback)
                                                                        <p class="mb-0"><strong>Fikr:</strong> {{ $task->task_score->feedback }}</p>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if (is_null($task->score))
                                                            <div class="task-actions">
                                                                <button class="btn btn-sm btn-modern edit-task-btn btn-info">
                                                                    <i class="fa fa-pencil"></i> Tahrirlash
                                                                </button>
                                                                <button class="btn btn-sm btn-modern delete-task btn-danger">
                                                                    <i class="fa fa-trash"></i> O‘chirish
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @if (is_null($task->score))
                                                        <form class="edit-task-form d-none" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                                                            <div class="mb-3">
                                                                <label class="form-label">Vazifa sarlavhasi</label>
                                                                <input type="text" name="title" class="form-control" value="{{ $task->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Tavsif</label>
                                                                <textarea name="description" class="form-control" rows="3" required>{{ $task->description }}</textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Fayl ilovasi</label>
                                                                <input type="file" name="file" class="form-control">
                                                            </div>
                                                            <div class="d-flex gap-2">
                                                                <button type="submit" class="btn btn-success btn-modern">
                                                                    <i class="fa fa-save"></i> Saqlash
                                                                </button>
                                                                <button type="button" class="btn btn-secondary btn-modern cancel-edit">
                                                                    <i class="fa fa-times"></i> Bekor qilish
                                                                </button>
                                                            </div>
                                                        </form>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="empty-state">
                                                    <div class="empty-state-icon"><i class="fa fa-sticky-note"></i></div>
                                                    <p>Hali hech qanday vazifa qo‘shilmagan. Quyida birinchi vazifangizni yarating!</p>
                                                </div>
                                            @endforelse
                                        </div>

                                        @if (!$user_kpi->current_score )
                                            <div class="task-form-section" id="add-task-form">
                                                <h6 class="task-form-title">Yangi vazifa qo‘shish</h6>
                                                <form class="task-form" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="child_id" value="{{ $user_kpi->id }}">

                                                    <div class="mb-3">
                                                        <input type="text" name="title" class="form-control" placeholder="Vazifa sarlavhasi..." required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <textarea name="description" class="form-control" rows="3" placeholder="Vazifa haqida yozing..." required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <input type="file" name="file" class="form-control">
                                                        <small class="text-muted">Ixtiyoriy: fayl biriktiring</small>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-modern">
                                                        <i class="fa fa-plus"></i> Vazifa qo‘shish
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="current-score mt-3">
                                                <span>Joriy baho:</span>
                                                <strong>{{ round($user_kpi->current_score) }}/{{ $user_kpi->kpi?->max_score }}</strong>
                                            </div>

                                            @if (!empty($user_kpi->score?->feedback))
                                                <div class="current-score mt-2 p-3 rounded" style="background-color: #bd7c7c; border-left: 4px solid #7c4f3b;">
                                                    <strong>Fikr-mulohaza:</strong>
                                                    <p class="mb-0">{{ $user_kpi->score->feedback }}</p>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            // Yangi vazifa qoʻshish
            $('.task-form').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);
                let taskList = form.closest('.kpi-child-body').find('.task-list');
                let submitBtn = form.find('button[type="submit"]');
                let originalBtnText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Yuklanmoqda...');

                $.ajax({
                    url: '/tasks/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (task) {
                        taskList.find('.empty-state').remove();

                        let taskHtml = `
                    <div class="task-item fade-in" data-task-id="${task.id}" data-score="${task.score ?? ''}">
                        <div class="task-header d-flex justify-content-between">
                            <div class="task-content">
                                <h6>${task.title}</h6>
                                <p>${task.description}</p>
                                ${task.file_url ? `<a href="${task.file_url}" target="_blank" class="task-file-link"><i class="fa fa-paperclip"></i> Ilova</a>` : ''}
                                ${task.score ? `<div class="task-score mt-2"><strong>Baho:</strong> ${task.score}</div>` : ''}
                            </div>
                            ${!task.score ? `
                            <div class="task-actions">
                                <button class="btn btn-sm btn-modern edit-task-btn btn-info"><i class="fa fa-pencil"></i> Tahrirlash</button>
                                <button class="btn btn-sm btn-modern delete-task btn-danger"><i class="fa fa-trash"></i> O‘chirish</button>
                            </div>` : ''}
                        </div>
                        ${!task.score ? `
                        <form class="edit-task-form d-none" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="task_id" value="${task.id}">
                            <div class="mb-3">
                                <label class="form-label">Vazifa nomi</label>
                                <input type="text" name="title" class="form-control" value="${task.title}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tavsif</label>
                                <textarea name="description" class="form-control" rows="3" required>${task.description}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fayl</label>
                                <input type="file" name="file" class="form-control">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success btn-modern"><i class="fa fa-save"></i> Saqlash</button>
                                <button type="button" class="btn btn-secondary btn-modern cancel-edit"><i class="fa fa-times"></i> Bekor qilish</button>
                            </div>
                        </form>` : ''}
                    </div>`;

                        taskList.append(taskHtml);
                        form.trigger('reset');
                        showNotification('Vazifa muvaffaqiyatli qoʻshildi!', 'success');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let messages = '';
                            for (let field in errors) {
                                messages += `<div>${errors[field].join('<br>')}</div>`;
                            }
                            showNotification(messages, 'error');
                        } else {
                            showNotification('Xatolik yuz berdi. Qaytadan urinib koʻring.', 'error');
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // O‘chirishni faqat scoreni tekshirib bajarish
            $(document).on('click', '.delete-task', function () {
                let taskItem = $(this).closest('.task-item');
                let taskId = taskItem.data('task-id');
                let score = taskItem.data('score');
                let taskTitle = taskItem.find('h6').text();

                if (score) {
                    showNotification('Bu vazifaga baho qo‘yilgan. O‘chirish mumkin emas.', 'error');
                    return;
                }

                if (confirm(`"${taskTitle}" vazifasini o‘chirmoqchimisiz?`)) {
                    taskItem.append('<div class="loading-overlay"><div class="spinner"></div></div>');

                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function () {
                            taskItem.fadeOut(300, function () {
                                $(this).remove();
                                let taskList = taskItem.closest('.task-list');
                                if (taskList.find('.task-item').length === 0) {
                                    taskList.html(`<div class="empty-state fade-in">
                                    <div class="empty-state-icon"><i class="fa fa-sticky-note"></i></div>
                                    <p>Hozircha hech qanday vazifa mavjud emas. Quyida yangi vazifa yarating.</p>
                                </div>`);
                                    taskList.closest('.kpi-child-body').find('#add-task-form').show();
                                }
                            });
                            showNotification('Vazifa o‘chirildi!', 'success');
                        },
                        error: function () {
                            taskItem.find('.loading-overlay').remove();
                            showNotification('O‘chirishda xatolik yuz berdi.', 'error');
                        }
                    });
                }
            });

            $(document).on('click', '.edit-task-btn', function () {
                let taskItem = $(this).closest('.task-item');
                let form = taskItem.find('.edit-task-form');
                form.toggleClass('d-none');
                $(this).html(form.hasClass('d-none') ? '<i class="fa fa-edit"></i> Tahrirlash' : '<i class="fa fa-times"></i> Bekor qilish');
            });

            $(document).on('click', '.cancel-edit', function () {
                let form = $(this).closest('.edit-task-form');
                form.addClass('d-none');
                form.closest('.task-item').find('.edit-task-btn').html('<i class="fa fa-edit"></i> Tahrirlash');
            });

            $(document).on('submit', '.edit-task-form', function (e) {
                e.preventDefault();

                const form = $(this);
                const taskId = form.find('input[name="task_id"]').val();
                const taskItem = form.closest('.task-item');
                const submitBtn = form.find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                const formData = new FormData(this);

                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saqlanmoqda...');

                $.ajax({
                    url: `/tasks/update/${taskId}`,
                    type: 'POST', // Use POST if Laravel's Route::put() is spoofed
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (task) {
                        const content = taskItem.find('.task-content');
                        content.find('h6').text(task.title);
                        content.find('p').text(task.description);

                        const fileLink = content.find('.task-file-link');
                        if (task.file_url) {
                            if (fileLink.length) {
                                fileLink.attr('href', task.file_url);
                            } else {
                                content.append(`<a href="${task.file_url}" target="_blank" class="task-file-link"><i class="fa fa-paperclip"></i> Ilova</a>`);
                            }
                        } else {
                            fileLink.remove();
                        }

                        // Update UI
                        form.addClass('d-none');
                        taskItem.find('.edit-task-btn').html('<i class="fa fa-edit"></i> Tahrirlash');
                        showNotification('Vazifa muvaffaqiyatli yangilandi!', 'success');
                    },
                    error: function (xhr) {
                        console.error(xhr);
                        if (xhr.status === 422) {
                            let messages = '';
                            const errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                messages += `<div>${errors[key].join('<br>')}</div>`;
                            }
                            showNotification(messages, 'error');
                        } else if (xhr.status === 403) {
                            showNotification(xhr.responseJSON?.message || 'Tahrirlashga ruxsat yo‘q.', 'error');
                        } else {
                            showNotification('Yangilashda xatolik yuz berdi.', 'error');
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });


            function showNotification(message, type) {
                let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                let icon = type === 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';

                let notification = $(`
                <div class="alert ${alertClass}" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="${icon} me-2"></i>
                    ${message}
                </div>
            `);

                $('body').append(notification);
                setTimeout(() => notification.fadeOut(300, () => notification.remove()), 3000);
            }
        });
    </script>
@endsection
