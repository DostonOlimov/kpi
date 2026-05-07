@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/commission/behavior.css') }}">
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid px-4">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-calendar mr-1"></i>&nbsp; &nbsp;{{ $title }}
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

        <!-- Work Zone Filter Component -->
        <x-work-zone-filter
            :actionUrl="route('commission.band_scores.list', $id)"
            :showLabel="true"
            :autoSubmit="true"
        />

        <!-- KPI Selection -->
        <div class="card mb-4" style="border:none;box-shadow:0 4px 16px rgba(0,0,0,0.1);border-radius:14px;overflow:hidden;">
            <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg,#3c4b64 0%,#4f6080 100%);border-bottom:2px solid rgba(255,255,255,0.1);padding:1rem 1.5rem;">
                <div class="d-flex align-items-center" style="gap:0.75rem;">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-list" style="color:#fff;font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <span style="color:#fff;font-weight:600;font-size:1rem;display:block;line-height:1.2;">Ko'rsatkichni tanlang</span>
                        <small style="color:rgba(255,255,255,0.55);font-size:0.75rem;">{{ $title }}</small>
                    </div>
                </div>
                <div class="loading-indicator d-none" id="loadingIndicator">
                    <div class="spinner-border spinner-border-sm me-1" role="status" style="width:14px;height:14px;border-width:2px;"></div>
                    Yuklanmoqda...
                </div>
            </div>
            <div class="card-body" style="background:#f4f6fb;padding:1.1rem 1.5rem;">
                <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                    @foreach($kpis as $kpi)
                        <button type="button"
                                onclick="selectKpi({{ $kpi->id }}, this)"
                                class="btn kpi-pill-btn {{ $selectedKpiId == $kpi->id ? 'kpi-active' : '' }}">
                            {{ $kpi->name }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" id="kpiSelect" value="{{ $selectedKpiId ?? '' }}">
            </div>
        </div>

        <!-- Department Tables -->
        <div id="departmentTables">
            @foreach($groupedUsers as $departmentName => $users)
                <div class="department-section mb-4">
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
                                    <th><i class="fa fa-calendar-check me-1"></i>Ko'rsatkichlar</th>
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
                                            <div class="working-days-wrapper"
                                                 data-user-id="{{ $user->id }}"
                                                 data-user-name="{{ $user->first_name . ' '.$user->last_name }}">
                                                <div class="kpi-content row" data-user-id="{{ $user->id }}">
                                                    <div class="text-muted">Ko'rsatkichni tanlang</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingIndicator = document.getElementById('loadingIndicator');

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });

            function showLoading(show) {
                if (show) {
                    loadingIndicator.classList.remove('d-none');
                } else {
                    loadingIndicator.classList.add('d-none');
                }
            }

            function resetKpiContent() {
                document.querySelectorAll('.kpi-content').forEach(content => {
                    content.innerHTML = '<div class="text-muted">Ko\'rsatkichni tanlang</div>';
                });
            }

            function updateKpiContent(kpiId) {
                const userRows = document.querySelectorAll('.user-row');
                setTimeout(() => {
                    userRows.forEach(row => {
                        const userId = row.dataset.userId;
                        const kpiContent = row.querySelector('.kpi-content');
                        fetchUserKpiData(userId, kpiId)
                            .then(data => updateUserKpiDisplay(kpiContent, data, userId, kpiId))
                            .catch(error => {
                                console.error('Error fetching KPI data:', error);
                                kpiContent.innerHTML = '<div class="text-danger">Xatolik yuz berdi</div>';
                            });
                    });
                    showLoading(false);
                }, 500);
            }

            async function fetchUserKpiData(userId, kpiId) {
                const response = await fetch(`/api/user-kpi-data/${userId}/${kpiId}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                return await response.json();
            }

            function updateUserKpiDisplay(container, data, userId, kpiId) {
                const ATTENDANCE_KPI_ID = {{ \App\Models\Kpi::BEHAVIOUR == $id ? 8 : 0 }};
                const isAttendanceKpi  = (parseInt(kpiId) === ATTENDANCE_KPI_ID);

                let html = '';
                if (data.hasScore && data.currentScore) {
                    const editUrl = isAttendanceKpi
                        ? `/commission-profile/attendance-score/${kpiId}/${userId}`
                        : `/commission-profile/check-user-edit/${kpiId}/${userId}`;
                    html = `
                        <div class="kpi-score col-md-1 text-center">${data.currentScore}</div>
                        <div class="working-days-display col-md-11">
                            <div class="action-buttons p-2">
                                <a href="${editUrl}"
                                   class="btn btn-primary btn-sm me-1 action-btn"
                                   data-bs-toggle="tooltip" title="Tahrirlash">
                                    <i class="fa fa-pencil me-1 text-white"></i>
                                    <span class="d-none d-lg-inline">Tahrirlash</span>
                                </a>
                            </div>
                        </div>`;
                } else {
                    const addUrl = isAttendanceKpi
                        ? `/commission-profile/attendance-score/${kpiId}/${userId}`
                        : `/commission-profile/check-user/${kpiId}/${userId}`;
                    html = `
                        <div class="working-days-display">
                            <div class="action-buttons">
                                <a href="${addUrl}"
                                   class="btn btn-success btn-sm me-1 action-btn"
                                   data-bs-toggle="tooltip" title="Baholash">
                                    <i class="fa fa-plus me-1 text-white"></i>
                                    <span class="d-none d-lg-inline">Natijalarni qo'shish</span>
                                </a>
                            </div>
                        </div>`;
                }
                container.innerHTML = html;
            }

            window.selectKpi = function(kpiId, btn) {
                // Toggle active styles
                document.querySelectorAll('.kpi-pill-btn').forEach(b => {
                    b.classList.remove('kpi-active');
                    b.style.background = '#fff';
                    b.style.color = '#4f6080';
                    b.style.boxShadow = 'none';
                });
                btn.classList.add('kpi-active');
                btn.style.background = 'linear-gradient(135deg,#3c4b64,#4f6080)';
                btn.style.color = '#fff';
                btn.style.boxShadow = '0 2px 6px rgba(63,98,128,0.35)';

                // Save selected KPI to session
                fetch('/commission-profile/save-selected-kpi', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        kpi_id: kpiId,
                        kpi_type: '{{ $id }}'
                    })
                }).catch(error => console.error('Error saving KPI to session:', error));

                if (!kpiId) {
                    resetKpiContent();
                    return;
                }

                showLoading(true);
                updateKpiContent(kpiId);
            };

            // Auto-load if KPI was previously selected
            const preselected = document.getElementById('kpiSelect').value;
            if (preselected) {
                showLoading(true);
                updateKpiContent(preselected);
            }
        });
    </script>
@endsection
