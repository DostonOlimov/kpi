@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Xodimlar ro'yxati</span></h4>
                </div>
            </div>
        </div>
        <div class="row">
     <!-- search component start-->
            <x-search url="bugalter.list"/>
    <!-- search component end-->

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h4>Xodimlar oladigan qo'shimcha ish haqlari jadvali</h4>
                            <h4>Joriy oydagi jami ish kuni - <span>{{$work_day}} kun</span></h4>
                        </div>
                        <div>

                            <a class="btn btn-primary" href="{{ route('bugalter.export',$month_id) }}">Export Excel fayl</a>

                        </div>
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> №</th>
                                <th> Ism-Sharifi</th>
                                <th> Ish kunlari soni</th>
                                <th> To'plangan ball</th>
                                <th> Ustama foizi</th>
                                <th> Xodimning lavozim maoshi</th>
                                <th> Ustama</th>
                                <th> Ijtimoiy soliq 25%</th>
                                <th> Jami</th>
                                <th> Qoldiqqa nisbatan</th>
                                <th>%</th>
                                <th>Qoldiqqa nisbatan ustama</th>
                                <th>Qoldiqqa nisbatan soliq</th>
                                <th>Qoldiqqa nisbatan jami</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->users->last_name.' '.$item->users->first_name  }}</td>
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
                                    <td><a class="btn btn-success" href="{{ route('bugalter.calculate', ['id' => $item->id,]) }}">Hisoblash</a></td>
                                </tr>
                            @endforeach
                            <tr style="font-weight: bold;">
                                <td colspan="5" class="text-center">Jami: </td>
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
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>


    </div>

@endsection

