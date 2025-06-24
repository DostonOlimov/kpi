@extends('layouts.app')

<style>
        .forms-sample input{
            font-size: 16px;
            font-weight: bold;
        }
        .forms-sample select{
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@section('content')
<div class="content-wrapper">
        <div class="row ">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body" style="background-color:white">
               <!-- <div class="col-lg-12 grid-margin stretch-card">
                <div class="card"> -->
                    <!-- <div class="card-body"> -->
                    <h2>Xodimlarning ish kunlari</h2>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tartib raqam</th>
                    <th>Xodimning ismi sharifi </th>
                    <th>Ish joyi</th>
                    <th>Lavozimi</th>
                    <th>Oy nomi</th>
                    <th>Oylik ish kuni</th>
                    <th>Ishga chiqilgan kun</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($users as $key => $user)
                    <tr>
                        <td>{{ $key + 1}}</td>
                        <td>{{ $user->first_name }}&nbsp;{{ $user->last_name }}</td>
                        <td>{{ $user->work_zone->name }}</td>
                        <td>{{ $user->lavozimi }}</td>
                        <td>{{ $date['month_name'] }}</td>
                        <td>{{ $date['days'] }} kun</td>
                         @if ($user->employeeDays->first())
                            <td>{{ optional($user->employeeDays->first())->days }} &nbsp; kun
                            <a class="btn btn-primary" href="{{ route('days.edit',$user->employeeDays->first()->id) }}">O'zgartirish</a>
                            </td>
                         @else
                          <td><a class="btn btn-success" href="{{ route('days.createday',[$user->id,$date['month_id'],$date['year']]) }}">Kiritish</a></td>
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
@endsection
