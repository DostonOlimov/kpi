@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-edit-3 mr-1"></i>&nbsp; {{ __('KPI tahrirlash') }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="mb-0">{{ __('Lavozim yo\'riqnomasini tahrirlash') }}</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('working-kpis.update', $kpi->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" value="{{ $user->id }}" name="user_id">

                            <div class="form-group">
                                <label>{{ __('Nomi') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $kpi->name) }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Maksimal ball') }}</label>
                                <input type="number" name="max_score" class="form-control"
                                       value="{{ old('max_score', $kpi->max_score) }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> {{ __('Saqlash') }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
