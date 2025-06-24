@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('app.Arizalar ro\'yxati')}}</li>
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
                                        <a href="{{ route('users.list') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i>
                                            {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a  href="{{ route('users.create') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
                                                Foydalanuvchi yaratish</b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class="table-responsive">
                            {{ $users->links() }}
                            <table class="table table-striped table-bordered nowrap display" style="margin-top:20px;" >
                            <thead>
                            <tr>
                                <th>Tartib raqam</th>
                                <th>ISMI</th>
                                <th>FAMILYASI</th>
                                <th>SHARIFI</th>
                                <th>Roli</th>
                                <th>Ish joyi</th>
                                <th>STIR raqami</th>
                                <th>Yaratilgan vaqti</th>
                                <th>O'zgartirilgan vaqti</th>
                                <th width="280px">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->father_name }}</td>
                                    <td>{!! $user->role_id !!}</td>
                                    <td>{{ $user->work_zone_id }}</td>
                                    <td>{{ $user->user_tin }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>{{ $user->updated_at }}</td>
                                    <td>
                                        <form action="{{ route('users.destroy',$user->id) }}" method="Post">
                                            <a class="btn btn-primary"
                                               href="{{ route('users.edit',$user->id) }}">Tahrirlash</a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">O'chirish</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                            {{ $apps->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
