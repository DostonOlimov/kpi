@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><i class="fe fe-life-buoy mr-1"></i>&nbsp Foydalanuvchi qo'shish</li>
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
                        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">User Name:</label>
                                        <input type="text" name="username" class="form-control" value="{{ old('username') }}">
                                        @error('username')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Parol:</label>
                                        <input type="text" name="password" class="form-control" value="{{ old('password') }}">
                                        @error('password')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Ismi:</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}">
                                        @error('first_name')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Familiyasi:</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                                        @error('last_name')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Sharifi:</label>
                                        <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}">
                                        @error('father_name')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Roli:</label>
                                        <select name="role_id" class="form-control" id="" >
                                            <option value="">Rolni tanlang...</option>
                                            @foreach($roles as $role)
                                                <option @if(old('role_id') == $role->id) selected @endif value="{{$role->id}}">{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Ish joyi:</label>
                                        <select name="work_zone_id" class="form-control" id="work_zone_id">
                                            <option value="">Bo'limni tanlang...</option>
                                            @foreach($works as $work)
                                                <option @if(old('work_zone_id') == $work->id) selected @endif value="{{$work->id}}">{{$work->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('work_zone_id')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Lavozimi:</label>
                                        <input type="text" name="lavozimi" class="form-control" value="{{ old('lavozimi') }}">
                                        @error('lavozimi')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Oylik maoshi:</label>
                                        <input type="number" step="0.01" name="salary" class="form-control" placeholder="so'm" value="{{ old('salary') }}" >
                                        @error('salary')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Foydalanuvchi rasmi (foto):</label>
                                        <input type="file" name="photo" class="form-control">
                                        @error('photo')
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
