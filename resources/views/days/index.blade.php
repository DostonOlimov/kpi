@extends('layouts.app')
<style>
        .forms-sample input{
            font-size: 16px;
            font-weight: bold;
        }
        .forms-sample select{
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body bg-white">


                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            {{ $message }}
                        </div>
                    @endif

                    <div class="mb-3 text-right">
                        <a class="btn btn-success" href="{{ route('roles.create') }}">+ Role yaratish</a>
                    </div>

                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Role nomi</th>
                            <th>Yaratilgan vaqti</th>
                            <th>O'zgartirilgan vaqti</th>
                            <th width="200px">Harakatlar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $role->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm mr-2">Tahrirlash</a>
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Haqiqatan ham o‘chirmoqchimisiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">O'chirish</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {!! $roles->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
