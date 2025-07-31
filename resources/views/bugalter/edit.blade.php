@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-dollar-sign mr-1"></i>&nbsp; {{ __("Qo'shimcha ish haqlari uchun summani o'zgartirish") }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                   <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list mb-0">
                                <li >
                                    <a href="{{ route('bugalter.check') }}">
                                        <i class="fa fa-list fa-lg"></i>&nbsp; {{ __("Ro'yxat") }}
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="#">
                                        <i class="fa fa-pencil fa-lg"></i>&nbsp; <strong>{{ __("Tahrirlash") }}</strong>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('bugalter.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="summa"><strong>{{ __('Pul miqdori') }}:</strong></label>
                                        <input
                                            type="number"
                                            name="summa"
                                            id="summa"
                                            class="form-control font-weight-bold"
                                            max="9999999999999"
                                            value="{{ $data->summa }}"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="month"><strong>{{ __('Hisobot oyi') }}:</strong></label>
                                        <select name="month" id="month" class="form-control font-weight-bold" disabled>
                                            @foreach($month as $key => $item)
                                                <option value="{{ $key + 1 }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary">
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
