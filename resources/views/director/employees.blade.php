@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/employee.css') }}">

@section('content')
    <div class="section">
        <!-- PAGE-HEADER -->
        <div class="page-header mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-users mr-1"></i>&nbsp; Bo'lim xodimlari ro'yxati
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <!-- Tab Navigation -->

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fa fa-users text-white fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1 small">Jami xodimlar</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ count($users) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-info bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fa fa-clipboard text-white fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1 small">Jami kpi ko'rsatkichlari</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $users->sum(function($user) { return $user->user_kpis->count(); }) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-success bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fa fa-check-circle text-white fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1 small">Tekshirilgan</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $users->sum(function($user) { return $user->kpis->whereNotNull('current_score')->count(); }) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fa fa-clock-o text-white fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1 small">Kutilmoqda</h6>
                                    @php
                                        $totalTasks = $users->sum(function($user) { return $user->kpis->count(); });
                                        $completedTasks = $users->sum(function($user) { return $user->kpis->whereNotNull('current_score')->count(); });
                                        $pendingTasks = $totalTasks - $completedTasks;
                                    @endphp
                                    <h3 class="mb-0 fw-bold text-dark">{{ $pendingTasks }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <!-- Enhanced Card Header -->
                    <div class="card-header bg-white border-bottom">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="tab-indicator active me-3"></div>
                                    <div>
                                        <h5 class="mb-0 fw-semibold">
                                            <i class="fa fa-list me-2 text-primary"></i>
                                            {{ trans('app.Ro\'yxat')}}
                                        </h5>
                                        <small class="text-muted">Xodimlar va ularning topshiriqlarini boshqaring</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Search Input -->
                                    <div class="search-box">
                                        <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fa fa-search text-muted"></i>
                                        </span>
                                            <input type="text" class="form-control border-start-0" placeholder="Xodim qidirish..." id="searchEmployee">
                                        </div>
                                    </div>
                                    <!-- Back Button -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <!-- Flash Messages -->
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-check-circle me-2"></i>
                                    <div>{{ $message }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @elseif($message = Session::get('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-alert-circle me-2"></i>
                                    <div>{{ $message }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Enhanced Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="employeesTable">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col" class="border-0 px-4 py-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label fw-semibold text-dark" for="selectAll">#</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="border-0 px-4 py-3 fw-semibold text-dark">Xodim ma'lumotlari</th>
                                    <th scope="col" class="border-0 px-4 py-3 fw-semibold text-dark">Lavozimi</th>
                                    <th scope="col" class="border-0 px-4 py-3 fw-semibold text-dark">Baholar holati</th>
                                    <th scope="col" class="border-0 px-4 py-3 fw-semibold text-dark">Baholanish foizi</th>
                                    <th scope="col" class="border-0 px-4 py-3 fw-semibold text-dark">Harakatlar</th>
                                </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                @foreach ($users as $index => $employee)
                                    @php
                                        $totalTasks = $employee->kpis->count();
                                        $checkedTasks = $employee->kpis->whereNotNull('current_score')->count();
                                        $pendingTasks = $totalTasks - $checkedTasks;
                                        $completionPercentage = $totalTasks > 0 ? round(($checkedTasks / $totalTasks) * 100) : 0;

                                        // Determine progress color and status
                                        $progressColor = 'secondary';
                                        $statusText = 'Boshlangan emas';
                                        $statusClass = 'secondary';

                                        if ($totalTasks > 0) {
                                            if ($completionPercentage == 100) {
                                                $progressColor = 'success';
                                                $statusText = 'Yakunlangan';
                                                $statusClass = 'success';
                                            } elseif ($completionPercentage >= 75) {
                                                $progressColor = 'info';
                                                $statusText = 'Deyarli tayyor';
                                                $statusClass = 'info';
                                            } elseif ($completionPercentage >= 50) {
                                                $progressColor = 'warning';
                                                $statusText = 'Jarayonda';
                                                $statusClass = 'warning';
                                            } elseif ($completionPercentage > 0) {
                                                $progressColor = 'primary';
                                                $statusText = 'Boshlangan';
                                                $statusClass = 'primary';
                                            }
                                        }
                                    @endphp
                                    <tr class="employee-row" data-completion="{{ $completionPercentage }}" data-status="{{ $statusClass }}">
                                        <td class="px-4 py-3">
                                            <div class="form-check">
                                                <input class="form-check-input employee-checkbox" type="checkbox" value="{{ $employee->id }}">
                                                <label class="form-check-label fw-bold text-primary">{{ $index + 1 }}</label>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="employee-avatar me-3">
                                                    <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="fa fa-user-md"></i>
                                                        {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="employee-info">
                                                    <h6 class="mb-1 fw-semibold text-dark">{{ $employee->first_name . ' ' . $employee->last_name }}</h6>
                                                    <small class="text-muted">ID: #{{ $employee->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                                <i class="fa fa-briefcase me-1"></i>
                                                {{ $employee->lavozimi }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="task-stats">
                                                <div class="row g-2 mb-2">
                                                    <div class="col-6">
                                                        <div class="stat-item text-center p-2 bg-light rounded">
                                                            <div class="stat-number fw-bold text-info">{{ $totalTasks }}</div>
                                                            <small class="text-muted">Jami</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="stat-item text-center p-2 bg-light rounded">
                                                            <div class="stat-number fw-bold text-success">{{ $checkedTasks }}</div>
                                                            <small class="text-muted">Baholangan</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-2 py-1">
                                                    {{ $statusText }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="progress-container">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">Baholangan</small>
                                                    <small class="fw-semibold text-{{ $progressColor }}">{{ $completionPercentage }}%</small>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $progressColor }} progress-bar-animated"
                                                         style="width: {{ $completionPercentage }}%"
                                                         role="progressbar"
                                                         aria-valuenow="{{ $completionPercentage }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="action-buttons">
                                                <a href="{!! url('director-profile/check-user' , ["type"=>1,"employee"=>$employee]) !!}"
{{--                                                <a href="{{ route('director.check_user', $employee) }}"--}}
                                                   class="btn btn-primary btn-sm me-1 action-btn"
                                                   data-bs-toggle="tooltip"
                                                   title="Xodimni tekshirish">
                                                    <i class="fa fa-check me-1 text-white"></i>
                                                    <span class="d-none d-lg-inline">Tekshirish</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Initialize tooltips
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                });

                                // Search functionality
                                const searchInput = document.getElementById('searchEmployee');
                                const employeeRows = document.querySelectorAll('.employee-row');

                                searchInput.addEventListener('input', function() {
                                    const searchTerm = this.value.toLowerCase();

                                    employeeRows.forEach(row => {
                                        const employeeName = row.querySelector('.employee-info h6').textContent.toLowerCase();
                                        const employeePosition = row.querySelector('.badge').textContent.toLowerCase();

                                        if (employeeName.includes(searchTerm) || employeePosition.includes(searchTerm)) {
                                            row.style.display = '';
                                        } else {
                                            row.style.display = 'none';
                                        }
                                    });

                                    updateEmptyState();
                                });

                                // Select all functionality
                                const selectAllCheckbox = document.getElementById('selectAll');
                                const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');

                                selectAllCheckbox.addEventListener('change', function() {
                                    employeeCheckboxes.forEach(checkbox => {
                                        checkbox.checked = this.checked;
                                    });
                                });

                                // Update select all when individual checkboxes change
                                employeeCheckboxes.forEach(checkbox => {
                                    checkbox.addEventListener('change', function() {
                                        const checkedCount = document.querySelectorAll('.employee-checkbox:checked').length;
                                        selectAllCheckbox.checked = checkedCount === employeeCheckboxes.length;
                                        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < employeeCheckboxes.length;
                                    });
                                });

                                // Auto-hide alerts
                                setTimeout(function() {
                                    const alerts = document.querySelectorAll('.alert');
                                    alerts.forEach(alert => {
                                        const bsAlert = new bootstrap.Alert(alert);
                                        bsAlert.close();
                                    });
                                }, 5000);
                            });

                            function refreshData() {
                                location.reload();
                            }

                            function filterEmployees(filter) {
                                const employeeRows = document.querySelectorAll('.employee-row');

                                employeeRows.forEach(row => {
                                    const completion = parseInt(row.dataset.completion);
                                    const status = row.dataset.status;

                                    let shouldShow = true;

                                    switch(filter) {
                                        case 'completed':
                                            shouldShow = completion === 100;
                                            break;
                                        case 'pending':
                                            shouldShow = completion < 100;
                                            break;
                                        case 'all':
                                        default:
                                            shouldShow = true;
                                            break;
                                    }

                                    row.style.display = shouldShow ? '' : 'none';
                                });

                                updateEmptyState();
                            }

                            function updateEmptyState() {
                                const visibleRows = document.querySelectorAll('.employee-row[style=""], .employee-row:not([style])');
                                const emptyState = document.querySelector('.empty-state');

                                if (visibleRows.length === 0 && !emptyState) {
                                    // Show no results message
                                    console.log('No visible employees');
                                }
                            }

                            function exportSelected(format) {
                                const selectedEmployees = Array.from(document.querySelectorAll('.employee-checkbox:checked')).map(cb => cb.value);

                                if (selectedEmployees.length === 0) {
                                    alert('Iltimos, kamida bitta xodimni tanlang.');
                                    return;
                                }

                                // Here you would implement the actual export functionality
                                console.log(`Exporting ${selectedEmployees.length} employees to ${format}`);
                                alert(`${selectedEmployees.length} ta xodim ma'lumotlari ${format.toUpperCase()} formatida yuklab olinmoqda...`);
                            }
                        </script>
@endsection
