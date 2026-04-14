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

                <!-- Work Zone Filter Component -->
                <x-work-zone-filter :actionUrl="route('kpis.user-kpis-dashboard')" :showLabel="true" :autoSubmit="true" />

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
                                    {{-- <th>Ish hududi</th> --}}
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
                                        {{-- <td>{{ $user->work_zone->name ?? '-' }}</td> --}}
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
