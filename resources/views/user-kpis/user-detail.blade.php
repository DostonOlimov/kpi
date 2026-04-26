@extends('layouts.app')

<style>
    .ud-score-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        color: #fff;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
        text-decoration: none;
        white-space: nowrap;
        transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
    }
    .ud-score-btn i {
        font-size: 0.82rem;
    }
    .ud-score-btn:hover {
        color: #fff;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.5);
        transform: translateY(-1px);
        text-decoration: none;
    }
    .ud-score-btn:active {
        transform: translateY(0);
        box-shadow: 0 1px 4px rgba(16, 185, 129, 0.3);
    }
</style>

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" style="color:white !important;">
                    <a href="{{ route('kpis.user-kpis-dashboard') }}">
                        <i class="fe fe-users mr-1"></i> Foydalanuvchilar KPI Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active">{{ $user->full_name }}</li>
            </ol>
        </div>

        <div class="card-body">
            <!-- User Info Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>F.I.O:</strong> {{ $user->full_name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Lavozimi:</strong> {{ $user->lavozimi ?? '-' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Ish hududi:</strong> {{ $user->work_zone->name ?? '-' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Role:</strong> {{ $user->role->name ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('message'))
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> {{ session('message') }}
                </div>
            @endif

            @if ($parentKpis->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                    <p class="mb-0">KPI kategoriyalari topilmadi</p>
                </div>
            @else
                @foreach ($parentKpis as $parentKpi)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0 text-white">
                                        <i class="fa fa-folder"></i> {{ $parentKpi->name }}
                                        @if (isset($groupedKpis[$parentKpi->id]))
                                            <span class="badge badge-light">{{ count($groupedKpis[$parentKpi->id]) }}
                                                KPI</span>
                                        @endif
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if (isset($groupedKpis[$parentKpi->id]) && count($groupedKpis[$parentKpi->id]) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered" style="table-layout:fixed;width:100%;">
                                                <thead class="thead-light">
                                                    <tr class="text-center">
                                                        <th style="width:44px;">#</th>
                                                        <th>KPI Nomi</th>
                                                        <th style="width:100px;">Holat</th>
                                                        <th style="width:100px;" class="text-nowrap">Joriy Ball</th>
                                                        <th style="width:100px;" class="text-nowrap">Maqsad Ball</th>
                                                        <th style="width:120px;">Natija</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($groupedKpis[$parentKpi->id] as $index => $userKpi)
                                                        <tr class="text-center align-middle">
                                                            <td>{{ $index + 1 }}</td>
                                                            <td class="text-left" style="white-space:normal;word-break:break-word;">
                                                                <span title="{{ $userKpi->kpi->name }}">{{ $userKpi->kpi->name }}</span>
                                                            </td>
                                                            <td>
                                                                @if ($userKpi->status == 'new')
                                                                    <span class="badge badge-info">Yangi</span>
                                                                @elseif($userKpi->status == 'in_progress')
                                                                    <span class="badge badge-warning">Jarayonda</span>
                                                                @elseif($userKpi->status == 'completed')
                                                                    <span class="badge badge-success">Bajarildi</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Noma'lum</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <strong>{{ $userKpi->current_score ?? 0 }}</strong>
                                                            </td>
                                                            <td>
                                                                <strong>{{ $userKpi->target_score ?? 0 }}</strong>
                                                            </td>
                                                            <td>
                                                                @if ($userKpi->score)
                                                                    <span
                                                                        class="badge {{ $userKpi->score->color ?? 'badge-secondary' }}">
                                                                        {{ $userKpi->score->name ?? '-' }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">Baholanmagan</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                                @php
                                                    $parentCurrentScore = collect($groupedKpis[$parentKpi->id])->sum(
                                                        'current_score',
                                                    );
                                                    $parentTargetScore = collect($groupedKpis[$parentKpi->id])->sum(
                                                        'target_score',
                                                    );
                                                @endphp
                                                @if ($parentTargetScore > 0)
                                                    @php
                                                        $categoryPct = round(
                                                            ($parentCurrentScore / $parentTargetScore) * 100,
                                                            1,
                                                        );
                                                    @endphp
                                                    <tfoot>
                                                        <tr class="bg-light">
                                                            <td colspan="2" class="text-center align-middle"
                                                                style="padding-right: 20px !important;
    color: #2d3748;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.85rem; /* Smaller, uppercase text for modern look */
    letter-spacing: 1px;">
                                                                <strong>Xodimning to'plagan natijasi:</strong>
                                                            </td>
                                                            <td colspan="3" class="align-middle"
                                                                style="min-width:180px;">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-grow-1 mr-2">
                                                                        <div class="progress" style="height:10px;">
                                                                            <div class="progress-bar bg-success"
                                                                                role="progressbar"
                                                                                style="width: {{ $categoryPct }}%"
                                                                                aria-valuenow="{{ $categoryPct }}"
                                                                                aria-valuemin="0" aria-valuemax="100">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted">{{ $categoryPct }}%</small>
                                                                </div>
                                                                <small class="text-muted">
                                                                    <strong
                                                                        class="text-success">{{ $parentCurrentScore }}</strong>
                                                                    / {{ $parentTargetScore }}
                                                                </small>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <a href="{{ route('kpis.score-kpi-type', [$user->id, $parentKpi->id]) }}"
                                                                   class="ud-score-btn">
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                    <span>Baholash</span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info text-center">
                                            <i class="fa fa-info-circle"></i> Bu ko'rsatkich uchun KPI lar topilmadi
                                        </div>
                                    @endif
                                </div>
                                @if ($parentKpi->type == \App\Models\Kpi::SELF_BY_PERSON)
                                    @php
                                        $selfKpis = isset($groupedKpis[$parentKpi->id])
                                            ? collect($groupedKpis[$parentKpi->id])
                                            : collect();
                                        $selfCurrent = $selfKpis->sum('target_score');
                                        // SELF_BY_PERSON has fixed max total of 60 (based on kpis.blade.php logic)
                                        $selfTotal = 60;
                                        $selfPercentage = $selfTotal > 0 ? round(($selfCurrent / $selfTotal) * 100) : 0;
                                    @endphp
                                    <div class="card-footer bg-gradient-light">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="mr-3">
                                                        <span class="badge badge-primary px-3 py-2">
                                                            <i class="fa fa-star"></i>
                                                            {{ $selfCurrent }}/{{ $selfTotal }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="progress" style="height: 12px;">
                                                            <div class="progress-bar bg-gradient-primary progress-bar-striped progress-bar-animated"
                                                                role="progressbar" style="width: {{ $selfPercentage }}%"
                                                                aria-valuenow="{{ $selfPercentage }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                                <span
                                                                    class="font-weight-bold text-white">{{ $selfPercentage }}%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <span class="text-muted font-size-sm">
                                                            <i class="fa fa-check-circle text-success"></i>
                                                            {{ $selfKpis->filter(fn($k) => $k->current_score > 0)->count() }}
                                                            ta bajarildi
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <a href="{{ route('employee.kpis', $user->id) }}"
                                                    class="btn btn-gradient-primary btn-lg">
                                                    <i class="fa fa-plus-circle"></i> Xodimga KPI biriktirish
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <style>
                                        .bg-gradient-light {
                                            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                                        }

                                        .bg-gradient-primary {
                                            background: linear-gradient(90deg, #0c238a 0%, #764ba2 100%) !important;
                                        }

                                        .btn-gradient-primary {
                                            background: linear-gradient(135deg, #0c238a 0%, #764ba2 100%);
                                            color: white;
                                            border: none;
                                            transition: all 0.3s ease;
                                        }

                                        .btn-gradient-primary:hover {
                                            transform: translateY(-2px);
                                            box-shadow: 0 4px 12px rgba(12, 35, 138, 0.3);
                                            color: white;
                                        }
                                    </style>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($userKpis->isNotEmpty())
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <h5><strong>Umumiy natija:</strong></h5>
                                            <h3 class="text-success">
                                                {{ $userKpis->sum('current_score') }} /
                                                {{ $userKpis->sum('target_score') }}
                                            </h3>
                                            @if ($userKpis->sum('target_score') > 0)
                                                <h4 class="text-muted">
                                                    {{ number_format(($userKpis->sum('current_score') / $userKpis->sum('target_score')) * 100, 1) }}%
                                                </h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    {{-- <form action="{{ route('kpis.user-kpis-refresh', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('KPI larni yangilashni xohlaysizmi?')">
                                <i class="fa fa-refresh"></i> KPI larni yangilash
                            </button>
                        </form> --}}
                    <a href="{{ route('kpis.user-kpis-dashboard') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Ortga qaytish
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
