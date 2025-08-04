@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/employee-days.css') }}">
@section('content')
    <div class="container-fluid px-4">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-calendar mr-1"></i>&nbsp;Takliflar ishlab chiqqanligi, amaliyatga joriy etganligi va oʼz tashabbusi bilan qoʼshimcha vazifalarni bajarganligi
                </li>
            </ol>
        </div>

        <!-- Success Alert -->
        <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            <span id="successMessage"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Error Alert -->
        <div id="errorAlert" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <span id="errorMessage"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">{{$users->count()}}</div>
                    <div>Jami xodimlar</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">{{ $groupedUsers->count() }}</div>
                    <div>Bo'limlar soni</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">{{ $month_name }}</div>
                    <div>Joriy oy</div>
                </div>
            </div>
        </div>

        @foreach($groupedUsers as $departmentName => $users)
            <div class="department-header">
                <h4><i class="fa fa-users me-2"></i>{{ $departmentName }}</h4>
            </div>
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><i class="fa fa-hashtag me-1"></i></th>
                            <th><i class="fa fa-user me-1"></i>Ism Sharifi</th>
                            <th><i class="fa fa-briefcase me-1"></i>Lavozimi</th>
                            <th><i class="fa fa-calendar me-1"></i>Oy</th>
                            <th style="width: 50%"><i class="fa fa-calendar-check me-1"></i>Xodimning tashabbuskorligi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr data-user-id="{{ $user->id }}" class="user-row">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><strong>{{ $user->first_name . ' '.$user->last_name }}</strong></td>
                                <td>{{ $user->lavozimi }}</td>
                                <td><span class="badge bg-info">{{ $month_name }}</span></td>
                                <td>
                                    <div class="working-days-wrapper" data-user-id="{{ $user->id }}" data-user-name="{{ $user->first_name . ' '.$user->last_name }}">
                                        @php $user_kpi = $user->user_kpis->where('kpi_id',11)->first() @endphp
                                        @if (!$user_kpi->current_score)
                                            <div class="input-group input-group-sm flex-nowrap">
                                                <input type="number" class="form-control result-input me-1"
                                                       placeholder="Ball"
                                                       min="0" max="{{ $kpi->max_score }}"
                                                       data-original-value="0" />

                                                <textarea class="form-control feedback-input"
                                                          placeholder="Izoh kiriting"
                                                          rows="2"
                                                          data-original-value2="0"
                                                          style="resize: vertical;"></textarea>

                                                <button class="btn btn-success save-working-days-btn"
                                                        data-month="{{ $month_id }}" data-year="{{ $year }}"
                                                        title="Saqlash (Ctrl+S)">
                                                    <span class="btn-text"><i class="fa fa-save"></i> Saqlash</span>
                                                    <span class="btn-loading d-none"><i class="fa fa-spinner fa-spin"></i> Saqlanmoqda...</span>
                                                </button>
                                            </div>
                                        @else
                                            <div class="working-days-display">
                                                    <span class="result-text badge bg-primary me-2">
                                                        <i class="fa fa-star me-1"></i>{{ round($user_kpi->current_score) }}
                                                    </span>
                                                <textarea class="form-control feedback-text"
                                                          placeholder="Izoh kiriting"
                                                          rows="2"
                                                          data-original-value2="0"
                                                          style="resize: vertical;" readonly>{{ $user_kpi->score?->feedback }}</textarea>
                                                <button
                                                    class="btn btn-sm btn-primary edit-working-days-btn"
                                                    data-month="{{ $month_id }}"
                                                    data-year="{{ $year }}"
                                                    data-current-result="{{ $user_kpi->current_score }}"
                                                    data-current-feedback="{{ $user_kpi->score?->feedback }}"
                                                    title="Tahrirlash (E)">
                                                    <i class="fa fa-pencil me-1"></i> Tahrirlash
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="confirmationModalLabel">
                        <i class="fa fa-exclamation-triangle me-2"></i>Tasdiqlash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Bekor qilish
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmActionBtn">
                        <i class="fa fa-check me-1"></i>Tasdiqlash
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        class WorkingDaysManager {
            constructor() {
                this.maxDays = {{ $kpi->max_score }};
                this.csrfToken = '{{ csrf_token() }}';
                this.currentEditingWrapper = null;
                this.autoSaveTimeout = null;
                this.autoSaveDelay = 3000;
                this.init();
            }

            init() {
                this.bindEvents();
                this.setupKeyboardShortcuts();
            }

            bindEvents() {
                document.addEventListener('click', this.handleClick.bind(this));
                document.addEventListener('input', this.handleInput.bind(this));
                document.addEventListener('keydown', this.handleKeydown.bind(this));
            }

            handleClick(e) {
                const editBtn = e.target.closest('.edit-working-days-btn');
                const saveBtn = e.target.closest('.save-working-days-btn');
                const cancelBtn = e.target.closest('.cancel-edit-btn');

                if (editBtn) {
                    this.handleEdit(editBtn);
                } else if (saveBtn) {
                    this.handleSave(saveBtn);
                } else if (cancelBtn) {
                    this.handleCancel(cancelBtn);
                }
            }

            handleInput(e) {
                if (e.target.classList.contains('result-input')) {
                    this.validateInput(e.target);
                }
                if (e.target.classList.contains('feedback-input')) {
                    this.scheduleAutoSave(e.target);
                }
            }

            handleKeydown(e) {
                if (e.target.classList.contains('result-input')) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const saveBtn = e.target.closest('.working-days-wrapper').querySelector('.save-working-days-btn');
                        if (saveBtn) this.handleSave(saveBtn);
                    } else if (e.key === 'Escape') {
                        const cancelBtn = e.target.closest('.working-days-wrapper').querySelector('.cancel-edit-btn');
                        if (cancelBtn) this.handleCancel(cancelBtn);
                    }
                }
            }

            setupKeyboardShortcuts() {
                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.key === 's' && this.currentEditingWrapper) {
                        e.preventDefault();
                        const saveBtn = this.currentEditingWrapper.querySelector('.save-working-days-btn');
                        if (saveBtn) this.handleSave(saveBtn);
                    }
                });
            }

            scheduleAutoSave(input) {
                clearTimeout(this.autoSaveTimeout);
                this.autoSaveTimeout = setTimeout(() => {
                    const wrapper = input.closest('.working-days-wrapper');
                    const saveBtn = wrapper.querySelector('.save-working-days-btn');
                    if (saveBtn) {
                        this.showToast('Auto-saving...', 'info');
                        this.handleSave(saveBtn, true);
                    }
                }, this.autoSaveDelay);
            }

            handleEdit(editBtn) {
                const wrapper = editBtn.closest('.working-days-wrapper');
                const result = editBtn.dataset.currentResult || '';
                const feedback = editBtn.dataset.currentFeedback || '';
                const month = editBtn.dataset.month;
                const year = editBtn.dataset.year;

                this.cancelAllEditing();
                wrapper.classList.add('editing');
                this.currentEditingWrapper = wrapper;

                wrapper.innerHTML = `
                <div class="input-group input-group-sm fade-in">
                    <input type="number" class="form-control result-input me-1"
                           value="${result}" min="0" max="${this.maxDays}"
                           placeholder="Ball (0-${this.maxDays})" autofocus />
                    <textarea class="form-control feedback-input me-1"
                              placeholder="Izoh kiriting" rows="2"
                              style="resize: vertical;">${feedback}</textarea>
                    <button class="btn btn-success save-working-days-btn"
                            data-month="${month}" data-year="${year}">
                        <span class="btn-text"><i class="fa fa-save"></i></span>
                        <span class="btn-loading d-none"><i class="fa fa-spinner fa-spin"></i></span>
                    </button>
                    <button class="btn btn-secondary cancel-edit-btn"
                            data-original-value="${result}" data-original-feedback="${feedback}"
                            data-month="${month}" data-year="${year}">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <small class="text-muted mt-1 d-block">
                    <i class="fa fa-info-circle me-1"></i> Ball va izohni tahrirlash
                </small>
            `;

                const input = wrapper.querySelector('.result-input');
                input.focus();
                input.select();
            }

            handleSave(saveBtn, isAutoSave = false) {
                const wrapper = saveBtn.closest('.working-days-wrapper');
                const userId = wrapper.dataset.userId;
                const userName = wrapper.dataset.userName;
                const resultInput = wrapper.querySelector('.result-input');
                const feedbackInput = wrapper.querySelector('.feedback-input');
                const result = resultInput.value.trim();
                const feedback = feedbackInput.value.trim();
                const month = saveBtn.dataset.month;
                const year = saveBtn.dataset.year;

                if (!this.validateInput(resultInput)) return;

                this.setLoadingState(wrapper, true);

                this.saveWorkingDays(userId, result, feedback, month, year)
                    .then(() => {
                        this.updateUI(wrapper, result, feedback, month, year, userName);
                        this.showAlert('success', `${userName} uchun natijalar muvaffaqiyatli saqlandi!`);
                        this.highlightRow(wrapper.closest('tr'));
                        if (!isAutoSave) this.currentEditingWrapper = null;
                    })
                    .catch((error) => {
                        this.showAlert('error', `Xatolik: ${error.message}`);
                        this.shakeElement(wrapper);
                    })
                    .finally(() => this.setLoadingState(wrapper, false));
            }

            handleCancel(cancelBtn) {
                const wrapper = cancelBtn.closest('.working-days-wrapper');
                const result = cancelBtn.dataset.originalValue || '0';
                const feedback = cancelBtn.dataset.originalFeedback || '';
                const month = cancelBtn.dataset.month;
                const year = cancelBtn.dataset.year;

                wrapper.classList.remove('editing');
                this.currentEditingWrapper = null;

                wrapper.innerHTML = `
                <div class="working-days-display fade-in">
                    <span class="result-text badge bg-primary">${result}</span>
                    <textarea class="feedback-text mt-1 form-control"
                             rows="2" style="resize: vertical;" readonly>${feedback}</textarea>
                    <button class="btn btn-sm btn-primary edit-working-days-btn mt-2"
                            data-month="${month}" data-year="${year}"
                            data-current-result="${result}"
                            data-current-feedback="${feedback}">
                        <i class="fa fa-edit me-1"></i> Tahrirlash
                    </button>
                </div>
            `;
            }

            async saveWorkingDays(userId, result, feedback, month, year) {
                const response = await fetch(`/days/create-activity/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ result, feedback, month, year })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Server xatosi: ${response.status}`);
                }

                return response.json();
            }

            validateInput(input, showError = true) {
                const value = input.value.trim();
                const numValue = parseInt(value);
                input.classList.remove('is-invalid', 'is-valid');

                if (value === '') {
                    if (showError) {
                        input.classList.add('is-invalid');
                        this.showToast('Natijani kiriting', 'error');
                    }
                    return false;
                }

                if (isNaN(numValue) || numValue < 0 || numValue > this.maxDays) {
                    if (showError) {
                        input.classList.add('is-invalid');
                        this.showToast(`Ball 0 dan ${this.maxDays} gacha bo'lishi kerak`, 'error');
                        this.shakeElement(input);
                    }
                    return false;
                }

                input.classList.add('is-valid');
                return true;
            }

            updateUI(wrapper, result, feedback, month, year, userName) {
                wrapper.classList.remove('editing');
                wrapper.innerHTML = `
                <div class="working-days-display fade-in">
                    <span class="result-text badge bg-primary">${result}</span>
                     <textarea class="feedback-text mt-1 form-control"
                             rows="2" style="resize: vertical;" readonly>${feedback}</textarea>
                    <button class="btn btn-sm btn-primary edit-working-days-btn mt-2"
                            data-month="${month}" data-year="${year}"
                            data-current-result="${result}"
                            data-current-feedback="${feedback}">
                        <i class="fa fa-edit me-1"></i> Tahrirlash
                    </button>
                </div>
            `;
            }

            setLoadingState(wrapper, isLoading) {
                const btnText = wrapper.querySelector('.btn-text');
                const btnLoading = wrapper.querySelector('.btn-loading');

                if (btnText && btnLoading) {
                    btnText.classList.toggle('d-none', isLoading);
                    btnLoading.classList.toggle('d-none', !isLoading);
                    wrapper.classList.toggle('saving', isLoading);
                }
            }

            cancelAllEditing() {
                document.querySelectorAll('.working-days-wrapper.editing').forEach(wrapper => {
                    const cancelBtn = wrapper.querySelector('.cancel-edit-btn');
                    if (cancelBtn) this.handleCancel(cancelBtn);
                });
            }

            highlightRow(row) {
                row.classList.add('updated');
                setTimeout(() => row.classList.remove('updated'), 2000);
            }

            shakeElement(element) {
                element.classList.add('shake');
                setTimeout(() => element.classList.remove('shake'), 500);
            }

            showAlert(type, message) {
                const alertId = type === 'success' ? 'successAlert' : 'errorAlert';
                const messageId = type === 'success' ? 'successMessage' : 'errorMessage';

                document.getElementById(messageId).textContent = message;
                const alert = document.getElementById(alertId);
                alert.classList.remove('d-none');

                setTimeout(() => alert.classList.add('d-none'), 5000);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
                <i class="fa fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
        }

        document.addEventListener('DOMContentLoaded', () => new WorkingDaysManager());
    </script>
@endsection
