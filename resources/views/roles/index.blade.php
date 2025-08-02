@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><i class="fe fe-life-buoy mr-1"></i>&nbsp Rollar ro'yxati</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li class="active">
                                        <a href="#">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i>
                                            {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('roles.create') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
                                                {{ trans('app.Qo\'shish')}}</b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @elseif($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                            </div>
                        @endif



                        <div class="table-responsive">
                            <table class="table table-striped table-bordered nowrap display" style="margin-top:20px;" >
                            <thead>
                            <tr>
                                <th>Tartib raqam</th>
                                <th>Nomi</th>
                                <th>Yaratilgan vaqti</th>
                                <th>O'zgartirilgan vaqti</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->created_at }}</td>
                                    <td>{{ $role->updated_at }}</td>
                                    <td>
                                        <form action="{{ route('roles.destroy',$role->id) }}" method="Post">
                                            <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}">Tahrirlash</a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">O'chirish</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            </table>
                            {{ $roles->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
