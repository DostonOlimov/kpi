@extends('layouts.app')

@section('content')
<div class="section">
    <!-- PAGE HEADER -->
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-clock mr-1"></i> Turniket davomati — batafsil
            </li>
        </ol>
    </div>

    <!-- Filters: Work Zone + Date -->
    <div class="row mb-4">
        <div class="col-12">
            <x-work-zone-filter
                :actionUrl="route('turniket-events.timeline')"
                :showLabel="true"
                :autoSubmit="true"
            />
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #3c4b64 0%, #4f6080 100%); border-bottom: none; padding: 1rem 1.5rem;">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                <h5 class="text-white mb-0" style="font-weight: 600;">
                    <i class="fe fe-list mr-1"></i> Xodimlar kirish-chiqish jadvali
                    <small class="ml-2" style="opacity: 0.8;">({{ $dateLabel }})</small>
                </h5>

                {{-- Date range filter --}}
                <form action="{{ route('turniket-events.timeline') }}" method="GET" class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    @php
                        $wz = request('work_zone_id');
                        $cwz = request('child_work_zone_id');
                    @endphp
                    @if($wz)
                        <input type="hidden" name="work_zone_id" value="{{ $wz }}">
                    @endif
                    @if($cwz)
                        <input type="hidden" name="child_work_zone_id" value="{{ $cwz }}">
                    @endif

                    <input type="date" name="date" value="{{ $day ?? '' }}" class="form-control form-control-sm"
                           style="max-width: 145px;" onchange="this.form.submit()">

                    @if($day)
                        <a href="{{ route('turniket-events.timeline', array_filter(['work_zone_id' => $wz, 'child_work_zone_id' => $cwz])) }}"
                           class="btn btn-sm btn-outline-light">
                            <i class="fa fa-times mr-1"></i>Bugun
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body">
            @forelse ($employees as $emp)
                <div class="mb-4" style="border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden;">
                    {{-- Employee header --}}
                    <div class="d-flex justify-content-between align-items-center px-3 py-2"
                         style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-primary mr-2" style="font-size: 0.9rem;">
                                ID: {{ $emp['external_id'] }}
                            </span>
                            <strong style="font-size: 1.05rem;">{{ $emp['name'] }}</strong>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="text-muted mr-2">Tashriflar:</span>
                            <span class="badge badge-secondary mr-3">{{ count($emp['visits']) }}</span>
                            <span class="text-muted mr-2">Jami vaqt:</span>
                            <span class="badge badge-success" style="font-size: 1rem;">{{ $emp['total_time'] }}</span>
                        </div>
                    </div>

                    {{-- Visits table --}}
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th style="width: 60px;">#</th>
                                    <th>Kirish</th>
                                    <th>Chiqish</th>
                                    <th>Davomiyligi</th>
                                    <th>Holat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emp['visits'] as $i => $visit)
                                    <tr class="text-center align-middle">
                                        <td>{{ $i + 1 }}</td>

                                        {{-- IN time --}}
                                        <td>
                                            @if($visit['in'])
                                                <span class="text-success font-weight-bold">
                                                    <i class="fe fe-log-in mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($visit['in']->event_time)->format('H:i:s') }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- OUT time --}}
                                        <td>
                                            @if($visit['out'])
                                                <span class="text-danger font-weight-bold">
                                                    <i class="fe fe-log-out mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($visit['out']->event_time)->format('H:i:s') }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Duration --}}
                                        <td>
                                            @if($visit['seconds'] !== null)
                                                @if($visit['status'] === 'open')
                                                    <span class="badge badge-warning" style="font-size: 0.95rem;">
                                                        {{ sprintf('%d:%02d', floor($visit['seconds']/3600), floor(($visit['seconds']%3600)/60)) }}
                                                        <small><i class="fe fe-activity ml-1"></i></small>
                                                    </span>
                                                @else
                                                    <span class="badge badge-info" style="font-size: 0.95rem;">
                                                        {{ sprintf('%d:%02d', floor($visit['seconds']/3600), floor(($visit['seconds']%3600)/60)) }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            @if($visit['status'] === 'closed')
                                                <span class="badge badge-success">Yopiq</span>
                                            @elseif($visit['status'] === 'open')
                                                <span class="badge badge-warning">Hali ichkarida</span>
                                            @elseif($visit['status'] === 'unmatched_exit')
                                                <span class="badge badge-secondary">Bog'lanmagan chiqish</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="fe fe-alert-circle fa-3x mb-3"></i>
                    <p class="mb-0" style="font-size: 1.1rem;">Ushbu sanada turniket ma'lumotlari topilmadi</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if(isset($pager) && $pager->hasPages())
                <div class="mt-4">
                    {{ $pager->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
