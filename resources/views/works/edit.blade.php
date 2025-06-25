@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp; {{ __('Ma\'lumotlarni tahrirlash') }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list mb-0">
                                <li>
                                    <a href="{{ route('works.index') }}">
                                        <i class="fa fa-list fa-lg"></i>&nbsp; {{ __("Ro'yxat") }}
                                    </a>
                                </li>
                                <li class="active">
                                    <i class="fa fa-pencil fa-lg"></i>&nbsp; <strong>{{ __('Tahrirlash') }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form action="{{ route('works.update', $work->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name"><strong>{{ __('Ish joyi nomi') }}:</strong></label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            value="{{ old('name', $work->name) }}"
                                            class="form-control"
                                            placeholder="{{ __('Nomi') }}"
                                        >
                                        @error('name')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 text-center">
                                    <a href="{{ URL::previous() }}" class="btn btn-primary">
                                        {{ __('Bekor qilish') }}
                                    </a>
                                    <button type="submit" class="btn btn-success" onclick="disableButton()">
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
