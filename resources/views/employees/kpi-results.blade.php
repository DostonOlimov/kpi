@extends('layouts.app')
@section('styles')
@endsection
@section('content')
    <div class="section">

        <!-- HEADER -->
        <div class="page-header mb-4">
            <h4 class="page-title text-white">
                <i class="fe fe-bar-chart-2 me-2 text-white"></i> Xodimlar KPI Natijalari
            </h4>

            <ol class="breadcrumb">
                <li class="breadcrumb-item active text-white">Boshqaruv</li>
                <li class="breadcrumb-item text-white">KPI Natijalari</li>
            </ol>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="btn-group">
                <a href="{{ route('employees.kpi-results', ['workZone' => $work_zone_id]) }}" class="btn btn-info">
                    <i class="fa fa-bar-chart"></i> KPI Natijalar
                </a>
            </div>

            <!-- Refresh/Calculate Button -->
            <form action="{{ route('employees.calculate-results', ['workZone' => $work_zone_id]) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Barcha xodimlar uchun KPI natijalarini hisoblashni xohlaysizmi?')">
                    <i class="fa fa-refresh"></i> Natijalarni hisoblash
                </button>
            </form>
        </div>

        <!-- KPI RESULTS TABLE -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fa fa-bar-chart me-2"></i> 
                    KPI Natijalari - {{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}
                </h5>
            </div>

            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-2"></i> {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Ism Familiya</th>
                                <th>Bo'lim</th>
                                <th>Lavozim</th>
                                <th>Jami Ball</th>
                                <th>Yakuniy Ball</th>
                                <th>Baho</th>
                                <th>Holat</th>
                                <th>Izoh</th>
                                <th width="100px">Harakatlar</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($results as $index => $result)
                                <tr>
                                    <td>{{ $results->firstItem() + $index }}</td>

                                    <td class="text-start">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $result->user->photo ? asset('storage/' . $result->user->photo) : asset('img/employee/avtar.png') }}"
                                                class="rounded-circle shadow-sm me-2" width="40" height="40" alt="User Photo">
                                            <strong>{{ $result->user->first_name }} {{ $result->user->last_name }}</strong>
                                        </div>
                                    </td>

                                    <td>{{ $result->user->work_zone->name ?? '-' }}</td>
                                    <td>{{ $result->user->lavozimi ?? '-' }}</td>

                                    <td>
                                        <span class="badge bg-primary fs-6">{{ number_format($result->total_score, 2) }}</span>
                                    </td>

                                    <td>
                                        <span class="badge bg-success fs-6">{{ number_format($result->final_score, 2) }}</span>
                                    </td>

                                    <td>
                                        @php
                                            $gradeColors = [
                                                'A' => 'bg-success',
                                                'B' => 'bg-info',
                                                'C' => 'bg-warning',
                                                'D' => 'bg-orange',
                                                'F' => 'bg-danger'
                                            ];
                                        @endphp
                                        <span class="badge {{ $gradeColors[$result->grade] ?? 'bg-secondary' }} fs-5">
                                            {{ $result->grade ?? '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-secondary',
                                                'calculated' => 'bg-info',
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger'
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$result->status] ?? 'bg-secondary' }}">
                                            {{ $result->status_name }}
                                        </span>
                                    </td>

                                    <td class="text-start">
                                        {{ Str::limit($result->comments, 30) ?? '-' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('employees.edit', $result->user->id) }}"
                                            class="btn btn-sm btn-success me-1" title="Tahrirlash">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <a href="{{ route('employees.edit-password', $result->user->id) }}"
                                            class="btn btn-sm btn-warning" title="Parolni o'zgartirish">
                                            <i class="fa fa-key"></i>
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="fa fa-info-circle text-muted" style="font-size: 48px;"></i>
                                        <p class="mt-3 text-muted">
                                            Hozircha natijalar yo'q. "Natijalarni hisoblash" tugmasini bosing.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if($results->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Jami:</strong></td>
                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            {{ number_format($results->sum('total_score'), 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success fs-6">
                                            {{ number_format($results->sum('final_score'), 2) }}
                                        </span>
                                    </td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <!-- PAGINATION -->
                @if($results->hasPages())
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $results->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@section('scripts')
@endsection
