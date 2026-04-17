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
        <form id="filterForm" method="GET" action="{{ route('employee.kpis.users', $workZone->id) }}">
            <!-- Work Zone Filter Component -->
            <x-work-zone-filter :actionUrl="route('employee.kpis.users', $workZone->id)" :showLabel="true" :autoSubmit="false" />

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="search-box">
                        <input type="text" name="search" id="searchUsers" class="form-control"
                            placeholder="Search users..." value="{{ request('search') }}">
                    </div>
                </div>
            </div>
        </form>

        <!-- Users Grid -->
        <div class="row" id="usersGrid">
            @foreach ($users as $user)
                <div class="col-lg-3 col-md-3 mb-3 user-card-wrapper" data-user-name="{{ strtolower($user->name) }}"
                    data-department="{{ $user->department ?? '' }}">
                    <div class="user-card" onclick="goToUserKpis({{ $user->id }})">
                        <div class="user-avatar">

                            @if ($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->first_name }}">
                            @else
                                <img src="{{ asset('img/employee/avtar.png') }}" alt="No Photo">
                            @endif
                        </div>
                        <div class="user-info">
                            <h5 class="user-name">{{ $user->full_name }}</h5>
                            <p class="user-email">{{ $user->lavozimi }}</p>
                            @if ($user->work_zone)
                                <span class="department-badge">{{ $user->work_zone->name }}</span>
                            @endif
                        </div>
                        <div class="user-stats">
                            <div class="stat-item">
                                <span class="stat-number">
                                    {{ $user->user_kpis->filter(fn($uk) => $uk->kpi?->type === \App\Models\Kpi::SELF_BY_PERSON)->count() }}
                                </span>
                                <span class="stat-label">KPIs ko'rsatkichalari</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">
                                    {{ $user->user_kpis->filter(fn($uk) => $uk->kpi?->type === \App\Models\Kpi::SELF_BY_PERSON)->sum('target_score') }}
                                </span>
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

        @if ($users->isEmpty())
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
            // Auto-submit form when work zone filter changes
            $('select[name="work_zone_id"], select[name="child_work_zone_id"]').on('change', function() {
                $('#filterForm').submit();
            });

            // Search and filter functionality
            $('#searchUsers').on('input', function() {
                clearTimeout(window.filterTimeout);
                window.filterTimeout = setTimeout(function() {
                    $('#filterForm').submit();
                }, 500); // Debounce
            });
        });
    </script>
@endsection
