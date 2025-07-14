@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-edit-3 mr-1"></i>&nbsp; {{ __('KPI ko\'rsatkichini tahrirlash') }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="panel panel-primary">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list">
                                <li class="active">
                                    <a href="#">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-pencil fa-lg">&nbsp;</i>
                                        {{ trans('app.Tahrirlash')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
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
                            <a class="btn btn-primary" href="{{ URL::previous() }}">
                                <i class="fa fa-warning" ></i>{{ trans('app.Cancel') }}
                            </a>
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
