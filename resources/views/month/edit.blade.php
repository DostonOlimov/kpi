@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- Page Header -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp; {{ __('Ish kunlarini tahrirlash') }}
                </li>
            </ol>
        </div>

        <!-- Page Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list mb-0">
                                <li>
                                    <a href="{{ route('month.index') }}">
                                        <i class="fa fa-list fa-lg"></i>&nbsp; {{ __("Ro'yxat") }}
                                    </a>
                                </li>
                                <li class="active">
                                    <i class="fa fa-pencil fa-lg"></i>&nbsp; <strong>{{ __("Tahrirlash") }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form action="{{ route('month.update', $month->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="month_id" value="{{ $month->month_id }}">

                            <div class="form-group">
                                <label for="days"><strong>{{ __('Ish kunlari soni') }}:</strong></label>
                                <input
                                    type="text"
                                    name="days"
                                    id="days"
                                    value="{{ old('days', $month->days) }}"
                                    class="form-control"
                                    placeholder="{{ __('Masalan: 22') }}"
                                >
                                @error('days')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center mt-4">
                                <a href="{{ route('month.index') }}" class="btn btn-primary">
                                    {{ __('Ortga') }}
                                </a>
                                <button type="submit" class="btn btn-success">
                                    {{ __('Saqlash') }}
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
