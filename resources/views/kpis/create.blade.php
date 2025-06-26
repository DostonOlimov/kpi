@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-plus-circle mr-1"></i>&nbsp; {{ __('Yangi KPI qo‘shish') }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="mb-0">{{ __('Yangi KPI qo‘shish') }}</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('kpis.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>{{ __('KPI nomi') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Ota toifa (ixtiyoriy)') }}</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">{{ __('Asosiy toifa') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Maksimal ball (ixtiyoriy)') }}</label>
                                <input type="number" name="max_score" class="form-control" value="{{ old('max_score') }}">
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
