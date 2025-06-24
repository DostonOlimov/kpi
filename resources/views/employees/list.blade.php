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
                                        <a href="{{ route('employees.list') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i>
                                            {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a  href="{{ route('employees.create') }}">
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
                            <table class="table table-striped table-bordered nowrap display" style="margin-top:20px;" >
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>Ismi</th>
                                <th>Familiyasi</th>
                                <th>Sharifi</th>
                                <th>User_name</th>
                                <th>Oylik maoshi</th>
                                <th>Lavozimi</th>
                                <th>Roli</th>
                                <th>Ish joyi</th>
                                <th>Yaratilgan vaqti</th>
                                <th>O'zgartirilgan vaqti</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->father_name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->salary }}</td>
                                    <td>{{ $user->lavozimi }}</td>
                                    <td>{{ $user->role->name ?? '' }}</td>
                                    <td>{{ $user->work_zone->name ?? '' }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>{{ $user->updated_at }}</td>
                                    <td>
                                        <form action="{{ route('employees.destroy',$user->id) }}" method="Post">
                                            <a class="btn btn-primary" href="{{ route('employees.edit',$user->id) }}">Tahrirlash</a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">O'chirish</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection


