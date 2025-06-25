@extends('layouts.app')

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

        <!-- Search Component -->
        <div class="row mb-3">
            <div class="col-md-12">
                <x-search url="bugalter.list" />
            </div>
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
                        <a class="btn btn-success mb-3" href="{{ route('bugalter.export', $month_id) }}">
                            <i class="fa fa-file-excel"></i> {{ __('Excel faylga yuklab olish') }}
                        </a>

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
                                    <th>{{ __('Hisoblash') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->users->last_name . ' ' . $item->users->first_name }}</td>
                                        <td>{{ optional($item->users->employeeDays->first())->days }}</td>
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
                                        <td>
                                            <a href="{{ route('bugalter.calculate', ['id' => $item->id]) }}" class="btn btn-sm btn-primary">
                                                {{ __('Hisoblash') }}
                                            </a>
                                        </td>
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
                                    <td></td>
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

