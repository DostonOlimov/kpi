@extends('layouts.app')

@section('content')
<div class="section">
    <!-- PAGE HEADER -->
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-users mr-1"></i> Turniket davomati
            </li>
        </ol>
    </div>

    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #3c4b64 0%, #4f6080 100%); border-bottom: none; padding: 1rem 1.5rem;">
            @if(session('message'))
                <div class="alert alert-success py-2 mb-3">
                    <i class="fa fa-check-circle mr-1"></i>{{ session('message') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                <h5 class="text-white mb-0" style="font-weight: 600;">
                    <i class="fe fe-clock mr-1"></i> Kelish — Ketish hisoboti
                </h5>

                {{-- Date filter --}}
                <form action="{{ route('turniket-events.report') }}" method="GET" class="d-flex align-items-center" style="gap: 8px;">
                    <input type="date" name="date" value="{{ $day ?? '' }}" class="form-control form-control-sm"
                           style="max-width: 150px;" onchange="this.form.submit()">

                    @if($eid)
                        <input type="hidden" name="external_id" value="{{ $eid }}">
                    @endif

                    @if($day)
                        <a href="{{ route('turniket-events.report', array_filter(['external_id' => $eid])) }}" class="btn btn-sm btn-outline-light">
                            <i class="fa fa-times mr-1"></i>Tozalash
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Sana</th>
                            <th>ID</th>
                            <th>Ism</th>
                            <th>Kelish vaqti</th>
                            <th>Ketish vaqti</th>
                            <th>Ish vaqti</th>
                            <th>Kech qolish</th>
                            <th>Erta ketish</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $index => $r)
                            <tr class="text-center align-middle">
                                <td>{{ $rows->firstItem() + $index }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->event_date)->format('d.m.Y') }}</td>
                                <td>{{ $r->external_id }}</td>
                                <td>{{ $r->any_name ?: '-' }}</td>

                                {{-- First In --}}
                                <td>
                                    @if($r->_first_in_clock)
                                        @if($r->_is_late)
                                            <span class="text-danger font-weight-bold">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ $r->_first_in_clock }}
                                            </span>
                                        @else
                                            {{ $r->_first_in_clock }}
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Last Out --}}
                                <td>
                                    @if($r->_last_out_clock)
                                        @if($r->_is_early)
                                            <span class="text-danger font-weight-bold">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ $r->_last_out_clock }}
                                            </span>
                                        @else
                                            {{ $r->_last_out_clock }}
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Time Spent --}}
                                <td>
                                    @if($r->_spent !== '-')
                                        <span class="badge badge-info" style="font-size: 0.95rem;">
                                            {{ $r->_spent }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Late flag --}}
                                <td>
                                    @if($r->_is_late)
                                        <span class="badge badge-danger">Kech</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Early flag --}}
                                <td>
                                    @if($r->_is_early)
                                        <span class="badge badge-warning">Erta</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fe fe-alert-circle fa-2x mb-2"></i>
                                    <p class="mb-0">Ushbu sanada turniket ma'lumotlari topilmadi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
