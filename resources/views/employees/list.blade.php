@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-users mr-1"></i>&nbsp; Xodimlar ro'yxati
                </li>
            </ol>
        </div>

        <!-- ACTION TABS -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus-circle mr-1"></i> Foydalanuvchi yaratish
                </a>
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success mb-0">
                    {{ $message }}
                </div>
            @endif
        </div>

        <!-- USERS TABLE -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-nowrap">
                        <thead class="thead-light">
                        <tr class="text-center">
                            <th>№</th>
                            <th>Rasm</th>
                            <th>Ism</th>
                            <th>Familiya</th>
                            <th>Login (username)</th>
                            <th>Oylik maosh</th>
                            <th>Lavozimi</th>
                            <th>Roli</th>
{{--                            <th>Ish joyi</th>--}}
                            <th>Harakatlar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $key => $user)
                            <tr class="text-center align-middle">
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="User Photo" class="rounded-circle" width="50" height="50">
                                    @else
                                        <img src="{{ asset('img/employee/avtar.png') }}" alt="No Photo" class="rounded-circle" width="50" height="50">
                                    @endif
                                </td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ number_format($user->salary, 2, '.', ' ') }} so'm</td>
                                <td>{{ $user->lavozimi }}</td>
                                <td>{{ $user->role->name ?? '-' }}</td>
{{--                                <td>{{ $user->work_zone->name ?? '-' }}</td>--}}
                                <td>
                                    <a href="{{ route('employees.edit', $user->id) }}" class="btn btn-sm btn-success mb-1">
                                        <i class="fa fa-pencil"></i> Tahrirlash
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger sa-warning" url="{{ url('/employees/delete', $user->id) }}">
                                        <i class="fa fa-trash"></i> O'chirish
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SWEET ALERT DELETE CONFIRMATION -->
    <script>
        $('body').on('click', '.sa-warning', function () {
            let url = $(this).attr('url');

            swal({
                title: "Haqiqatdan ham o'chirmoqchimisiz?",
                text: "Bu amal qaytarib bo'lmaydi!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#297FCA",
                confirmButtonText: "Ha, o'chir!",
                cancelButtonText: "Bekor qilish",
                closeOnConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>

@endsection


