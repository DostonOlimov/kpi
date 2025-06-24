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
                    <h2>Oy ish kunlari</h2>
               
                <div class="pull-right mb-2">
                    <a class="btn btn-success" href="{{ route('month.create') }}"> Yangi yaratish</a>
                </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tartib raqam</th>
                    <th>Oy nomi</th>
                    <th>Ish kuni</th>
                    <th>Yaratilgan vaqti</th>
                    <th>O'zgartirilgan vaqti</th>
                    <th width="280px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ App\Models\Month::getMonth($role->month_id) }}</td>
                        <td>{{ $role->days }}</td>
                        <td>{{ $role->created_at }}</td>
                        <td>{{ $role->updated_at }}</td>
                        <td>
                            <form action="{{ route('month.destroy',$role->id) }}" method="Post">
                                <a class="btn btn-primary" href="{{ route('month.edit',$role->id) }}">Tahrirlash</a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">O'chirish</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
        {!! $roles->links() !!}
        </div>
    </div>
</div>
</div>
@endsection