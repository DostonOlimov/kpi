@extends('layouts.app')

@section('title', $department->name . ' - Department Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fa fa-users mr-1"></i>&nbsp;{{ $department->name }}ning samaradorlik ko'rsatkichlari
                    </li>
                </ol>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ url()->previous() }}">Samaradorlik ko'rsatkichlari</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $department->name }}</li>
                        </ol>
                    </nav>
                
                </div>
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                        <i class="fa fa-arrow-left me-2"></i>Orqaga qaytish
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fa fa-print me-2"></i>Chop etish
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Barcha xodimlar
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $employees->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        O'rtacha ko'rsatkich
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ round($employees->avg(function($employee) { 
                            return $employee->user_kpis->avg('avg_score'); 
                        }), 2) }}%
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Yuqori ko'rsatkichlar
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $employees->filter(function($employee) { 
                            return $employee->user_kpis->avg('avg_score') >= 80; 
                        })->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                       Past ko'rsatkichlar
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $employees->filter(function($employee) { 
                            return $employee->user_kpis->avg('avg_score') < 60; 
                        })->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Details -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Xodimlarning samaradorlik ko'rsatkichlari</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Xodimning ismi-sharifi</th>
                                    <th>Xodimning lavozimi</th>
                                    <th>Natijasi</th>
                                    <th>Samaradorlik ko'rsatkichi</th>
                                    <th>Jarayon</th>
                                     <th>Harakat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                @php
                                    $avgScore = $employee->user_kpis->avg('avg_score') ?? 0;
                                @endphp
                                <tr style="height: 60px;">
                                    <td  style="vertical-align: middle;">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-3">
                                                @if($employee->photo)
                                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="User Photo" class="rounded-circle" width="50" height="50">
                                                @else
                                                    <img src="{{ asset('img/employee/avtar.png') }}" alt="No Photo" class="rounded-circle" width="50" height="50">
                                                @endif
                                            </div>
                                            <strong>{{ $employee->first_name .' ' .$employee->last_name }}</strong>
                                        </div>
                                    </td>
                                    <td  style="vertical-align: middle;">{{ $employee->lavozimi }}</td>
                                    <td  style="vertical-align: middle;">
                                        <span class="font-weight-bold">{{ round($avgScore, 2) }}%</span>
                                    </td>
                                    <td  style="vertical-align: middle;">
                                        @if($avgScore >= 80)
                                            <span class="badge badge-success">Yuqori</span>
                                        @elseif($avgScore >= 60)
                                            <span class="badge badge-warning">Yaxshi</span>
                                        @else
                                            <span class="badge badge-danger">Past</span>
                                        @endif
                                    </td>
                                    <td  style="vertical-align: middle;">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar 
                                                @if($avgScore >= 80) bg-success
                                                @elseif($avgScore >= 60) bg-warning
                                                @else bg-danger
                                                @endif" 
                                                style="width: {{ $avgScore }}%">
                                                {{ round($avgScore, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                     <td>
                                        <a href="{{ route('department.user.detail', $employee->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-eye"></i> Tafsilotlarni ko'rish
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Xodimlar topilmadi!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.progress {
    height: 0.5rem;
}
.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
@media print {
    .btn {
        display: none !important;
    }
}
</style>
@endsection
