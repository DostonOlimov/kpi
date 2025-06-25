@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-calendar mr-1"></i>&nbsp; {{ __('Ish kunlarini qo\'shish') }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ __('Ish kunlarini qo\'shish') }}</h3>
                        <a href="{{ route('roles.index') }}" class="btn btn-primary">
                            {{ __('Ortga') }}
                        </a>
                    </div>

                    <div class="card-body">
                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form action="{{ route('month.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="month_id"><strong>{{ __('Oy nomi') }}:</strong></label>
                                        <select name="month_id" id="month_id" class="form-control">
                                            @foreach($months as $key => $month)
                                                <option value="{{ $key }}">{{ $month }}</option>
                                            @endforeach
                                        </select>
                                        @error('month_id')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="days"><strong>{{ __('Ish kunlari soni') }}:</strong></label>
                                        <input
                                            type="number"
                                            name="days"
                                            id="days"
                                            class="form-control"
                                            placeholder="{{ __('Masalan: 22') }}"
                                            value="{{ old('days') }}"
                                        >
                                        @error('days')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 text-center mt-4">
                                    <button type="submit" class="btn btn-success">
                                        {{ __('Saqlash') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @endsection
