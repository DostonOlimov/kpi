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
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('working-kpis.store') }}" method="POST">
                            @csrf

                            <input type="hidden" value="{{ $user->id }}" name="user_id">

                            <div class="form-group">
                                <label>{{ __('Nomi') }}</label>
                                <textarea name="name" class="form-control" required>{{ old('name') }}</textarea>
                            </div>

                            <div class="form-group"></div>
                                <label>{{ __('Maksimal ball') }}</label>
                                <input type="number" name="max_score" class="form-control" value="{{ old('max_score') }}" required>
                            </div>
                             <div class="form-group">
                                 <a class="btn btn-primary" href="{{ URL::previous() }}">
                                <i class="fa fa-warning" ></i>{{ trans('app.Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-success form">
                                <i class="fa fa-save"></i> {{ __('Saqlash') }}
                            </button>
                             </div>
                           
                        </form>
                    </div></button></a>
                </div>
            </div>
        </div>
    </div>
    @endsection
