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
                        <h3 class="mb-0">{{ __('KPI tahrirlash') }}</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('kpis.update', $kpi->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>{{ __('KPI nomi') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $kpi->name) }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Ota toifa (ixtiyoriy)') }}</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">{{ __('Asosiy toifa') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('parent_id', $kpi->parent_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Maksimal ball (ixtiyoriy)') }}</label>
                                <input type="number" name="max_score" class="form-control"
                                       value="{{ old('max_score', $kpi->max_score) }}">
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
