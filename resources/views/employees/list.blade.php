@extends('layouts.app')
@section('styles')
@endsection
@section('content')
    <div class="section">

        <!-- HEADER -->
        <div class="page-header mb-4">
            <h4 class="page-title text-white">
                <i class="fe fe-users me-2 text-white"></i> Xodimlar ro'yxati
            </h4>

            <ol class="breadcrumb">
                <li class="breadcrumb-item active text-white">Boshqaruv</li>
                <li class="breadcrumb-item text-white">Xodimlar</li>
            </ol>
        </div>

        <!-- ACTION TABS -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="btn-group">
                <a href="{{ route('works.child-list', ['workZone' => $workZone->parent_id]) }}"
                    class="btn btn-outline-primary">
                    <i class="fa fa-list"></i> Ro'yxat
                </a>

                <a href="{{ route('employees.create', ['id' => $workZone->id]) }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Yangi foydalanuvchi
                </a>
            </div>
        </div>

        <!-- USERS TABLE -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa fa-users me-2"></i> Xodimlar jadvali</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Rasm</th>
                                <th>Ism</th>
                                <th>Familiya</th>
                                <th>Login</th>
                                <th>PINFL</th>
                                <th>Oylik maosh</th>
                                <th>Bo'lim</th>
                                <th>Roli</th>
                                <th width="120px">Harakatlar</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>

                                    <td>
                                        <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('img/employee/avtar.png') }}"
                                            class="rounded-circle shadow-sm" width="48" height="48" alt="User Photo">
                                    </td>

                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->pinfl ?? '-' }}</td>

                                    <td>{{ number_format($user->salary, 0, '.', ' ') }} so'm</td>

                                    <td>{{ $user->work_zone->name ?? '-' }}</td>
                                    <td><span class="badge bg-primary">{{ $user->role->name ?? '-' }}</span></td>

                                    <td>
                                        <a href="{{ route('employees.edit', $user->id) }}"
                                            class="btn btn-sm btn-success me-1">
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
                </div>

                <!-- PAGINATION -->
                <div class="mt-3 d-flex justify-content-end">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    Swal.fire({
                        title: "Haqiqatdan ham o'chirmoqchimisiz?",
                        text: "Bu amal qaytarib bo'lmaydi!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ha, o'chir",
                        cancelButtonText: "Bekor qilish"
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = this.dataset.url;
                        }
                    });

                });
            });

        });
    </script>
@endsection
