@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/commission/behavior.css') }}">
@section('content')
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

        <!-- KPI Selection -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="kpi-selector-wrapper">
                    <label for="kpiSelect" class="form-label">
                        <i class="fa fa-chart-line me-2"></i>Ko'rsatkichni tanlang
                    </label>
                    <select id="kpiSelect" class="form-select kpi-select">
                        <option value="">Ko'rsatkichni tanlang...</option>
                        @foreach($kpis as $kpi)
                            <option value="{{ $kpi->id }}" {{ request('kpi_id') == $kpi->id ? 'selected' : '' }}>
                                {{ $kpi->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="loading-indicator d-none" id="loadingIndicator">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Ma'lumotlar yuklanmoqda...
                </div>
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
                                                    <!-- KPI content will be loaded here -->
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
            const kpiSelect = document.getElementById('kpiSelect');
            const loadingIndicator = document.getElementById('loadingIndicator');

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            kpiSelect.addEventListener('change', function() {
                const selectedKpiId = this.value;

                if (!selectedKpiId) {
                    // Reset all KPI content
                    resetKpiContent();
                    return;
                }

                // Show loading indicator
                showLoading(true);

                // Update KPI content for all users
                updateKpiContent(selectedKpiId);
            });

            function showLoading(show) {
                if (show) {
                    loadingIndicator.classList.remove('d-none');
                } else {
                    loadingIndicator.classList.add('d-none');
                }
            }

            function resetKpiContent() {
                const kpiContents = document.querySelectorAll('.kpi-content');
                kpiContents.forEach(content => {
                    content.innerHTML = '<div class="text-muted">Ko\'rsatkichni tanlang</div>';
                });
            }

            function updateKpiContent(kpiId) {
                // Get all user rows
                const userRows = document.querySelectorAll('.user-row');

                // Simulate loading delay
                setTimeout(() => {
                    userRows.forEach(row => {
                        const userId = row.dataset.userId;
                        const kpiContent = row.querySelector('.kpi-content');

                        // Make AJAX request to get KPI data for this user
                        fetchUserKpiData(userId, kpiId)
                            .then(data => {
                                updateUserKpiDisplay(kpiContent, data, userId, kpiId);
                            })
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
                        headers: {
                            'Content-Type': 'application/json',

                        }
                    });
                    return await response.json();
            }

            function updateUserKpiDisplay(container, data, userId, kpiId) {
                let html = '';

                if (data.hasScore && data.currentScore) {
                    html = `
                <div class="kpi-score col-md-1 text-center"  >${data.currentScore}</div>
                <div class="working-days-display col-md-11">
                    <div class="action-buttons p-2">
                        <a href="/commission-profile/check-user-edit/${kpiId}/${userId}"
                           class="btn btn-primary btn-sm me-1 action-btn"
                           data-bs-toggle="tooltip"
                           title="Xodimni tekshirish">
                            <i class="fa fa-pencil me-1 text-white"></i>
                            <span class="d-none d-lg-inline">Tahrirlash</span>
                        </a>
                    </div>
                </div>
            `;
                } else {
                    html = `
                <div class="working-days-display">
                    <div class="action-buttons">
                        <a href="/commission-profile/check-user/${kpiId}/${userId}"
                           class="btn btn-success btn-sm me-1 action-btn"
                           data-bs-toggle="tooltip"
                           title="Xodimni tekshirish">
                            <i class="fa fa-plus me-1 text-white"></i>
                            <span class="d-none d-lg-inline">Natijalarni qo'shish</span>
                        </a>
                    </div>
                </div>
            `;
                }

                container.innerHTML = html;
            }

            // Add smooth scrolling for better UX
            function smoothScrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Auto-select first KPI if available
            if (kpiSelect.options.length > 1) {
                // Uncomment the next line if you want to auto-select the first KPI
                // kpiSelect.selectedIndex = 1;
                // kpiSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection
