@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container mt-2">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Tahrirlash</h2>
                    </div>
                </div>
            </div>

            @if(session('status'))
                <div class="alert alert-success mb-1 mt-1">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('days.update', $days->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="form-group">
                            <strong>Ishga chiqilgan kun:</strong>
                            <input type="number" name="days" class="form-control" value="{{ $days->days }}" min="0">
                            @error('days')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Saqlash</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
