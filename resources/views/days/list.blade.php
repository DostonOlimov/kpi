@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/employee-days.css') }}">
@section('content')
    <div class="container-fluid px-4">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-calendar mr-1"></i>&nbsp; Xodimlarning ish kunlari
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
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{$users->count()}}</div>
                    <div>Jami xodimlar</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $days }}</div>
                    <div>Oylik ish kunlari</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $groupedUsers->count() }}</div>
                    <div>Bo'limlar soni</div>
                </div>
            </div>
            <div class="col-md-3">
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
                            <th><i class="fa fa-calendar-days me-1"></i>Oylik ish kuni</th>
                            <th><i class="fa fa-calendar-check me-1"></i>Ishga chiqilgan kun</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr data-user-id="{{ $user->id }}" class="user-row">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><strong>{{ $user->first_name . ' '.$user->last_name }}</strong></td>
                                <td>{{ $user->lavozimi }}</td>
                                <td><span class="badge bg-info">{{ $month_name }}</span></td>
                                <td><span class="total-days-badge">{{ $days }} kun</span></td>
                                <td>
                                    <div class="working-days-wrapper" data-user-id="{{ $user->id }}" data-user-name="{{ $user->first_name . ' '.$user->last_name }}">
                                        @if (!$user->working_days)
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control working-days-input"
                                                       placeholder="Kun" min="0" max="{{ $days }}"
                                                       data-original-value="0" />
                                                <button class="btn btn-success save-working-days-btn"
                                                        data-month="{{ $month_id }}" data-year="{{ $year }}"
                                                        title="Saqlash (Ctrl+S)">
                                            <span class="btn-text">
                                                <i class="fa fa-save"></i> Saqlash
                                            </span>
                                                    <span class="btn-loading d-none">
                                                <i class="fa fa-spinner fa-spin"></i> Saqlanmoqda...
                                            </span>
                                                </button>
                                            </div>
                                        @else
                                            <div class="working-days-display">
                                                <span class="working-days-text badge bg-primary">{{ $user->working_days->days }} kun</span>
                                                <button class="btn btn-sm btn-primary edit-working-days-btn ms-2"
                                                        data-month="{{ $month_id }}" data-year="{{ $year }}"
                                                        data-current-days="{{ $user->working_days->days }}"
                                                        title="Tahrirlash (E)">
                                                    <i class="fa fa-edit"></i>Tahrirlash
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
                this.maxDays = {{ $days }};
                this.csrfToken = '{{ csrf_token() }}';
                this.currentEditingWrapper = null;
                this.autoSaveTimeout = null;
                this.init();
            }

            init() {
                this.bindEvents();
                this.setupKeyboardShortcuts();
                this.setupAutoSave();
            }

            bindEvents() {
                // Use event delegation for better performance
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
                if (e.target.classList.contains('working-days-input')) {
                    this.validateInput(e.target);
                    this.scheduleAutoSave(e.target);
                }
            }

            handleKeydown(e) {
                // Handle keyboard shortcuts
                if (e.target.classList.contains('working-days-input')) {
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
                    // Ctrl+S to save current editing
                    if (e.ctrlKey && e.key === 's' && this.currentEditingWrapper) {
                        e.preventDefault();
                        const saveBtn = this.currentEditingWrapper.querySelector('.save-working-days-btn');
                        if (saveBtn) this.handleSave(saveBtn);
                    }
                });
            }

            setupAutoSave() {
                // Auto-save after 3 seconds of inactivity
                this.autoSaveDelay = 3000;
            }

            scheduleAutoSave(input) {
                clearTimeout(this.autoSaveTimeout);
                this.autoSaveTimeout = setTimeout(() => {
                    const saveBtn = input.closest('.working-days-wrapper').querySelector('.save-working-days-btn');
                    if (saveBtn && this.validateInput(input, false)) {
                        this.showToast('Auto-saving...', 'info');
                        this.handleSave(saveBtn, true);
                    }
                }, this.autoSaveDelay);
            }

            handleEdit(editBtn) {
                const wrapper = editBtn.closest('.working-days-wrapper');
                const currentValue = editBtn.dataset.currentDays;
                const month = editBtn.dataset.month;
                const year = editBtn.dataset.year;

                // Cancel any other editing
                this.cancelAllEditing();

                wrapper.classList.add('editing');
                this.currentEditingWrapper = wrapper;

                wrapper.innerHTML = `
            <div class="input-group input-group-sm fade-in">
                <input type="number" class="form-control working-days-input"
                       value="${currentValue}" min="0" max="${this.maxDays}"
                       data-original-value="${currentValue}"
                       placeholder="Kun kiriting" autofocus />
                <button class="btn btn-success save-working-days-btn"
                        data-month="${month}" data-year="${year}"
                        title="Saqlash (Enter)">
                    <span class="btn-text">
                        <i class="fa fa-save"></i>
                    </span>
                    <span class="btn-loading d-none">
                        <i class="fa fa-spinner fa-spin"></i>
                    </span>
                </button>
                <button class="btn btn-secondary cancel-edit-btn"
                        data-original-value="${currentValue}"
                        data-month="${month}" data-year="${year}"
                        title="Bekor qilish (Esc)">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <small class="text-muted mt-1 d-block">
                <i class="fa fa-info-circle"></i> Ish kunini tahrirlash
            </small>
        `;

                // Focus on input
                const input = wrapper.querySelector('.working-days-input');
                input.focus();
                input.select();
            }

            handleSave(saveBtn, isAutoSave = false) {
                const wrapper = saveBtn.closest('.working-days-wrapper');
                const userId = wrapper.dataset.userId;
                const userName = wrapper.dataset.userName;
                const input = wrapper.querySelector('.working-days-input');
                const days = input.value.trim();
                const month = saveBtn.dataset.month;
                const year = saveBtn.dataset.year;
                const originalValue = input.dataset.originalValue;

                // Validate input
                if (!this.validateInput(input)) {
                    return;
                }

                // Check if value changed
                if (days === originalValue && !isAutoSave) {
                    this.showAlert('info', 'Hech qanday o\'zgarish kiritilmadi.');
                    this.handleCancel(saveBtn);
                    return;
                }

                this.setLoadingState(wrapper, true);

                this.saveWorkingDays(userId, days, month, year)
                    .then(() => {
                        this.updateUI(wrapper, days, month, year, userName);
                        this.showAlert('success', `${userName} uchun ish kunlari muvaffaqiyatli saqlandi!`);
                        this.highlightRow(wrapper.closest('tr'));

                        if (!isAutoSave) {
                            this.currentEditingWrapper = null;
                        }
                    })
                    .catch((error) => {
                        this.showAlert('error', `Xatolik: ${error.message}`);
                        this.shakeElement(wrapper);
                    })
                    .finally(() => {
                        this.setLoadingState(wrapper, false);
                    });
            }

            handleCancel(cancelBtn) {
                const wrapper = cancelBtn.closest('.working-days-wrapper');
                const originalValue = cancelBtn.dataset.originalValue;
                const month = cancelBtn.dataset.month;
                const year = cancelBtn.dataset.year;

                wrapper.classList.remove('editing');
                this.currentEditingWrapper = null;

                wrapper.innerHTML = `
            <div class="working-days-display fade-in">
                <span class="working-days-text badge bg-primary">${originalValue} kun</span>
                <button class="btn btn-sm btn-primary edit-working-days-btn ms-2"
                        data-month="${month}" data-year="${year}"
                        data-current-days="${originalValue}"
                        title="Tahrirlash (E)">
                    <i class="fa fa-edit"></i>Tahrirlash
                </button>
            </div>
        `;
            }

            async saveWorkingDays(userId, days, month, year) {
                const response = await fetch(`/days/createday/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ days, month, year })
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

                // Remove previous validation classes
                input.classList.remove('is-invalid', 'is-valid');

                if (value === '') {
                    if (showError) {
                        input.classList.add('is-invalid');
                        this.showToast('Ish kunini kiriting', 'error');
                    }
                    return false;
                }

                if (isNaN(numValue) || numValue < 0 || numValue > this.maxDays) {
                    if (showError) {
                        input.classList.add('is-invalid');
                        this.showToast(`Ish kuni 0 dan ${this.maxDays} gacha bo'lishi kerak`, 'error');
                        this.shakeElement(input);
                    }
                    return false;
                }

                input.classList.add('is-valid');
                return true;
            }

            updateUI(wrapper, days, month, year, userName) {
                wrapper.classList.remove('editing');
                wrapper.innerHTML = `
            <div class="working-days-display fade-in">
                <span class="working-days-text badge bg-primary">${days} kun</span>
                <button class="btn btn-sm btn-primary edit-working-days-btn ms-2"
                        data-month="${month}" data-year="${year}"
                        data-current-days="${days}"
                        title="Tahrirlash (E)">
                    <i class="fa fa-edit"></i>Tahrirlash
                </button>
            </div>
        `;
            }

            setLoadingState(wrapper, isLoading) {
                const btnText = wrapper.querySelector('.btn-text');
                const btnLoading = wrapper.querySelector('.btn-loading');

                if (btnText && btnLoading) {
                    if (isLoading) {
                        btnText.classList.add('d-none');
                        btnLoading.classList.remove('d-none');
                        wrapper.classList.add('saving');
                    } else {
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                        wrapper.classList.remove('saving');
                    }
                }
            }

            cancelAllEditing() {
                const editingWrappers = document.querySelectorAll('.working-days-wrapper.editing');
                editingWrappers.forEach(wrapper => {
                    const cancelBtn = wrapper.querySelector('.cancel-edit-btn');
                    if (cancelBtn) {
                        this.handleCancel(cancelBtn);
                    }
                });
            }

            highlightRow(row) {
                row.classList.add('updated');
                setTimeout(() => {
                    row.classList.remove('updated');
                }, 2000);
            }

            shakeElement(element) {
                element.classList.add('shake');
                setTimeout(() => {
                    element.classList.remove('shake');
                }, 500);
            }

            showAlert(type, message) {
                const alertId = type === 'success' ? 'successAlert' : 'errorAlert';
                const messageId = type === 'success' ? 'successMessage' : 'errorMessage';

                document.getElementById(messageId).textContent = message;
                const alertElement = document.getElementById(alertId);
                alertElement.classList.remove('d-none');

                // Auto hide after 5 seconds
                setTimeout(() => {
                    alertElement.classList.add('d-none');
                }, 5000);

                // Scroll to top to show alert
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            showToast(message, type = 'info') {
                // Simple toast notification (you can enhance this)
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
            <i class="fa fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
        `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }

            showConfirmation(message, onConfirm) {
                document.getElementById('confirmationMessage').textContent = message;
                const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));

                const confirmBtn = document.getElementById('confirmActionBtn');
                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                newConfirmBtn.addEventListener('click', () => {
                    modal.hide();
                    onConfirm();
                });

                modal.show();
            }
        }

        // Initialize the manager when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            new WorkingDaysManager();
        });
    </script>
@endsection
