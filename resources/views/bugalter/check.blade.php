@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Kiritilgan summalar holati</span></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4>Bugalteriyadan kiritilgan summalar jadvali</h4>
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> №</th>
                                <th> Summa</th>
                                <th> Hisobot oyi</th>
                                <th> Holati</th>
                                <th> Harakat</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ number_format($item->summa, 0, ',', '.') }}</td>
                                    <td>{{ $month[$item->month-1] }}</td>
                                    <td>{{ ($item->status == 'active') ? 'Taqsimlangan' : 'Taqsimlanmagan'}}</td>
                                    <td>
                                        @if($item->status != 'active')
                                            <a class="btn btn-primary" href="{{ route('bugalter.edit', [$item->id]) }}">O'zgartirish</a>
                                            <a class="btn btn-info"
                                               href="{{ route('bugalter.distribution', [$item->id]) }}">Taqsimlashga
                                                berish</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

