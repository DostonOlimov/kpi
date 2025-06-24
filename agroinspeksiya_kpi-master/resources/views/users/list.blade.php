@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body" style="background-color:white">
                        <!-- <div class="col-lg-12 grid-margin stretch-card">
                         <div class="card"> -->
                        <!-- <div class="card-body"> -->
                        <h2>Foydalanuvchilar</h2>

                        <div class="mb-2">
                            <a class="btn btn-success" href="{{ route('users.create') }}"> Foydalanuvchi yaratish</a>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <table class="table table-bordered table-responsive">
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
                        {!! $users->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
