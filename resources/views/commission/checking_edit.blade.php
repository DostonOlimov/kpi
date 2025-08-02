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
                            Xodimning shaxsiy ko'rsatkichlari ({{ $user->first_name . ' ' . $user->last_name }})
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="tab_wrapper page-tab">
                    <ul class="tab_list mb-0">
                        <li>
                            <a href="{{ url()->previous()  }}">
                                <i class="fa fa-list fa-lg"></i>&nbsp; {{ __("Ro'yxat") }}
                            </a>
                        </li>
                        <li class="active">
                            <i class="fa fa-pencil fa-lg"></i>&nbsp; <strong>{{ __('Tahrirlash') }}</strong>
                        </li>
                    </ul>
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
                    <h5 class="modal-title text-white" id="editCriteriaModalLabel">
                        <i class="fa fa-edit me-2"></i>Mezonni tahrirlash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
                </div>
                <div class="modal-body">
                    <form id="editCriteriaForm">
                        @csrf
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
                    <h5 class="modal-title text-white" id="editCommentsModalLabel">
                        <i class="fa fa-comment me-2 "></i>Izohni tahrirlash
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
        document.addEventListener('DOMContentLoaded', () => {
            // === Modals ===
            const editCriteriaModal = new bootstrap.Modal(document.getElementById('editCriteriaModal'));
            const editCommentsModal = new bootstrap.Modal(document.getElementById('editCommentsModal'));

            // === Data ===
            let currentCriteriaId = null;
            let isLoading = false;
            const criteriaData = @json($user_kpi->kpi->criterias ?? []);
            const currentScores = @json($criteria_scores ?? []);

            // === Init ===
            initModals();
            initEventListeners();

            // === Init modal cancel buttons ===
            function initModals() {
                document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const modal = bootstrap.Modal.getInstance(btn.closest('.modal'));
                        modal?.hide();
                    });
                });

                document.querySelector('#editCriteriaModal .btn-secondary')?.addEventListener('click', () => editCriteriaModal.hide());
                document.querySelector('#editCommentsModal .btn-secondary')?.addEventListener('click', () => editCommentsModal.hide());
            }

            // === Main UI Event Listeners ===
            function initEventListeners() {
                document.querySelectorAll('.btn-edit-criteria').forEach(btn => {
                    btn.addEventListener('click', handleEditCriteriaClick);
                });

                document.querySelector('.btn-edit-comments')?.addEventListener('click', handleEditCommentsClick);

                document.getElementById('saveCriteriaBtn')?.addEventListener('click', saveCriteriaScore);
                document.getElementById('saveCommentsBtn')?.addEventListener('click', saveComments);

                document.querySelector('.kpi-header')?.addEventListener('keydown', e => {
                    if (['Enter', ' '].includes(e.key)) {
                        e.preventDefault();
                        toggleKpi(e.currentTarget);
                    }
                });
            }

            // === Handlers ===
            function handleEditCriteriaClick(e) {
                if (isLoading) return;

                const btn = e.currentTarget;
                const criteriaId = btn.dataset.criteriaId;
                const criteriaName = btn.dataset.criteriaName;

                if (!criteriaId || !criteriaName) return showAlert("Xatolik: Mezon topilmadi", 'danger');

                currentCriteriaId = criteriaId;
                document.getElementById('editCriteriaName').textContent = criteriaName;
                loadScoreOptions(criteriaId);
                editCriteriaModal.show();
            }

            function handleEditCommentsClick() {
                if (isLoading) return;

                const currentComments = document.querySelector('.comments-display')?.textContent.trim();
                document.getElementById('editCommentsText').value = currentComments === 'Izoh qoldirilmagan' ? '' : currentComments;
                editCommentsModal.show();
            }

            function loadScoreOptions(criteriaId) {
                const criteria = criteriaData.find(c => c.id == criteriaId);
                if (!criteria?.bands) return showAlert("Xatolik: Mezon yoki ballar topilmadi", 'danger');

                const container = document.getElementById('editScoreOptions');
                const currentScore = currentScores[criteriaId] ?? 0;

                container.innerHTML = criteria.bands.map(band => {
                    const selected = band.fine_ball == currentScore;
                    return `
                <div class="score-option-edit ${selected ? 'selected' : ''}" role="radio"
                     aria-checked="${selected}" tabindex="${selected ? 0 : -1}" data-score="${band.fine_ball}">
                    <input type="radio" name="edit_score" value="${band.fine_ball}"
                           id="edit_score_${band.fine_ball}" ${selected ? 'checked' : ''}>
                    <div class="score-icon-display me-3"><i class="fa ${getIconClass(band.fine_ball)}"></i></div>
                    <div>
                        <strong>${escapeHtml(band.name)}</strong>
                        <div class="text-muted">Ball: ${band.fine_ball}</div>
                    </div>
                </div>
            `;
                }).join('');

                container.querySelectorAll('.score-option-edit').forEach(option => {
                    option.addEventListener('click', () => selectScoreOption(option));
                    option.addEventListener('keydown', e => {
                        if (['Enter', ' '].includes(e.key)) {
                            e.preventDefault();
                            selectScoreOption(option);
                        }
                    });
                });
            }

            function selectScoreOption(option) {
                const container = option.closest('#editScoreOptions');
                container.querySelectorAll('.score-option-edit').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.setAttribute('aria-checked', 'false');
                    opt.setAttribute('tabindex', '-1');
                });

                option.classList.add('selected');
                option.setAttribute('aria-checked', 'true');
                option.setAttribute('tabindex', '0');

                option.querySelector('input[type="radio"]')?.click();
                option.focus();
            }

            // === Submit Score ===
            async function saveCriteriaScore() {
                if (isLoading) return;

                const selected = document.querySelector('input[name="edit_score"]:checked');
                if (!selected) return showAlert('Iltimos, ball tanlang!', 'warning');

                const scoreValue = selected.value;
                if (!scoreValue) return showAlert('Xatolik: Ball topilmadi', 'danger');

                setLoading(true);

                try {
                    const res = await fetch(`{{ route('commission.update_criteria_score') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken()
                        },
                        body: JSON.stringify({
                            user_id: {{ $user->id }},
                            kpi_id: {{ $kpi->id }},
                            criteria_id: currentCriteriaId,
                            score: scoreValue
                        })
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || `HTTP error ${res.status}`);

                    if (data.success) {
                        updateCriteriaDisplay(currentCriteriaId, scoreValue, data.band_name);
                        currentScores[currentCriteriaId] = parseInt(scoreValue);
                        if (data.total_score) updateTotalScore(data.total_score);
                        editCriteriaModal.hide();
                        showAlert('Muvaffaqiyatli saqlandi!', 'success');
                    } else {
                        showAlert(data.message || 'Xatolik yuz berdi!', 'danger');
                    }
                } catch (err) {
                    console.error(err);
                    showAlert('Server bilan bog\'lanishda xatolik yuz berdi!', 'danger');
                } finally {
                    setLoading(false);
                }
            }

            // === Submit Comments ===
            async function saveComments() {
                if (isLoading) return;

                const feedback = document.getElementById('editCommentsText')?.value.trim();
                if (feedback === undefined) return;

                setLoading(true);

                try {
                    const res = await fetch(`{{ route('commission.update_comments') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken()
                        },
                        body: JSON.stringify({
                            user_id: {{ $user->id }},
                            kpi_id: {{ $kpi->id }},
                            feedback
                        })
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || `HTTP error ${res.status}`);

                    if (data.success) {
                        const display = document.querySelector('.comments-display');
                        display.textContent = feedback || 'Izoh qoldirilmagan';
                        display.classList.add('pulse-animation');
                        setTimeout(() => display.classList.remove('pulse-animation'), 500);
                        editCommentsModal.hide();
                        showAlert('Izoh saqlandi!', 'success');
                    } else {
                        showAlert(data.message || 'Xatolik yuz berdi!', 'danger');
                    }
                } catch (err) {
                    console.error(err);
                    showAlert('Server bilan bog\'lanishda xatolik yuz berdi!', 'danger');
                } finally {
                    setLoading(false);
                }
            }

            // === Helpers ===
            function updateCriteriaDisplay(criteriaId, score, bandName) {
                const card = document.querySelector(`[data-criteria-id="${criteriaId}"]`);
                if (!card) return;

                const display = card.querySelector('.selected-score');
                display.innerHTML = `
            <div class="score-icon-display"><i class="fa ${getIconClass(score)}"></i></div>
            <div class="score-text"><strong>${escapeHtml(bandName)}</strong><div class="score-value">Ball: ${score}</div></div>
        `;

                display.classList.add('pulse-animation');
                setTimeout(() => display.classList.remove('pulse-animation'), 500);
            }

            function updateTotalScore(score) {
                const badge = document.querySelector('.badge.bg-primary');
                if (badge) {
                    const [, max] = badge.textContent.split(' / ');
                    badge.textContent = max ? `${score} / ${max}` : score;
                }
            }

            function setLoading(loading) {
                isLoading = loading;
                loading ? showLoadingOverlay() : hideLoadingOverlay();

                document.querySelectorAll('#saveCriteriaBtn, #saveCommentsBtn').forEach(btn => {
                    btn.disabled = loading;
                    btn.innerHTML = loading
                        ? `<i class="fa fa-spinner fa-spin me-2"></i>Saqlanmoqda...`
                        : `<i class="fa fa-save me-2"></i>Saqlash`;
                });
            }

            function showLoadingOverlay() {
                if (document.getElementById('loadingOverlay')) return;
                const overlay = document.createElement('div');
                overlay.id = 'loadingOverlay';
                overlay.className = 'loading-overlay';
                overlay.innerHTML = `
            <div class="loading-content">
                <div class="spinner-border text-primary mb-3" role="status"><span class="visually-hidden">Yuklanmoqda...</span></div>
                <div>Saqlanmoqda...</div>
            </div>
        `;
                document.body.appendChild(overlay);
            }

            function hideLoadingOverlay() {
                document.getElementById('loadingOverlay')?.remove();
            }

            function showAlert(message, type) {
                const container = document.getElementById('alert-container');
                const id = 'alert-' + Date.now();
                const icon = {
                    success: 'check-circle',
                    warning: 'exclamation-triangle',
                    danger: 'exclamation-circle',
                    info: 'info-circle'
                }[type] || 'info-circle';

                container.innerHTML = `
            <div id="${id}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fa fa-${icon} me-2"></i>${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Yopish"></button>
            </div>
        `;

                setTimeout(() => document.getElementById(id)?.remove(), 5000);
                container.scrollIntoView({ behavior: 'smooth' });
            }

            function getIconClass(score) {
                if (score == 0) return 'fa-check text-success';
                if (score == 1) return 'fa-minus text-warning';
                return 'fa-times text-danger';
            }

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            function getCSRFToken() {
                return '{{ csrf_token() }}';
            }
        });

        // === Toggle KPI Expand/Collapse ===
        function toggleKpi(el) {
            const card = el.closest('.kpi-card');
            const content = card.querySelector('.kpi-content');
            const icon = el.querySelector('.kpi-toggle i');
            const expanded = card.classList.contains('active');

            card.classList.toggle('active');
            content.style.display = expanded ? 'none' : 'block';
            icon.classList.toggle('fa-chevron-down', !expanded);
            icon.classList.toggle('fa-chevron-right', expanded);
            el.setAttribute('aria-expanded', !expanded);
        }
    </script>
@endsection
