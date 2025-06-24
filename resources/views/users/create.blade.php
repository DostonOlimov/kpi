@extends('layouts.app')

@section('content')
<div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left mb-2">
                    <h2>Role qo'shish</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('roles.index') }}"> Ortga</a>
                </div>
            </div>
        </div>
        @if(session('status'))
        <div class="alert alert-success mb-1 mt-1">
            {{ session('status') }}
        </div>
        @endif
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>User Name:</strong>
                        <input type="text" name="username" class="form-control" >
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
                        <input type="text" name="first_name" class="form-control" >
                        @error('first_name')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Familiyasi:</strong>
                        <input type="text" name="last_name" class="form-control" >
                        @error('last_name')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Sharifi:</strong>
                        <input type="text" name="father_name" class="form-control" >
                        @error('father_name')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Roli:</strong>
                        <select name="role_id" class="form-control" id="" >
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                        @error('role_id')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Ish joyi:</strong>
                        <select name="work_zone_id" class="form-control" id="work_zone_id">
                                        @foreach($works as $work)
                                            <option value="{{$work->id}}">{{$work->name}}</option>
                                        @endforeach
                            </select>
                        @error('work_zone_id')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>JS SHIR / STIR:</strong>
                        <input type="text" name="user_tin" class="form-control" >
                        @error('user_tin')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary ml-3">Saqlash</button>
            </div>
        </form>
</div>
    <!-- <style>
        .forms-sample input{
            font-size: 16px;
            font-weight: bold;
        }
        .forms-sample select{
            font-size: 16px;
            font-weight: bold;
        }
    </style>
    <div class="content-wrapper">
        
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Yangi foydalanuvchi qo'shish</span>  </h4>
                </div>
            </div>
        </div>
        @if (session()->has('fail'))
            <div class="alert alert-danger">
                {{ session()->get('fail') }}
            </div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Foydalanuvchi ma'lumotlari</h4>
                        <form class="forms-sample" action="{{ route('employees.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group col-4">
                                    <label for="">Familiya</label>
                                    <input name="name1" class="form-control" id="">
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Ism</label>
                                    <input name="name2" class="form-control" id="">
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Sharif</label>
                                    <input name="name3" class="form-control" id="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="">Tizimdagi roli (lavozimi)</label>
                                    <select name="role" class="form-control" id="" >
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Faoliyat yo'nalishi</label>
                                    <select name="work_id" class="form-control" id="">
                                        @foreach($works as $work)
                                            <option value="{{$work->id}}">{{$work->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-4">
                                    <label for="">JS SHIR / STIR</label>
                                    <input name="tin" type="text" class="form-control" minlength="9" maxlength="14" id="" placeholder="">
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Foydalanuvchi logini</label>
                                    <input name="login" type="text" class="form-control" id="" placeholder="">
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Foydalanuvchi paroli</label>
                                    <input name="pass" type="password" class="form-control" id="" placeholder="">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mr-2">Saqlash</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
@endsection
