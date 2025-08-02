@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/employee-users-kpi.css') }}">
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-bar-chart-2 mr-1"></i>&nbsp; {{ __('Xodimlarning KPI Ko‘rsatkichlari') }}
                </li>
            </ol>
        </div>

        <!-- Filter Form -->
        <form id="filterForm" method="GET" action="{{ route('employee.kpis.users') }}">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="search-box">
                        <input type="text" name="search" id="searchUsers" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="department_id" id="departmentFilter" class="form-select">
                        <option value="">Barcha bo'limlar</option>
                        @foreach($work_zones as $work_zone)
                            <option value="{{ $work_zone->id }}" {{ request('department_id') == $work_zone->id ? 'selected' : '' }}>
                                {{ $work_zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <!-- Users Grid -->
        <div class="row" id="usersGrid">
            @foreach($users as $user)
                <div class="col-lg-3 col-md-3 mb-3 user-card-wrapper" data-user-name="{{ strtolower($user->name) }}" data-department="{{ $user->department ?? '' }}">
                    <div class="user-card" onclick="goToUserKpis({{ $user->id }})">
                        <div class="user-avatar">

                            @if($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->first_name }}">
                            @else
                                <img src="{{ asset('img/employee/avtar.png') }}" alt="No Photo">
                            @endif
                        </div>
                        <div class="user-info">
                            <h5 class="user-name">{{ $user->full_name }}</h5>
                            <p class="user-email">{{ $user->lavozimi }}</p>
                            @if($user->department)
                                <span class="department-badge">{{ $user->department }}</span>
                            @endif
                        </div>
                        <div class="user-stats">
                            <div class="stat-item">
                                <span class="stat-number">{{ $user->user_kpis->count() ?? 0 }}</span>
                                <span class="stat-label">KPIs ko'rsatkichalari</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">{{ $user->user_kpis->sum('target_score') ?? 0 }}</span>
                                <span class="stat-label">Umumiy natijasi</span>
                            </div>
                        </div>
                        <div class="card-arrow">
                            <i class="fa fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $users->links() }}

        @if($users->isEmpty())
            <div class="empty-state">
                <i class="fa fa-users fa-3x"></i>
                <h4>Foydalanuvchilar topilmadi</h4>
                <p>Hali hech qanday foydalanuvchi qo'shilmagan.</p>
            </div>
        @endif
    </div>
@endsection
@section('scripts')
    <script>
        function goToUserKpis(userId) {
            window.location.href = `/employee/users/${userId}/kpis`;
        }

        $(document).ready(function() {
            $('#searchUsers, #departmentFilter').on('change input', function () {
                clearTimeout(window.filterTimeout);
                window.filterTimeout = setTimeout(function () {
                    $('#filterForm').submit();
                }, 500); // Debounce
            });
        });
    </script>
@endsection
