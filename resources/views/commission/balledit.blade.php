@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-edit mr-1"></i>&nbsp; {{ __("O'zgartirish") }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        @if(session('status'))
                            <div class="alert alert-success mb-3">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('commission.store2') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <input type="hidden" name="month" value="{{ $user->month }}">
                            <input type="hidden" name="year" value="{{ $user->year }}">

                            <div class="form-group">
                                <label for="current_ball" class="form-label font-weight-bold">Joriy ball:</label>
                                <input type="number" step="0.01" name="current_ball" class="form-control"
                                       value="{{ $user->current_ball }}">
                                @error('current_ball')
                                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">{{ __('Saqlash') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
