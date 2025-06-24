@extends('layouts.app')

@section('content')
<div class="content-wrapper">
<div class="container mt-2">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Tahrirlash</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('month.index') }}" enctype="multipart/form-data">
                        Orqaga</a>
                </div>
            </div>
        </div>
        @if(session('status'))
        <div class="alert alert-success mb-1 mt-1">
            {{ session('status') }}
        </div>
        @endif
        <form action="{{ route('month.update',$month->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Ish kunlarini soni:</strong>
                        <input type="hidden" name="month_id" value="{{ $month->month_id }}" class="form-control">
                        <input type="text" name="days" value="{{ $month->days }}" class="form-control">
                        @error('name')
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