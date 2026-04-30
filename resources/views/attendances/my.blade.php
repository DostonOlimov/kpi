@extends('layouts.app')

@section('content')
<div class="section">
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><i class="fe fe-calendar mr-1"></i> Mening davomatim</li>
        </ol>
    </div>

    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #3c4b64 0%, #4f6080 100%); border-bottom: none; padding: 1rem 1.5rem;">
            <div class="d-flex align-items-center" style="gap:1rem; flex-wrap:wrap;">

                {{-- Icon + Title --}}
                <div class="d-flex align-items-center" style="gap:0.75rem; flex-shrink:0;">
                    <div style="width:42px;height:42px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fe fe-calendar" style="font-size:1.2rem;color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="color:#fff;font-weight:600;line-height:1.2;">
                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                        </h5>
                        <small style="color:rgba(255,255,255,0.6);font-size:0.75rem;">{{ $year }} — Shaxsiy davomat tarixi</small>
                    </div>
                </div>

                {{-- Divider --}}
                <div style="width:1px;height:36px;background:rgba(255,255,255,0.2);flex-shrink:0;"></div>

                {{-- 12 month buttons (multi-select) --}}
                @php
                    $monthNames = [1=>'Yanvar',2=>'Fevral',3=>'Mart',4=>'Aprel',5=>'May',6=>'Iyun',
                                   7=>'Iyul',8=>'Avgust',9=>'Sentabr',10=>'Oktobar',11=>'Noyabr',12=>'Dekabr'];
                @endphp
                <form id="monthForm" action="{{ route('attendances.my') }}" method="GET" style="flex:1;">
                    <div class="d-flex align-items-center" style="gap:6px; flex-wrap:wrap;">
                        @foreach ($monthNames as $num => $name)
                            <button type="button"
                                    onclick="toggleMonth({{ $num }}, this)"
                                    class="btn btn-sm month-btn {{ in_array($num, $selected) ? 'btn-light' : 'btn-outline-light' }}"
                                    style="border-radius:20px;font-size:0.82rem;padding:5px 15px;line-height:1.4;">
                                {{ $name }}
                            </button>
                        @endforeach

                        {{-- Hidden inputs for selected months --}}
                        <div id="hiddenMonths">
                            @foreach ($selected as $s)
                                <input type="hidden" name="months[]" value="{{ $s }}">
                            @endforeach
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="card-body">

            {{-- Summary badges --}}
            @php
                $total   = $attendances->count();
                $present = $attendances->filter(fn($a) => $a->display_status === 'Bor')->count();
                $absent  = $attendances->filter(fn($a) => $a->display_status === "Yo'q")->count();
                $other   = $total - $present - $absent;
            @endphp

            <div class="row mb-3 text-center">
                <div class="col-6 col-md-3 mb-2">
                    <div class="card border-primary h-100">
                        <div class="card-body py-2">
                            <h4 class="text-primary mb-0">{{ $total }}</h4>
                            <small class="text-muted">Jami yozuv</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="card border-success h-100">
                        <div class="card-body py-2">
                            <h4 class="text-success mb-0">{{ $present }}</h4>
                            <small class="text-muted">Bor</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="card border-danger h-100">
                        <div class="card-body py-2">
                            <h4 class="text-danger mb-0">{{ $absent }}</h4>
                            <small class="text-muted">Yo'q</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="card border-warning h-100">
                        <div class="card-body py-2">
                            <h4 class="text-warning mb-0">{{ $other }}</h4>
                            <small class="text-muted">Boshqa</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Sana</th>
                            <th>Kelish vaqti</th>
                            <th>Ketish vaqti</th>
                            <th>Holat</th>
                            <th>Izoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $index => $attendance)
                            <tr class="text-center align-middle">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ date('d.m.Y', strtotime($attendance->date)) }}</td>

                                {{-- First In --}}
                                <td>
                                    @if ($attendance->first_in)
                                        @if ($attendance->is_late)
                                            <span class="text-danger font-weight-bold">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ date('H:i', strtotime($attendance->first_in)) }}
                                            </span>
                                            <br><small class="text-danger">Kech qoldi</small>
                                        @else
                                            {{ date('H:i', strtotime($attendance->first_in)) }}
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Last Out --}}
                                <td>
                                    @if ($attendance->last_out)
                                        @if ($attendance->is_early)
                                            <span class="text-danger font-weight-bold">
                                                <i class="fa fa-exclamation-circle"></i>
                                                {{ date('H:i', strtotime($attendance->last_out)) }}
                                            </span>
                                            <br><small class="text-danger">Erta ketdi</small>
                                        @else
                                            {{ date('H:i', strtotime($attendance->last_out)) }}
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span class="badge {{ $attendance->status_class }}">
                                        {{ $attendance->display_status }}
                                    </span>
                                </td>

                                <td>{{ $attendance->comment ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fe fe-alert-circle fa-2x mb-2"></i>
                                    <p class="mb-0">Ushbu oy uchun davomat ma'lumotlari topilmadi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

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
            if (selectedMonths.length === 1) return; // keep at least 1 selected
            selectedMonths.splice(idx, 1);
            btn.classList.remove('btn-light');
            btn.classList.add('btn-outline-light');
        } else {
            selectedMonths.push(num);
            btn.classList.remove('btn-outline-light');
            btn.classList.add('btn-light');
        }
        syncHidden();
        document.getElementById('monthForm').submit();
    }
</script>
@endsection
