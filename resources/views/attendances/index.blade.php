@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-users mr-1"></i> Davomat ro'yxati
                </li>
            </ol>
        </div>
  <!-- Work Zone Filter Component -->
                <x-work-zone-filter 
                    :actionUrl="route('attendances.index')" 
                    :showLabel="false" 
                    :autoSubmit="true" 
                />
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="fe fe-calendar mr-2"></i>Davomat sanasi: {{ date('d.m.Y', strtotime($date)) }}
                        </h5>
                        @if(session('message'))
                            <div class="alert alert-success mt-2 mb-0">
                                <i class="fa fa-check-circle mr-1"></i>{{ session('message') }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Upload Button -->
                    <a href="{{ route('attendances.upload', ['date' => $date]) }}" class="btn btn-primary">
                        <i class="fa fa-upload mr-1"></i> Excel yuklash
                    </a>
                </div>

                
                <!-- Filters Section -->
                <div class="filters-wrapper bg-light p-3 rounded">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <label class="form-label mb-2"><strong><i class="fe fe-clock mr-1"></i>Tezkor sana:</strong></label>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('attendances.index', ['date' => now()->format('Y-m-d')]) }}" 
                                   class="btn {{ $date == now()->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fa fa-calendar-day"></i> Bugun
                                </a>
                                <a href="{{ route('attendances.index', ['date' => now()->subDay()->format('Y-m-d')]) }}" 
                                   class="btn {{ $date == now()->format('Y-m-d') ? 'btn-outline-primary' : 'btn-outline-primary' }}">
                                    <i class="fa fa-calendar-minus"></i> Kecha
                                </a>
                                <a href="{{ route('attendances.index', ['date' => now()->subDays(7)->format('Y-m-d')]) }}" 
                                   class="btn {{ $date == now()->subDays(7)->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fa fa-calendar-week"></i> 7 kun oldin
                                </a>
                                <a href="{{ route('attendances.index', ['date' => now()->subDays(14)->format('Y-m-d')]) }}" 
                                   class="btn {{ $date == now()->subDays(14)->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fa fa-calendar-alt"></i> 2 hafta oldin
                                </a>
                                <a href="{{ route('attendances.index', ['date' => now()->subDays(30)->format('Y-m-d')]) }}" 
                                   class="btn {{ $date == now()->subDays(30)->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fa fa-calendar"></i> 1 oy oldin
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('attendances.index') }}" method="GET">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-2"><strong><i class="fe fe-filter mr-1"></i>Maxsus sana:</strong></label>
                                    <div class="input-group">
                                        <input type="date" name="date" id="date" class="form-control" 
                                               value="{{ $date }}" max="{{ now()->format('Y-m-d') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Qidirish
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

              
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-nowrap">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Ism</th>
                                <th>Familiya</th>
                                <th>Bo'lim</th>
                                <th>Fayldagi ismi</th>
                                <th>Kelish vaqti</th>
                                <th>Ketish vaqti</th>
                                <th>Holat</th>
                                <th>Izoh</th>
                                <th>Harakatlar</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($attendances as $index => $attendance)
                                <tr class="text-center align-middle">
                                    <td>{{ $attendances->firstItem() + $index }}</td>
                                    
                                    @if($attendance->user)
                                        <td><strong>{{ $attendance->user->first_name }}</strong></td>
                                        <td><strong>{{ $attendance->user->last_name }}</strong></td>
                                        <td>{{ $attendance->user->work_zone->name ?? '-' }}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif
                                    <td>{{ $attendance->name ?? '-' }}</td>
                                    {{-- First In with color coding --}}
                                    <td>
                                        @if($attendance->first_in)
                                            @if($attendance->is_late)
                                                <span class="text-danger font-weight-bold">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                    {{ date('H:i', strtotime($attendance->first_in)) }}
                                                </span>
                                            @else
                                                {{ date('H:i', strtotime($attendance->first_in)) }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Last Out with color coding --}}
                                    <td>
                                        @if($attendance->last_out)
                                            @if($attendance->is_early)
                                                <span class="text-danger font-weight-bold">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                    {{ date('H:i', strtotime($attendance->last_out)) }}
                                                </span>
                                            @else
                                                {{ date('H:i', strtotime($attendance->last_out)) }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Status Badge --}}
                                    <td>
                                        <span class="badge {{ $attendance->status_class }}">
                                            {{ $attendance->display_status }}
                                        </span>
                                        @if($attendance->is_late && $attendance->display_status !== 'Sababli')
                                            <br><small class="text-danger">Kech qoldi</small>
                                        @endif
                                        @if($attendance->is_early && $attendance->display_status !== 'Sababli')
                                            <br><small class="text-danger">Erta ketdi</small>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->comment ?? '-' }}</td>
                                    
                                    {{-- Actions --}}
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary edit-attendance-btn" 
                                                data-id="{{ $attendance->id }}"
                                                data-status="{{ $attendance->status }}"
                                                data-comment="{{ $attendance->comment }}"
                                                data-date="{{ $attendance->date }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fe fe-alert-circle fa-2x mb-2"></i>
                                        <p class="mb-0">Ushbu sanada davomat ma'lumotlari topilmadi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $attendances->appends(['date' => $date])->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Edit Attendance Modal --}}
<div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="editAttendanceModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editAttendanceForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title" id="editAttendanceModalLabel">
                        <i class="fe fe-edit mr-1"></i> Davomat holatini tahrirlash
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_status"><strong>Holat:</strong></label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="">Tanlang...</option>
                            <option value="Bor">Bor</option>
                            <option value="Yo'q">Yo'q</option>
                            <option value="Sababli">Sababli</option>
                            <option value="Ta'tilda">Ta'tilda</option>
                            <option value="Kasal">Kasal</option>
                            <option value="Ishda">Ishda</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_comment"><strong>Izoh:</strong></label>
                        <textarea name="comment" id="edit_comment" class="form-control" rows="3" 
                                  placeholder="Izoh kirishingiz mumkin..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Bekor qilish
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit attendance modal
        document.querySelectorAll('.edit-attendance-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const status = this.dataset.status || '';
                const comment = this.dataset.comment || '';
                const date = this.dataset.date;
                
                // Set form action
                document.getElementById('editAttendanceForm').action = '/attendances/' + id;
                
                // Fill form fields
                document.getElementById('edit_status').value = status;
                document.getElementById('edit_comment').value = comment;
                
                // Show modal
                $('#editAttendanceModal').modal('show');
            });
        });
    });
</script>
@endsection
