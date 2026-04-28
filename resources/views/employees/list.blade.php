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

                <a href="{{ route('employees.kpi-results', ['workZone' => $workZone->id]) }}" class="btn btn-info">
                    <i class="fa fa-bar-chart"></i> KPI Natijalar
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
                                {{-- <th>Oylik maosh</th> --}}
                                <th>Bo'lim</th>
                                <th>Roli</th>
                                <th>Harakatlar</th>
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

                                    {{-- <td>{{ number_format($user->salary, 0, '.', ' ') }} so'm</td> --}}

                                    <td>{{ $user->work_zone->name ?? '-' }}</td>
                                    <td>
                                        {{-- Default role badge --}}
                                        <span class="badge bg-primary me-1">{{ $user->role->name ?? '-' }}</span>
                                        {{-- Extra roles from user_roles (exclude default role_id) --}}
                                        @foreach($user->roles->where('id', '!=', $user->role_id) as $extraRole)
                                            <span class="badge bg-secondary me-1 d-inline-flex align-items-center gap-1">
                                                {{ $extraRole->name }}
                                                <button type="button"
                                                    class="btn-close btn-close-white remove-role-btn"
                                                    style="font-size:0.55rem"
                                                    data-user="{{ $user->id }}"
                                                    data-role="{{ $extraRole->id }}"
                                                    aria-label="Remove"></button>
                                            </span>
                                        @endforeach
                                        <button class="btn btn-sm btn-outline-primary ms-1 assign-role-btn"
                                            data-user="{{ $user->id }}"
                                            data-default-role="{{ $user->role_id }}"
                                            title="Rol qo'shish">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>

                                    <td>
                                        <a href="{{ route('employees.edit', $user->id) }}"
                                            class="btn btn-sm btn-success me-1">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <a href="{{ route('employees.edit-password', $user->id) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="fa fa-key"></i>
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

{{-- Assign Role Modal --}}
<div class="modal fade" id="assignRoleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Rol qo'shish</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="roleSelect" class="form-select">
                    <option value="">-- Rol tanlang --</option>
                    @foreach($allRoles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <div id="assignRoleError" class="text-danger mt-2 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
                <button type="button" id="confirmAssignRole" class="btn btn-primary">Saqlash</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let currentUserId = null;
            let currentDefaultRole = null;
            const assignModal = new bootstrap.Modal(document.getElementById('assignRoleModal'));
            const roleSelect = document.getElementById('roleSelect');
            const assignError = document.getElementById('assignRoleError');

            // Open assign modal
            document.querySelectorAll('.assign-role-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentUserId = this.dataset.user;
                    currentDefaultRole = this.dataset.defaultRole;
                    roleSelect.value = '';
                    assignError.classList.add('d-none');
                    assignError.textContent = '';
                    // Disable the default role option
                    Array.from(roleSelect.options).forEach(opt => {
                        opt.disabled = opt.value == currentDefaultRole;
                    });
                    assignModal.show();
                });
            });

            // Confirm assign role
            document.getElementById('confirmAssignRole').addEventListener('click', function() {
                if (!roleSelect.value) return;
                fetch(`/users/${currentUserId}/assign-role`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ role_id: roleSelect.value })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        assignModal.hide();
                        location.reload();
                    } else {
                        assignError.textContent = data.message || 'Xatolik yuz berdi';
                        assignError.classList.remove('d-none');
                    }
                });
            });

            // Remove extra role
            document.querySelectorAll('.remove-role-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = this.dataset.user;
                    const roleId = this.dataset.role;
                    if (!confirm("Ushbu rolni o'chirishni xohlaysizmi?")) return;
                    fetch(`/users/${userId}/remove-role`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ role_id: roleId })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) location.reload();
                    });
                });
            });

            // Delete user
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
