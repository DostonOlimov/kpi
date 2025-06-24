
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
       <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Xodimning baholar ro'yxati ( {{ $date['month_name']  }} oyi uchun)</span></h4>
                </div>
            </div>
        </div>
        <!-- search month component start -->
        <x-search url="commission.list"/>
        <!-- search month component end -->
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4>Xodimlar baholari jadvali</h4>
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> №</th>
                                <th> Ismi</th>
                                <th> Familiyasi</th>

                                <th> Faoliyat yo'nalishi</th>
                                <th> O'zining bali</th>
                                <th> Jarima bali</th>
                                <th> Yakuniy bal</th>
                                <th> Samaradorligi</th>
                                <th> Harakat</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->first_name }}</td>
                                    <td>{{ $item->last_name }}</td>
                                    <td>{{ $item->work_zone->long_name ?? '' }}</td>
                                    @if ($item->totalBalls->first())
                                    <td>{{ optional($item->totalBalls->first())->personal_ball }}</td>
                                    <td>{{ optional($item->totalBalls->first())->fine_ball }}</td>
                                    <td>{{ optional($item->totalBalls->first())->current_ball }}</td>
                                    <td>{{ optional($item->totalBalls->first())->current_ball }}%</td>

                                    <td>
                                    <a class="btn btn-warning" href="{{ route('commission.edit', ['id' => $item->id,'month_id' => $date['month'],'year' => $date['year']]) }}">Tekshirish</a>
                                    </td>
                                    @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @endif
                                    @if($total = optional($item->totalBalls->first())->current_ball)
                                    <td>{{ $total }}</td>
                                    <td><a class="btn btn-warning" href="{{ route('commission.ball.edit', ['id' => $item->totalBalls->first()->id,]) }}">O'zgartirish</a></td>
                                    @else
                                   <form style="width:auto;height:auto;color:black;margin:0;" action="{{ route('commission.ball') }}" method="POST" enctype="multipart/form-data">
                                 @csrf
                                       <td >
                                 <input type="hidden" name="user_id" value={{ $item->id }}>
                                 <input type="hidden" name="month" value={{ $date['month'] }}>
                                <input type="hidden" name="year" value={{ $date['year'] }}>
                                <input type="number" name="current_ball" class="form-control" placeholder="Yakuniy ball">
                                 @error('name')
                                @enderror
                                </td>
                                <td>
                                <button class="btn btn-success" type="submit">Saqlash</button>
                                </td>
                                </form>
                                @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @error('current_ball')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="container">
  <canvas id="myChart"></canvas>
</div>

@endsection
