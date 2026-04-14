@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-users mr-1"></i> Foydalanuvchilar KPI Dashboard
                </li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header">

                <!-- Filters: period + work zones (GET links avoid broken JSON fetch; child form preserves parent) -->
                <div class="user-kpi-dashboard-filters border-bottom bg-white px-3 py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted small text-uppercase mb-0">Hisobot davri</span>
                            <span class="badge badge-info font-weight-normal">{{ $monthLabel }} {{ $year }}</span>
                            <span class="text-muted small">·</span>
                            <span class="text-muted small">{{ $users->count() }} ta xodim</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <label for="userKpiParentZone" class="sr-only">Asosiy hudud (ro'yxat)</label>
                            <select id="userKpiParentZone"
                                    class="form-control form-control-sm d-md-none w-100"
                                    style="max-width: 100%;"
                                    aria-label="Asosiy hududni ro'yxatdan tanlash"
                                    onchange="window.location.href = {{ json_encode(route('kpis.user-kpis-dashboard')) }} + '?work_zone_id=' + encodeURIComponent(this.value);">
                                @foreach($parentWorkZones as $parentZone)
                                    <option value="{{ $parentZone->id }}" {{ (int) $workZoneId === (int) $parentZone->id ? 'selected' : '' }}>
                                        {{ $parentZone->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if((int) $workZoneId !== (int) $defaultParentWorkZoneId || filled($childWorkZoneId))
                                <a href="{{ route('kpis.user-kpis-dashboard', ['work_zone_id' => $defaultParentWorkZoneId]) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-times"></i> Filtrlarni tozalash
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 d-none d-md-block">
                        <span class="d-block small text-muted mb-1">
                            <i class="fa fa-map-marker text-primary"></i> Asosiy hudud
                        </span>
                        <div class="user-kpi-zone-pills">
                            @foreach($parentWorkZones as $parentZone)
                                @php $isActive = (int) $workZoneId === (int) $parentZone->id; @endphp
                                <a href="{{ route('kpis.user-kpis-dashboard', ['work_zone_id' => $parentZone->id]) }}"
                                   class="btn btn-sm {{ $isActive ? 'btn-primary' : 'btn-outline-primary' }} mb-1"
                                   title="{{ $parentZone->name }}"
                                   @if($isActive) aria-current="true" @endif>
                                    <span class="text-truncate d-inline-block" style="max-width: 14rem;">{{ $parentZone->name }}</span>
                                    <span class="badge badge-light text-dark ml-1">{{ $parentZone->employees_count ?? 0 }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <form action="{{ route('kpis.user-kpis-dashboard') }}" method="GET" id="userKpiChildFilterForm" class="mb-0">
                        <input type="hidden" name="work_zone_id" value="{{ $workZoneId }}">
                        <div class="form-group mb-0">
                            <label for="childWorkZone" class="small text-muted mb-1 d-block">
                                <i class="fa fa-sitemap text-info"></i> Bo'lim (ixtiyoriy)
                            </label>
                            <select name="child_work_zone_id"
                                    id="childWorkZone"
                                    class="form-control form-control-sm"
                                    style="max-width: 28rem;"
                                    @if($childWorkZones->isEmpty()) disabled @endif
                                    onchange="this.form.submit()">
                                <option value="">
                                    {{ $childWorkZones->isEmpty() ? 'Bu hududda bo\'lim yo\'q' : 'Barcha bo\'limlar' }}
                                </option>
                                @foreach($childWorkZones as $childZone)
                                    <option value="{{ $childZone->id }}" {{ (int) $childWorkZoneId === (int) $childZone->id ? 'selected' : '' }}>
                                        {{ $childZone->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> {{ session('message') }}
                    </div>
                @endif

                @if($users->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Ushbu oy uchun foydalanuvchilar topilmadi</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-nowrap">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>F.I.O</th>
                                    <th>Lavozimi</th>
                                    <th>Ish hududi</th>
                                    <th>KPI lar Soni</th>
                                    <th>Maqsad Ball</th>
                                    <th>Joriy Ball</th>
                                    <th>Natija</th>
                                    <th>Harakatlar</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr class="text-center align-middle">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-left">
                                            <strong>{{ $user->full_name }}</strong>
                                        </td>
                                        <td>{{ $user->lavozimi ?? '-' }}</td>
                                        <td>{{ $user->work_zone->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $user->total_kpis }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">{{ $user->total_target_score ?? 0 }}</strong>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ $user->total_current_score ?? 0 }}</strong>
                                        </td>
                                        <td>
                                            @if($user->total_target_score > 0)
                                                @php
                                                    $percentage = ($user->total_current_score / $user->total_target_score) * 100;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $percentage >= 80 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min($percentage, 100) }}%;" 
                                                         aria-valuenow="{{ $percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($percentage, 1) }}%
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">0%</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('kpis.user-kpis-detail', $user->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Batafsil ko'rish">
                                                <i class="fa fa-eye"></i> Ko'rish
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fe fe-users fa-2x mb-2"></i>
                                            <p class="mb-0">Foydalanuvchilar topilmadi</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                            @if($users->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="5" class="text-right"><strong>Umumiy natija:</strong></td>
                                        <td colspan="4" class="text-center">
                                            <strong class="text-primary">
                                                {{ $users->sum('total_current_score') }} / {{ $users->sum('total_target_score') }}
                                            </strong>
                                            <br>
                                            @if($users->sum('total_target_score') > 0)
                                                <small class="text-muted">
                                                    {{ number_format(($users->sum('total_current_score') / $users->sum('total_target_score')) * 100, 1) }}%
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .user-kpi-dashboard-filters .user-kpi-zone-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        align-items: center;
    }
    .user-kpi-dashboard-filters .user-kpi-zone-pills .btn {
        text-align: left;
    }
</style>
@endsection
