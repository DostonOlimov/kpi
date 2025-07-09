@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-plus-circle mr-1"></i>&nbsp; {{ __('Yangi lavozim yo\'riqnomasini qo‘shish') }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="mb-0">{{ __('Yangi lavozim yo\'riqnomasini qo‘shish') }}</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('working-kpis.store') }}" method="POST">
                            @csrf

                            <input type="hidden" value="{{ $user->id }}" name="user_id">

                            <div class="form-group">
                                <label>{{ __('Nomi') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Maksimal ball') }}</label>
                                <input type="number" name="max_score" class="form-control" value="{{ old('max_score') }}" required>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> {{ __('Saqlash') }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endsection
