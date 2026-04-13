@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('kpis.user-kpis-dashboard') }}">
                        <i class="fe fe-users mr-1"></i> Foydalanuvchilar KPI Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active">{{ $user->full_name }}</li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="panel panel-primary">
                    <div class="tab_wrapper page-tab">
                        <ul class="tab_list">
                            <li class="active">
                                <a href="#">
                                    <span class="visible-xs"></span>
                                    <i class="fa fa-list fa-lg">&nbsp;</i>
                                    {{ $user->full_name }} - KPI lar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="card-body">
                <!-- User Info Card -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
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

                @if(session('message'))
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> {{ session('message') }}
                    </div>
                @endif

                @if($parentKpis->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">KPI kategoriyalari topilmadi</p>
                    </div>
                @else
                    @foreach($parentKpis as $parentKpi)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fa fa-folder"></i> {{ $parentKpi->name }}
                                            @if(isset($groupedKpis[$parentKpi->id]))
                                                <span class="badge badge-light">{{ count($groupedKpis[$parentKpi->id]) }} KPI</span>
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($groupedKpis[$parentKpi->id]) && count($groupedKpis[$parentKpi->id]) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered text-nowrap">
                                                    <thead class="thead-light">
                                                        <tr class="text-center">
                                                            <th>#</th>
                                                            <th>KPI Nomi</th>
                                                            <th>Holat</th>
                                                            <th>Joriy Ball</th>
                                                            <th>Maqsad Ball</th>
                                                            <th>Natija</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach($groupedKpis[$parentKpi->id] as $index => $userKpi)
                                                            <tr class="text-center align-middle">
                                                                <td>{{ $index + 1 }}</td>
                                                                <td class="text-left">
                                                                    <strong>{{ $userKpi->kpi->name }}</strong>
                                                                </td>
                                                                <td>
                                                                    @if($userKpi->status == 'new')
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
                                                                    @if($userKpi->score)
                                                                        <span class="badge {{ $userKpi->score->color ?? 'badge-secondary' }}">
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
                                                        $parentCurrentScore = collect($groupedKpis[$parentKpi->id])->sum('current_score');
                                                        $parentTargetScore = collect($groupedKpis[$parentKpi->id])->sum('target_score');
                                                    @endphp
                                                    @if($parentTargetScore > 0)
                                                        <tfoot>
                                                            <tr class="bg-light">
                                                                <td colspan="3" class="text-right"><strong>Kategoriya natijasi:</strong></td>
                                                                <td colspan="3" class="text-center">
                                                                    <strong class="text-primary">
                                                                        {{ $parentCurrentScore }} / {{ $parentTargetScore }}
                                                                    </strong>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        {{ number_format(($parentCurrentScore / $parentTargetScore) * 100, 1) }}%
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info text-center">
                                                <i class="fa fa-info-circle"></i> Bu kategoriya uchun KPI lar topilmadi
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($userKpis->isNotEmpty())
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <h5><strong>Umumiy natija:</strong></h5>
                                                <h3 class="text-primary">
                                                    {{ $userKpis->sum('current_score') }} / {{ $userKpis->sum('target_score') }}
                                                </h3>
                                                @if($userKpis->sum('target_score') > 0)
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
                        <form action="{{ route('kpis.user-kpis-refresh', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('KPI larni yangilashni xohlaysizmi?')">
                                <i class="fa fa-refresh"></i> KPI larni yangilash
                            </button>
                        </form>
                        <a href="{{ route('kpis.user-kpis-dashboard') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Ortga qaytish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
