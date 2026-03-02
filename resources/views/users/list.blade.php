@extends('layouts.app')

@section('content')
<div class="section">

    <!-- PAGE-HEADER -->
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-life-buoy mr-1"></i>&nbsp; {{ trans("app.Arizalar ro'yxati") }}
            </li>
        </ol>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">

                    <!-- Tabs -->
                    <div class="panel panel-primary">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list">
                                <li class="active">
                                    <a href="{{ route('users.list') }}">
                                        <i class="fa fa-list fa-lg"></i>&nbsp;
                                        {{ trans("app.Ro'yxat") }}
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('users.create') }}">
                                        <i class="fa fa-plus-circle fa-lg"></i>&nbsp;
                                        <b>Foydalanuvchi yaratish</b>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Success message -->
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="table-responsive">

                        {{ $users->links() }}

                        <table class="table table-striped table-bordered nowrap display mt-4">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ismi</th>
                                    <th>Familyasi</th>
                                    <th>Sharifi</th>
                                    <th>Roli</th>
                                    <th>Ish joyi</th>
                                    <th>STIR</th>
                                    <th>Yaratilgan</th>
                                    <th>O‘zgartirilgan</th>
                                    <th width="150px">Amallar</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->father_name }}</td>
                                    <td>{{ $user->role_id }}</td>
                                    <td>{{ $user->work_zone_id }}</td>
                                    <td>{{ $user->user_tin }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>{{ $user->updated_at }}</td>

                                    <td>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="deleteForm">
                                            <a class="btn btn-primary btn-sm" href="{{ route('users.edit', $user->id) }}">
                                                Tahrirlash
                                            </a>

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm deleteBtn">
                                                O‘chirish
                                            </button>
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

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const deleteButtons = document.querySelectorAll('.deleteBtn');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                if (confirm("Rostdan ham ushbu foydalanuvchini o‘chirmoqchimisiz?")) {
                    this.closest('form').submit();
                }
            });
        });
    });
</script>
@endsection