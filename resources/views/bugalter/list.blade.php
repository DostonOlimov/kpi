@extends('layouts.app')
<style>
    /* Simple and Clean Table Styles */

.section {
    padding: 20px;
    background-color: #ffffff;
}

.page-header {
    margin-bottom: 20px;
}

/* Card Styling */
.card {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 15px 20px;
}

.card-header h4 {
    color: #212529;
    font-size: 18px;
    font-weight: 600;
}

.card-header h5 {
    color: #6c757d;
    font-size: 14px;
}

.card-body {
    padding: 20px;
}

/* Button Styling */
.btn {
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    margin-right: 10px;
    margin-bottom: 10px;
}

/* Table Styling */
.table {
    font-size: 14px;
}

.table thead th {
    background-color: #e9ecef;
    color: #495057;
    font-weight: 600;
    text-align: center;
    padding: 12px 8px;
    border-bottom: 2px solid #dee2e6;
}

.table tbody td {
    padding: 10px 8px;
    text-align: center;
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Total row */
.table tbody tr.font-weight-bold {
    background-color: #e3f2fd;
    font-weight: 700;
}

.table tbody tr.font-weight-bold td {
    border-top: 2px solid #2196f3;
    padding: 12px 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .section {
        padding: 10px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .table {
        font-size: 12px;
    }
    
    .table thead th,
    .table tbody td {
        padding: 8px 4px;
    }
}
</style>

@section('content')
    <div class="section">
        <!-- Page Header -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-users mr-1"></i>&nbsp; {{ __("Xodimlar ro'yxati") }}
                </li>
            </ol>
        </div>

        <!-- Table Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ __('Xodimlar oladigan qo\'shimcha ish haqlari jadvali') }}</h4>
                        <h5 class="mb-0 text-muted">
                            {{ __('Joriy oydagi jami ish kuni') }} - <span class="text-primary">{{ $work_day }} {{ __('kun') }}</span>
                        </h5>
                    </div>

                    <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <a class="btn btn-success" href="{{ route('bugalter.export', $month_id) }}">
                            <i class="fa fa-file-excel-o me-1"></i> {{ __('Excel faylga yuklab olish') }}
                        </a>
                        <a href="{{ route('bugalter.calculate') }}" class="btn btn-primary">
                            <i class="fa fa-refresh me-1"></i> {{ __('Yangilash') }}
                        </a>
                    </div>


                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Ism-sharifi') }}</th>
                                    <th>{{ __('Ish kunlari') }}</th>
                                    <th>{{ __('Ball') }}</th>
                                    <th>{{ __('Ustama %') }}</th>
                                    <th>{{ __('Maosh') }}</th>
                                    <th>{{ __('Ustama') }}</th>
                                    <th>{{ __('Soliq (25%)') }}</th>
                                    <th>{{ __('Jami') }}</th>
                                    <th>{{ __('Qoldiq') }}</th>
                                    <th>%</th>
                                    <th>{{ __('Yangi ustama') }}</th>
                                    <th>{{ __('Yangi soliq') }}</th>
                                    <th>{{ __('Yangi jami') }}</th>
                                
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->users->last_name . ' ' . $item->users->first_name }}</td>
                                        <td>{{ $item->days }}</td>
                                        <td>{{ $item->current_ball }}</td>
                                        <td>{{ $item->rating * $item->current_ball }}</td>
                                        <td>{{ number_format($item->summa) }}</td>
                                        <td>{{ number_format($item->ustama) }}</td>
                                        <td>{{ number_format($item->ustama * 0.25) }}</td>
                                        <td>{{ number_format($item->total_summa) }}</td>
                                        <td>{{ number_format($item->active_summa) }}</td>
                                        <td>{{ $item->foiz }}</td>
                                        <td>{{ number_format($item->new_ustama) }}</td>
                                        <td>{{ number_format($item->new_ustama * 0.25) }}</td>
                                        <td>{{ number_format($item->new_total) }}</td>
                                      
                                    </tr>
                                @endforeach

                                <tr class="font-weight-bold text-center">
                                    <td colspan="5">{{ __('Jami:') }}</td>
                                    <td>{{ number_format(array_sum($salary)) }}</td>
                                    <td>{{ number_format(array_sum($ustama)) }}</td>
                                    <td>{{ number_format(array_sum($soliq)) }}</td>
                                    <td>{{ number_format(array_sum($jami)) }}</td>
                                    <td>{{ number_format(array_sum($active)) }}</td>
                                    <td></td>
                                    <td>{{ number_format(array_sum($new_ustama)) }}</td>
                                    <td>{{ number_format(array_sum($new_soliq)) }}</td>
                                    <td>{{ number_format(array_sum($new_total)) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

