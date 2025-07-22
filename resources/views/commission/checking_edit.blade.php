@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/commission/checking-edit.css') }}">
@section('content')
    <div class="main-container">
        <div class="container-fluid px-4">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <i class="fa fa-user-circle me-2"></i>
                            Xodimning shaxsiy ko'rsatkichlari
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- User Information Section -->
            <div class="user-info-section">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="info-card">
                            <div class="info-title">
                                <i class="fa fa-user-circle"></i>
                                Xodim ma'lumotlari
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ism-sharifi</span>
                                <span class="info-value">{{ $user->first_name . ' ' . $user->last_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Lavozimi</span>
                                <span class="info-value">{{ $user->lavozimi }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Bo'limi</span>
                                <span class="info-value">{{ $user->work_zone->name ?? 'Belgilanmagan' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="info-card">
                            <div class="info-title">
                                <i class="fa fa-calendar-alt"></i>
                                Baholash ma'lumotlari
                            </div>
                            <div class="info-item">
                                <span class="info-label">Sana</span>
                                <span class="info-value">{{ date('d.m.Y') }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Baholanuvchi oy</span>
                                <span class="info-value">{{ $month_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Baholovchi</span>
                                <span class="info-value">{{ auth()->user()->name ?? 'Komissiya azosi' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="evaluation-card fade-in">
                        <div class="card-body">
                            <!-- Alert Container -->
                            <div id="alert-container" role="alert" aria-live="polite"></div>

                            <!-- Read-only View -->
                            <div id="readonly-view">
                                @if($user_kpi)
                                    <div class="kpi-card fade-in active">
                                        <div class="kpi-header" onclick="toggleKpi(this)" role="button" tabindex="0" aria-expanded="true">
                                            <div class="kpi-title">
                                                <i class="fa fa-tasks"></i>
                                                {{ $user_kpi->kpi->name }}
                                            </div>
                                            <div class="kpi-toggle">
                                                <i class="fa fa-chevron-down"></i>
                                            </div>
                                        </div>
                                        <div class="kpi-content">
                                            @foreach($user_kpi->kpi->criterias as $index => $criteria)
                                                <div class="criteria-card fade-in"
                                                     style="animation-delay: {{ $index * 0.1 }}s"
                                                     data-criteria="{{ $index }}"
                                                     data-criteria-id="{{ $criteria->id }}">
                                                    <div class="criteria-header">
                                                        <div class="criteria-number">{{ $index + 1 }}</div>
                                                        <div class="criteria-title">{{ $criteria->name }}</div>
                                                        <div class="criteria-description">{{ $criteria->description }}</div>
                                                        <div class="edit-button-container">
                                                            <button type="button"
                                                                    class="btn-edit-criteria"
                                                                    data-criteria-id="{{ $criteria->id }}"
                                                                    data-criteria-name="{{ $criteria->name }}"
                                                                    aria-label="Mezonni tahrirlash">
                                                                <i class="fa fa-pencil"></i>
                                                                <span class="d-none d-sm-inline">Tahrirlash</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="score-display">
                                                        @php
                                                            $savedScore = $criteria_scores[$criteria->id] ?? 0;
                                                            $selectedBand = $criteria->bands->where('fine_ball', $savedScore)->first();
                                                        @endphp
                                                        <div class="selected-score" data-score="{{ $savedScore }}">
                                                            <div class="score-icon-display">
                                                                @if($savedScore == 0)
                                                                    <i class="fa fa-check text-success"></i>
                                                                @elseif($savedScore == 1)
                                                                    <i class="fa fa-minus text-warning"></i>
                                                                @else
                                                                    <i class="fa fa-times text-danger"></i>
                                                                @endif
                                                            </div>
                                                            <div class="score-text">
                                                                <strong>{{ $selectedBand ? $selectedBand->name : 'Tanlanmagan' }}</strong>
                                                                <div class="score-value">Ball: {{ $savedScore }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Comments Section -->
                                <div class="comments-section fade-in">
                                    <div class="comments-header">
                                        <div style="display: flex; align-items: center;">
                                            <div class="comments-icon">
                                                <i class="fa fa-comment"></i>
                                            </div>
                                            <div class="comments-title">Qo'shimcha izohlar</div>
                                        </div>
                                        <button type="button"
                                                class="btn-edit-comments"
                                                aria-label="Izohni tahrirlash">
                                            <i class="fa fa-pencil"></i>
                                            <span class="d-none d-sm-inline">Tahrirlash</span>
                                        </button>
                                    </div>
                                    <div class="comments-display">
                                        {{ $user_kpi->score->feedback ?? 'Izoh qoldirilmagan' }}
                                    </div>
                                </div>

                                <!-- Score Summary -->
                                <div class="alert alert-info" role="status">
                                    <i class="fa fa-info-circle me-2"></i>
                                    Ushbu ko'rsatkich bo'yicha baho:
                                    <span class="badge bg-primary">{{ $user_kpi->current_score }} / {{ $user_kpi->target_score }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Criteria Modal -->
    <div class="modal fade" id="editCriteriaModal" tabindex="-1" aria-labelledby="editCriteriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCriteriaModalLabel">
                        <i class="fa fa-edit me-2"></i>Mezonni tahrirlash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
                </div>
                <div class="modal-body">
                    <form id="editCriteriaForm">
                        <input type="hidden" id="editCriteriaId">
                        <div class="mb-3">
                            <label class="form-label" id="editCriteriaName"></label>
                            <div id="editScoreOptions" class="score-options-edit" role="radiogroup">
                                <!-- Score options will be loaded here -->
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Bekor qilish
                    </button>
                    <button type="button" class="btn btn-primary" id="saveCriteriaBtn">
                        <i class="fa fa-save me-2"></i>Saqlash
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Comments Modal -->
    <div class="modal fade" id="editCommentsModal" tabindex="-1" aria-labelledby="editCommentsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCommentsModalLabel">
                        <i class="fa fa-comment me-2"></i>Izohni tahrirlash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
                </div>
                <div class="modal-body">
                    <form id="editCommentsForm">
                        <div class="mb-3">
                            <label for="editCommentsText" class="form-label">Qo'shimcha izohlar</label>
                            <textarea class="form-control"
                                      id="editCommentsText"
                                      rows="5"
                                      placeholder="Iltimos, xodimning ish faoliyati, kuchli tomonlari, takomillashtirish yo'nalishlari va tavsiyalar haqida batafsil fikr-mulohaza bildiring..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Bekor qilish
                    </button>
                    <button type="button" class="btn btn-primary" id="saveCommentsBtn">
                        <i class="fa fa-save me-2"></i>Saqlash
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals with proper options
            const editCriteriaModal = new bootstrap.Modal(document.getElementById('editCriteriaModal'));
            const editCommentsModal = new bootstrap.Modal(document.getElementById('editCommentsModal'));

            // Fix cancel buttons - add event listeners
            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (modal) {
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                });
            });

            // Alternative: Direct event listeners for cancel buttons
            document.querySelector('#editCriteriaModal .btn-secondary').addEventListener('click', function() {
                editCriteriaModal.hide();
            });

            document.querySelector('#editCommentsModal .btn-secondary').addEventListener('click', function() {
                editCommentsModal.hide();
            });


            // Global variables
            let currentCriteriaId = null;
            let criteriaData = @json($user_kpi->kpi->criterias ?? []);
            let currentScores = @json($criteria_scores ?? []);
            let isLoading = false;

            // Initialize event listeners
            initializeEventListeners();

            function initializeEventListeners() {
                // Edit Criteria Button Click
                document.querySelectorAll('.btn-edit-criteria').forEach(button => {
                    button.addEventListener('click', handleEditCriteriaClick);
                });

                // Edit Comments Button Click
                const editCommentsBtn = document.querySelector('.btn-edit-comments');
                if (editCommentsBtn) {
                    editCommentsBtn.addEventListener('click', handleEditCommentsClick);
                }

                // Save buttons
                document.getElementById('saveCriteriaBtn').addEventListener('click', saveCriteriaScore);
                document.getElementById('saveCommentsBtn').addEventListener('click', saveComments);

                // Keyboard navigation for KPI header
                document.querySelector('.kpi-header').addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggleKpi(this);
                    }
                });
            }

            function handleEditCriteriaClick(event) {
                if (isLoading) return;

                const button = event.currentTarget;
                const criteriaId = button.dataset.criteriaId;
                const criteriaName = button.dataset.criteriaName;

                if (!criteriaId || !criteriaName) {
                    showAlert('Xatolik: Mezon ma\'lumotlari topilmadi!', 'danger');
                    return;
                }

                currentCriteriaId = criteriaId;

                // Set modal content
                document.getElementById('editCriteriaName').textContent = criteriaName;
                document.getElementById('editCriteriaId').value = criteriaId;

                // Load score options
                loadScoreOptions(criteriaId);

                // Show modal
                editCriteriaModal.show();
            }

            function handleEditCommentsClick() {
                if (isLoading) return;

                const currentComments = document.querySelector('.comments-display').textContent.trim();
                const commentsText = currentComments === 'Izoh qoldirilmagan' ? '' : currentComments;
                document.getElementById('editCommentsText').value = commentsText;
                editCommentsModal.show();
            }

            function loadScoreOptions(criteriaId) {
                const criteria = criteriaData.find(c => c.id == criteriaId);
                if (!criteria || !criteria.bands) {
                    showAlert('Xatolik: Mezon ma\'lumotlari topilmadi!', 'danger');
                    return;
                }

                const container = document.getElementById('editScoreOptions');
                const currentScore = currentScores[criteriaId] || 0;

                let html = '';
                criteria.bands.forEach((band, index) => {
                    const isSelected = band.fine_ball == currentScore;
                    const iconClass = getIconClass(band.fine_ball);

                    html += `
                <div class="score-option-edit ${isSelected ? 'selected' : ''}"
                     data-score="${band.fine_ball}"
                     role="radio"
                     aria-checked="${isSelected}"
                     tabindex="${isSelected ? '0' : '-1'}">
                    <input type="radio"
                           name="edit_score"
                           value="${band.fine_ball}"
                           id="edit_score_${band.fine_ball}"
                           ${isSelected ? 'checked' : ''}
                           aria-describedby="score_desc_${band.fine_ball}">
                    <div class="score-icon-display me-3">
                        <i class="fa ${iconClass}"></i>
                    </div>
                    <div>
                        <strong>${escapeHtml(band.name)}</strong>
                        <div class="text-muted" id="score_desc_${band.fine_ball}">Ball: ${band.fine_ball}</div>
                    </div>
                </div>
            `;
                });

                container.innerHTML = html;

                // Add event listeners to score options
                container.querySelectorAll('.score-option-edit').forEach(option => {
                    option.addEventListener('click', handleScoreOptionClick);
                    option.addEventListener('keydown', handleScoreOptionKeydown);
                });
            }

            function handleScoreOptionClick(event) {
                const option = event.currentTarget;
                selectScoreOption(option);
            }

            function handleScoreOptionKeydown(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    selectScoreOption(event.currentTarget);
                }
            }

            function selectScoreOption(option) {
                const container = document.getElementById('editScoreOptions');

                // Remove selected class from all options
                container.querySelectorAll('.score-option-edit').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.setAttribute('aria-checked', 'false');
                    opt.setAttribute('tabindex', '-1');
                });

                // Add selected class to clicked option
                option.classList.add('selected');
                option.setAttribute('aria-checked', 'true');
                option.setAttribute('tabindex', '0');

                // Check the radio button
                const radio = option.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }

                // Focus the selected option
                option.focus();
            }

            function saveCriteriaScore() {
                if (isLoading) return;

                const selectedScore = document.querySelector('input[name="edit_score"]:checked');
                if (!selectedScore) {
                    showAlert('Iltimos, ball tanlang!', 'warning');
                    return;
                }

                const scoreValue = selectedScore.value;

                // Validate score value
                if (scoreValue === null || scoreValue === undefined) {
                    showAlert('Xatolik: Noto\'g\'ri ball qiymati!', 'danger');
                    return;
                }

                // Show loading
                setLoading(true);

                // Make AJAX request
                fetch(`{{ route('commission.update_criteria_score') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: {{ $user->id }},
                        kpi_id: {{ $kpi->id }},
                        criteria_id: currentCriteriaId,
                        score: scoreValue
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        setLoading(false);

                        if (data.success) {
                            // Update the display
                            updateCriteriaDisplay(currentCriteriaId, scoreValue, data.band_name);

                            // Update current scores
                            currentScores[currentCriteriaId] = parseInt(scoreValue);

                            // Update total score if provided
                            if (data.total_score !== undefined) {
                                updateTotalScore(data.total_score);
                            }

                            // Close modal
                            editCriteriaModal.hide();

                            // Show success message
                            showAlert('Muvaffaqiyatli saqlandi!', 'success');
                        } else {
                            showAlert(data.message || 'Xatolik yuz berdi!', 'danger');
                        }
                    })
                    .catch(error => {
                        setLoading(false);
                        console.error('Error:', error);
                        showAlert('Server bilan bog\'lanishda xatolik yuz berdi!', 'danger');
                    });
            }

            function saveComments() {
                if (isLoading) return;

                const comments = document.getElementById('editCommentsText').value.trim();

                // Show loading
                setLoading(true);

                // Make AJAX request
                fetch(`{{ route('commission.update_comments') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: {{ $user->id }},
                        kpi_id: {{ $kpi->id }},
                        feedback: comments
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        setLoading(false);

                        if (data.success) {
                            // Update the display
                            const commentsDisplay = document.querySelector('.comments-display');
                            commentsDisplay.textContent = comments || 'Izoh qoldirilmagan';

                            // Add animation
                            commentsDisplay.classList.add('pulse-animation');
                            setTimeout(() => {
                                commentsDisplay.classList.remove('pulse-animation');
                            }, 500);

                            // Close modal
                            editCommentsModal.hide();

                            // Show success message
                            showAlert('Izoh muvaffaqiyatli saqlandi!', 'success');
                        } else {
                            showAlert(data.message || 'Xatolik yuz berdi!', 'danger');
                        }
                    })
                    .catch(error => {
                        setLoading(false);
                        console.error('Error:', error);
                        showAlert('Server bilan bog\'lanishda xatolik yuz berdi!', 'danger');
                    });
            }

            function updateCriteriaDisplay(criteriaId, score, bandName) {
                const criteriaCard = document.querySelector(`[data-criteria-id="${criteriaId}"]`);
                if (!criteriaCard) return;

                const scoreDisplay = criteriaCard.querySelector('.selected-score');
                const iconClass = getIconClass(score);

                scoreDisplay.innerHTML = `
            <div class="score-icon-display">
                <i class="fa ${iconClass}"></i>
            </div>
            <div class="score-text">
                <strong>${escapeHtml(bandName)}</strong>
                <div class="score-value">Ball: ${score}</div>
            </div>
        `;

                // Add animation
                scoreDisplay.classList.add('pulse-animation');
                setTimeout(() => {
                    scoreDisplay.classList.remove('pulse-animation');
                }, 500);
            }

            function updateTotalScore(totalScore) {
                const badge = document.querySelector('.badge.bg-primary');
                if (badge) {
                    const currentText = badge.textContent;
                    const parts = currentText.split(' / ');
                    if (parts.length === 2) {
                        badge.textContent = `${totalScore} / ${parts[1]}`;
                    } else {
                        badge.textContent = totalScore;
                    }
                }
            }

            function setLoading(loading) {
                isLoading = loading;

                if (loading) {
                    showLoadingOverlay();
                } else {
                    hideLoadingOverlay();
                }

                // Disable/enable buttons
                const buttons = document.querySelectorAll('#saveCriteriaBtn, #saveCommentsBtn');
                buttons.forEach(btn => {
                    btn.disabled = loading;
                    if (loading) {
                        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Saqlanmoqda...';
                    } else {
                        const isComments = btn.id === 'saveCommentsBtn';
                        btn.innerHTML = `<i class="fa fa-save me-2"></i>Saqlash`;
                    }
                });
            }

            function showLoadingOverlay() {
                const existingOverlay = document.getElementById('loadingOverlay');
                if (existingOverlay) return;

                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'loading-overlay';
                loadingOverlay.id = 'loadingOverlay';
                loadingOverlay.innerHTML = `
            <div class="loading-content">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Yuklanmoqda...</span>
                </div>
                <div>Saqlanmoqda...</div>
            </div>
        `;
                document.body.appendChild(loadingOverlay);
            }

            function hideLoadingOverlay() {
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingOverlay) {
                    loadingOverlay.remove();
                }
            }

            function showAlert(message, type) {
                const alertContainer = document.getElementById('alert-container');
                const alertId = 'alert-' + Date.now();

                const iconMap = {
                    'success': 'check-circle',
                    'warning': 'exclamation-triangle',
                    'danger': 'exclamation-circle',
                    'info': 'info-circle'
                };

                const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fa fa-${iconMap[type] || 'info-circle'} me-2"></i>
                ${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Yopish"></button>
            </div>
        `;

                alertContainer.innerHTML = alertHtml;

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    const alert = document.getElementById(alertId);
                    if (alert) {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    }
                }, 5000);

                // Scroll to alert
                alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            // Utility functions
            function getIconClass(score) {
                if (score == 0) return 'fa-check text-success';
                if (score == 1) return 'fa-minus text-warning';
                return 'fa-times text-danger';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function getCSRFToken() {
                const token = document.querySelector('meta[name="csrf-token"]');
                return token ? token.getAttribute('content') : '';
            }
        });

        // Toggle KPI function
        function toggleKpi(element) {
            const kpiCard = element.closest('.kpi-card');
            const content = kpiCard.querySelector('.kpi-content');
            const toggle = element.querySelector('.kpi-toggle i');
            const isActive = kpiCard.classList.contains('active');

            if (isActive) {
                kpiCard.classList.remove('active');
                content.style.display = 'none';
                toggle.classList.remove('fa-chevron-down');
                toggle.classList.add('fa-chevron-right');
                element.setAttribute('aria-expanded', 'false');
            } else {
                kpiCard.classList.add('active');
                content.style.display = 'block';
                toggle.classList.remove('fa-chevron-right');
                toggle.classList.add('fa-chevron-down');
                element.setAttribute('aria-expanded', 'true');
            }
        }
    </script>
@endsection
