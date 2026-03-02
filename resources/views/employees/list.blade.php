@extends('layouts.app')

@section('content')
<div class="section">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-users mr-1"></i> Xodimlar ro'yxati
            </li>
        </ol>
    </div>

    <!-- ACTION BAR -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fa fa-plus-circle mr-1"></i> Foydalanuvchi yaratish
        </a>

        @if(session('success'))
            <div class="alert alert-success mb-0">
                {{ session('success') }}
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
                            <th>#</th>
                            <th>Rasm</th>
                            <th>Ism</th>
                            <th>Familiya</th>
                            <th>Login</th>
                            <th>Oylik maosh</th>
                            <th>Bo'lim</th>
                            <th>Roli</th>
                            <th>Harakatlar</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr class="text-center align-middle">

                                <!-- Auto-increment with pagination -->
                                <td>{{ $users->firstItem() + $index }}</td>

                                <!-- User Photo -->
                                <td>
                                    <img 
                                        src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('img/employee/avtar.png') }}"
                                        class="rounded-circle shadow-sm"
                                        width="50" height="50" alt="User Photo">
                                </td>

                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->username }}</td>

                                <td>{{ number_format($user->salary, 2, '.', ' ') }} so'm</td>

                                <td>{{ $user->work_zone->name ?? '-' }}</td>
                                <td>{{ $user->role->name ?? '-' }}</td>

                                <td>
                                    <a href="{{ route('employees.edit', $user->id) }}"
                                       class="btn btn-sm btn-success mb-1">
                                        <i class="fa fa-edit"></i> 
                                    </a>

                                    <button class="btn btn-sm btn-danger delete-btn"
                                            data-url="{{ route('employees.delete', $user->id) }}">
                                        <i class="fa fa-trash"></i>
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
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.delete-btn').forEach(button => {

            button.addEventListener('click', function () {
                let deleteUrl = this.dataset.url;

                Swal.fire({
                    title: "Haqiqatdan ham o'chirmoqchimisiz?",
                    text: "Bu amal qaytarib bo'lmaydi!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ha, o'chir!",
                    cancelButtonText: "Bekor qilish"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
            });
        });
    });
</script>
@endsection