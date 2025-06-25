
@extends('layouts.app')

@section('content')
<style>
    table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #ddd;
    }

    th, td {
        text-align: center;
        padding: 16px;
    }

    th:first-child, td:first-child {
        text-align: left;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2
    }
</style>

<div class="content-wrapper">
    <div class="row page-title-header">
        <div class="col-12">
            <div class="page-header">
                <h4 class="page-title">
                    <span class="text-primary">Xodimning baholar ro'yxati ({{ $date['month_name'] }} oyi uchun)</span>
                </h4>
            </div>
        </div>
    </div>

    <x-search url="commission.list"/>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4>Xodimlar baholari jadvali</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="text-center">
                            <tr>
                                <th>№</th>
                                <th>Ismi</th>
                                <th>Familiyasi</th>
                                <th>Faoliyat yo'nalishi</th>
                                <th>O'zining bali</th>
                                <th>Jarima bali</th>
                                <th>Yakuniy ball</th>
                                <th>Samaradorligi</th>
                                <th>Harakat</th>
                                <th>Yakuniy ball kiritish</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->first_name }}</td>
                                    <td>{{ $item->last_name }}</td>
                                    <td>{{ $item->work_zone->long_name ?? '' }}</td>

                                    @php
                                        $total = optional($item->totalBalls->first());
                                    @endphp

                                    @if ($total)
                                        <td>{{ $total->personal_ball }}</td>
                                        <td>{{ $total->fine_ball }}</td>
                                        <td>{{ $total->current_ball }}</td>
                                        <td>{{ $total->current_ball }}%</td>
                                        <td>
                                            <a class="btn btn-warning btn-sm" href="{{ route('commission.edit', ['id' => $item->id, 'month_id' => $date['month'], 'year' => $date['year']]) }}">
                                                Tekshirish
                                            </a>
                                        </td>
                                        <td>
{{--                                            <a class="btn btn-success btn-sm" href="{{ route('commission.ball.edit', 'id' => $total->id }}">--}}
{{--                                                O'zgartirish--}}
{{--                                            </a>--}}
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <a class="btn btn-warning btn-sm" href="{{ route('commission.edit', ['id' => $item->id, 'month_id' => $date['month'], 'year' => $date['year']]) }}">
                                                Tekshirish
                                            </a>
                                        </td>
                                        <td>
                                            <form action="{{ route('commission.ball') }}" method="POST" class="d-flex" style="gap: 5px;">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $item->id }}">
                                                <input type="hidden" name="month" value="{{ $date['month'] }}">
                                                <input type="hidden" name="year" value="{{ $date['year'] }}">
                                                <input type="number" name="current_ball" class="form-control" placeholder="Ball" style="width: 100px;">
                                                <button class="btn btn-success btn-sm" type="submit">Saqlash</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    @error('current_ball')
                    <div class="alert alert-danger mt-3">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <canvas id="myChart"></canvas>
    </div>
</div>

@endsection
