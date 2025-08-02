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

                    <div class="card-header">
                        <h4 class="mb-0">{{ __("Qo'shimcha ish haqlari uchun summani o'zgartirish") }}</h4>
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
                                        <select name="month" id="month" class="form-control font-weight-bold">
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
