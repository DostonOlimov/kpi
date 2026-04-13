@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-bar-chart-2 mr-1"></i> Mening KPIlarim
                </li>
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
                                    KPI Ro'yxati
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Month/Year Filter -->
                <div class="filters-wrapper bg-light p-3 rounded">
                    <form action="{{ route('kpis.user-kpis') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="month" class="form-control">
                                    @foreach($months as $monthNum => $monthName)
                                        <option value="{{ $monthNum }}" {{ $month == $monthNum ? 'selected' : '' }}>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="year" class="form-control">
                                    @foreach($years as $yearOption)
                                        <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>
                                            {{ $yearOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Ko'rish
                                </button>
                                <a href="{{ route('kpis.user-kpis') }}" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i>
                                </a>
                            </div>
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

                @if($userKpis->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Ushbu oy uchun KPI lar topilmadi</p>
                    </div>
                @else
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
                                @forelse ($userKpis as $index => $userKpi)
                                    <tr class="text-center align-middle">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-left">
                                            <strong>{{ $userKpi->kpi->name ?? '-' }}</strong>
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
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fe fe-bar-chart-2 fa-2x mb-2"></i>
                                            <p class="mb-0">KPI lar topilmadi</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                            @if($userKpis->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right"><strong>Umumiy natija:</strong></td>
                                        <td colspan="3" class="text-center">
                                            <strong class="text-primary">
                                                {{ $userKpis->sum('current_score') }} / {{ $userKpis->sum('target_score') }}
                                            </strong>
                                            <br>
                                            @if($userKpis->sum('target_score') > 0)
                                                <small class="text-muted">
                                                    {{ number_format(($userKpis->sum('current_score') / $userKpis->sum('target_score')) * 100, 1) }}%
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
