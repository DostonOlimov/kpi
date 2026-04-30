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
            <div class="card-header" style="background: linear-gradient(135deg, #3c4b64 0%, #4f6080 100%); border-bottom: none; padding: 1rem 1.5rem;">

                @if(session('message'))
                    <div class="alert alert-success py-2 mb-3">
                        <i class="fa fa-check-circle mr-1"></i>{{ session('message') }}
                    </div>
                @endif

                {{-- Row 1: Month multi-select --}}
                @php
                    $monthNames = [1=>'Yanvar',2=>'Fevral',3=>'Mart',4=>'Aprel',5=>'May',6=>'Iyun',
                                   7=>'Iyul',8=>'Avgust',9=>'Sentabr',10=>'Oktobar',11=>'Noyabr',12=>'Dekabr'];
                @endphp
                <div style="padding-right: 50px; display: flex; align-items: center;">
                <form id="filterForm" action="{{ route('attendances.index') }}" method="GET">
                    {{-- Month pills row --}}
                    <div class="d-flex align-items-center" style="gap:6px; flex-wrap:wrap; margin-bottom:10px;">
                        <small style="color:rgba(255, 255, 255, 0.949);font-size:0.75rem;flex-shrink:0;min-width:36px;">Oy:</small>
                        @foreach ($monthNames as $num => $name)
                            <button type="button"
                                    onclick="toggleMonth({{ $num }}, this)"
                                    class="btn btn-sm month-btn {{ in_array($num, $selected) ? 'btn-light' : 'btn-outline-light' }}"
                                    style="border-radius:20px;font-size:0.82rem;padding:5px 15px;line-height:1.4;">
                                {{ $name }}
                            </button>
                        @endforeach
                        <div id="hiddenMonths">
                            @foreach ($selected as $s)
                                <input type="hidden" name="months[]" value="{{ $s }}">
                            @endforeach
                        </div>
                    </div>

                    {{-- Day filter row (only when single month selected) --}}
                    @if($daysInMonth)
                        <div class="d-flex align-items-center" style="gap:5px; flex-wrap:wrap;">
                            <small style="color:rgba(255,255,255,0.65);font-size:0.75rem;flex-shrink:0;min-width:36px;">Kun:</small>
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                <button type="button"
                                        onclick="selectDay({{ $d }}, this)"
                                        class="btn btn-sm day-btn {{ (int)$day === $d ? 'btn-warning' : 'btn-outline-light' }}"
                                        style="border-radius:50%;width:30px;height:30px;padding:0;font-size:0.73rem;line-height:30px;flex-shrink:0;">
                                    {{ $d }}
                                </button>
                            @endfor
                            @if($day)
                                <button type="button" onclick="clearDay()"
                                        class="btn btn-sm btn-outline-light"
                                        style="border-radius:20px;font-size:0.75rem;padding:3px 12px;margin-left:4px;flex-shrink:0;">
                                    <i class="fa fa-times mr-1"></i>Tozalash
                                </button>
                            @endif
                            <input type="hidden" id="hiddenDay" name="day" value="{{ $day ?? '' }}">
                        </div>
                    @endif
                </form>
                </div>
                                {{-- Top row: title + upload button --}}
               <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('attendances.upload') }}" 
                    class="btn btn-light"
                    style="font-weight:600; font-size:0.95rem; padding:8px 18px; border-radius:8px;">
                        <i class="fa fa-upload mr-1"></i> Ma'lumotlarni yuklash
                    </a>
                </div>

            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-nowrap">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Sana</th>
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
                                    <td>{{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('d.m.Y') : '-' }}</td>
                                    
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
                                    <td colspan="11" class="text-center text-muted py-4">
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
                    {{ $attendances->links() }}
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
    let selectedMonths = @json($selected);

    function syncHidden() {
        const container = document.getElementById('hiddenMonths');
        container.innerHTML = '';
        selectedMonths.forEach(m => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'months[]';
            inp.value = m;
            container.appendChild(inp);
        });
    }

    function toggleMonth(num, btn) {
        const idx = selectedMonths.indexOf(num);
        if (idx > -1) {
            if (selectedMonths.length === 1) return;
            selectedMonths.splice(idx, 1);
            btn.classList.remove('btn-light');
            btn.classList.add('btn-outline-light');
        } else {
            selectedMonths.push(num);
            btn.classList.remove('btn-outline-light');
            btn.classList.add('btn-light');
        }
        // reset day when months change
        const hd = document.getElementById('hiddenDay');
        if (hd) hd.value = '';
        syncHidden();
        document.getElementById('filterForm').submit();
    }

    function selectDay(d, btn) {
        document.getElementById('hiddenDay').value = d;
        document.querySelectorAll('.day-btn').forEach(b => {
            b.classList.remove('btn-warning');
            b.classList.add('btn-outline-light');
        });
        btn.classList.add('btn-warning');
        btn.classList.remove('btn-outline-light');
        syncHidden();
        document.getElementById('filterForm').submit();
    }

    function clearDay() {
        document.getElementById('hiddenDay').value = '';
        syncHidden();
        document.getElementById('filterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-attendance-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const status = this.dataset.status || '';
                const comment = this.dataset.comment || '';
                document.getElementById('editAttendanceForm').action = '/attendances/' + id;
                document.getElementById('edit_status').value = status;
                document.getElementById('edit_comment').value = comment;
                $('#editAttendanceModal').modal('show');
            });
        });
    });
</script>
@endsection
