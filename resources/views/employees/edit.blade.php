@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><i class="fe fe-life-buoy mr-1"></i>&nbsp Foydalanuvchi ma'lumotlarini tahrirlash</li>
            </ol>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li>
                                        <a href="{{ route('employees.list') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-plus-circle fa-lg">&nbsp;</i>
                                        <b>{{ trans('app.Qo\'shish')}}</b>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    @if(session('status'))
            <div class="alert alert-success mb-1 mt-1">
                {{ session('status') }}
            </div>
        @endif
        <form action="{{ route('employees.update',$user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>User Name:</strong>
                        <input type="text" name="username" class="form-control" value="{{ $user ->username }}" >
                        @error('username')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Parol:</strong>
                        <input type="text" name="password" class="form-control" >
                        @error('password')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Ismi:</strong>
                        <input type="text" name="first_name" class="form-control"value="{{ $user->first_name }}" >
                        @error('first_name')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Familiyasi:</strong>
                        <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}">
                        @error('last_name')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Sharifi:</strong>
                        <input type="text" name="father_name" class="form-control"value="{{ $user->father_name }}" >
                        @error('father_name')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Roli:</strong>
                        <select name="role_id" class="form-control" id="" value="{{ $user->role_id }}" >
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Ish joyi:</strong>
                        <select name="work_zone_id" class="form-control" id="work_zone_id" value="{{ $user->work_zone_id }}">
                            @foreach($works as $work)
                                <option value="{{$work->id}}">{{$work->name}}</option>
                            @endforeach
                        </select>
                        @error('work_zone_id')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Lavozimi:</strong>
                        <input type="text" name="lavozimi" class="form-control" value="{{ $user->lavozimi }}">
                        @error('lavozimi')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Oylik maoshi:</strong>
                        <input type="number" step="0.0001" name="salary" class="form-control"placeholder="so'm" value="{{ $user->salary }}">
                        @error('salary')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group col-md-12 text-center">
                    <a class="btn btn-primary" href="{{ URL::previous() }}">
                        {{ trans('app.Cancel') }}
                    </a>
                    <button type="submit" id="submitter" class="btn btn-success" onclick="disableButton()">
                        {{ trans('app.Submit') }}
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

