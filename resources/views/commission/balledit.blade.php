@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container mt-2">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left mb-2">
                        <h2>O'zgartirish</h2>
                    </div>

                </div>
            </div>
            @if(session('status'))
                <div class="alert alert-success mb-1 mt-1">
                    {{ session('status') }}
                </div>
            @endif
            <form action="{{ route('commission.store2') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <input type="hidden" name="id" value= {{ $user->id }} >
                            <input type="hidden" name="month" value={{ $user->month}}>
                            <input type="hidden" name="year" value={{ $user->year }}>
                            <td> <input type="number" step="0.01"  name="current_ball" class="form-control" value= {{  $user->current_ball }}></td>
                            @error('current_ball')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary ml-3">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
@endsection
