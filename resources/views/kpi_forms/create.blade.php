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
                            @foreach ($parent_kpis as $parent)
                                <h4 class="category-title">{{ $parent->name }}</h4>
                                @foreach ($user_kpis as $user_kpi)
                                    @if($user_kpi->kpi->parent_id == $parent->id)
                                        <div class="kpi-child-card fade-in">
                                        <div class="kpi-child-header">
                                            <h5>{{ $user_kpi->kpi?->name }}</h5>
                                        </div>

                                        <div class="kpi-child-body" data-child-id="{{ $user_kpi->kpi?->id }}">
                                            <h6 class="task-section-title">Sizning bajargan vazifalaringiz</h6>

                                            <div class="task-list">
                                                @forelse ($user_kpi->tasks ?? [] as $task)
                                                    <div class="task-item" data-task-id="{{ $task->id }}">
                                                        <div class="task-header d-flex justify-content-between">
                                                            <div class="task-content">
                                                                <h6>{{ $task->name }}</h6>
                                                                <p>{{ $task->description }}</p>
                                                                @if ($task->file_path)
                                                                    <a href="{{ asset('storage/' . $task->file_path) }}"
                                                                       target="_blank"
                                                                       class="task-file-link">
                                                                        <i class="fa fa-paperclip"></i> Ilovani ko‘rish
                                                                    </a>
                                                                @endif
                                                            </div>

                                                            @if (!$user_kpi->current_score)
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

                                                        {{-- Edit Task Form (Hidden by Default) --}}
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
                                                                <button type="submit" class="btn btn-success btn-modern"><i class="fa fa-save"></i> Saqlash</button>
                                                                <button type="button" class="btn btn-secondary btn-modern cancel-edit"><i class="fa fa-times"></i> Bekor qilish</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @empty
                                                    <div class="empty-state">
                                                        <div class="empty-state-icon"><i class="fa fa-sticky-note"></i></div>
                                                        <p>Hali hech qanday vazifa qo‘shilmagan. Quyida birinchi vazifangizni yarating!</p>
                                                    </div>
                                                @endforelse
                                            </div>

                                            {{-- Task Creation Section --}}
                                            @if (!$user_kpi->current_score && ($user_kpi->tasks->isEmpty() ?? true))
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
                                            @endif

                                            {{-- Score Display --}}
                                            @if ($user_kpi->current_score)
                                                <div class="current-score mt-3">
                                                    <span>Joriy baho:</span>
                                                    <strong>{{ round($user_kpi->current_score) }}/{{ $user_kpi->kpi?->max_score }}</strong>
                                                </div>
                                                <div class="current-score mt-3" style="background-color: #97908b" >
                                                    <strong>{{ $user_kpi->score?->feedback }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
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
            console.log(formData);

            $.ajax({
                url: '/tasks/store',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (task) {
                    taskList.find('.empty-state').remove();

                    // Hide the add-task-form div on success
                    form.closest('#add-task-form').css('display', 'none');

                    let taskHtml = `
                    <div class="task-item fade-in" data-task-id="${task.id}">
                        <div class="task-header">
                            <div class="task-content flex-grow-1">
                                <h6>${task.title}</h6>
                                <p>${task.description}</p>
                                ${task.file_url ? `<a href="${task.file_url}" target="_blank" class="task-file-link"><i class="fa fa-paperclip"></i> Ilova</a>` : ''}
                            </div>
                            <div class="task-actions">
                                <button class="btn btn-sm btn-modern btn-edit-modern btn-info"><i class="fa fa-edit"></i> Tahrirlash</button>
                                <button class="btn btn-sm btn-modern btn-delete-modern btn-danger"><i class="fa fa-trash"></i> Oʻchirish</button>
                            </div>
                        </div>
                        <form class="edit-task-form d-none" enctype="multipart/form-data">
                            @csrf
                    <input type="hidden" name="task_id" value="${task.id}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Vazifa nomi</label>
                                <input type="text" name="title" class="form-control form-control-modern" value="${task.title}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tavsif</label>
                                <textarea name="description" class="form-control form-control-modern" rows="3" required>${task.description}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Fayl</label>
                                <input type="file" name="file" class="form-control form-control-modern">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success btn-modern"><i class="fa fa-save"></i> Saqlash</button>
                                <button type="button" class="btn btn-secondary btn-modern cancel-edit"><i class="fa fa-times"></i> Bekor qilish</button>
                            </div>
                        </form>
                    </div>`;

                    taskList.append(taskHtml);
                    form.trigger('reset');
                    showNotification('Vazifa muvaffaqiyatli qoʻshildi!', 'success');
                },
                error: function (xhr) {
                    console.log(xhr)
;                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';

                        // Loop through validation errors and format them
                        for (let field in errors) {
                            if (errors.hasOwnProperty(field)) {
                                errors[field].forEach(function (message) {
                                    errorMessages += `<div>${message}</div>`;
                                });
                            }
                        }

                        showNotification(errorMessages, 'error'); // Use your existing notification function
                    } else {
                        showNotification('Xatolik yuz berdi. Qaytadan urinib koʻring.', 'error');
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        // Vazifani o‘chirish
        $(document).on('click', '.delete-task', function () {
            let taskItem = $(this).closest('.task-item');
            let taskId = taskItem.data('task-id');
            let taskTitle = taskItem.find('h6').text();

            if (confirm(`"${taskTitle}" vazifasini o‘chirmoqchimisiz?`)) {
            taskItem.css('position', 'relative').append('<div class="loading-overlay"><div class="spinner"></div></div>');

            $.ajax({
                url: `/tasks/${taskId}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                taskItem.fadeOut(300, function () {
                    $(this).remove();

                    let taskList = taskItem.closest('.task-list');
                    if (taskList.find('.task-item').length === 0) {
                    taskList.html(`
                    <div class="empty-state fade-in">
                        <div class="empty-state-icon"><i class="fa fa-sticky-note"></i></div>
                        <p>Hozircha hech qanday vazifa mavjud emas. Quyida yangi vazifa yarating.</p>
                    </div>
                    `);
                    // Show the add-task-form again if all tasks are deleted
                    taskList.closest('.kpi-child-body').find('#add-task-form').css('display', '');
                    } else {
                    // Also show the add-task-form if it exists and is hidden
                    taskList.closest('.kpi-child-body').find('#add-task-form').css('display', '');
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

        // Vazifani tahrirlash
        $(document).on('click', '.edit-task-btn', function () {
            let taskItem = $(this).closest('.task-item');
            let editForm = taskItem.find('.edit-task-form');

            editForm.toggleClass('d-none');
            $(this).html(editForm.hasClass('d-none') ? '<i class="fa fa-edit"></i> Tahrirlash' : '<i class="fa fa-times"></i> Bekor qilish');
        });

        $(document).on('click', '.cancel-edit', function () {
            let taskItem = $(this).closest('.task-item');
            let editForm = taskItem.find('.edit-task-form');
            let editBtn = taskItem.find('.edit-task-btn');

            editForm.addClass('d-none');
            editBtn.html('<i class="fa fa-edit"></i> Tahrirlash');
        });

        // Vazifani yangilash
        $(document).on('submit', '.edit-task-form', function (e) {
            e.preventDefault();

            let form = $(this);
            let formData = new FormData(this);
            let taskId = form.find('input[name="task_id"]').val();
            let taskItem = form.closest('.task-item');
            let submitBtn = form.find('button[type="submit"]');
            let originalBtnText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saqlanmoqda...');

            $.ajax({
                url: `/tasks/${taskId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (task) {
                    let taskContent = taskItem.find('.task-content');
                    taskContent.find('h6').text(task.title);
                    taskContent.find('p').text(task.description);

                    let fileLink = taskContent.find('.task-file-link');
                    if (task.file_url) {
                        if (fileLink.length) {
                            fileLink.attr('href', task.file_url);
                        } else {
                            taskContent.append(`<a href="${task.file_url}" target="_blank" class="task-file-link"><i class="fa fa-paperclip"></i> Ilova</a>`);
                        }
                    } else {
                        fileLink.remove();
                    }

                    form.addClass('d-none');
                    taskItem.find('.edit-task-btn').html('<i class="fa fa-edit"></i> Tahrirlash');
                    showNotification('Vazifa muvaffaqiyatli yangilandi!', 'success');
                },
                error: function () {
                    showNotification('Yangilashda xatolik yuz berdi.', 'error');
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        function showNotification(message, type) {
            let alertClass = type === 'success' ? 'alert-success-modern' : 'alert-danger';
            let icon = type === 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';

            let notification = $(`
                <div class="alert alert-modern ${alertClass} slide-in" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="${icon} me-2"></i>
                    ${message}
                </div>
            `);

            $('body').append(notification);

            setTimeout(function () {
                notification.fadeOut(300, function () {
                    $(this).remove();
                });
            }, 3000);
        }

        $('a[href^="#"]').on('click', function (event) {
            let target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });
    });
</script>

@endsection
