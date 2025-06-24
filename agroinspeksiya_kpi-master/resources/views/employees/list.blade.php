@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h2>Foydalanuvchilar</h2>

                        <div class="pull-right mb-2">
                            <a class="btn btn-success" href="{{ route('employees.create') }}"> Foydalanuvchi yaratish</a>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <table class="table table-bordered table-responsive">
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
                        {!! $users->links() !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
