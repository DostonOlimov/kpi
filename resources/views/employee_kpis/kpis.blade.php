@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('css/kpi/employee-user-kpi2.css') }}">

@section('content')
    <div class="section">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><i class="fe fe-life-buoy mr-1"></i>&nbsp Xodim shaxsiy ko'rsatkichlari ro'yxati</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li class="active">
                                        <a href="{{ route('employee.kpis.users') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i>
                                            {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
                                                {{ trans('app.Qo\'shish')}}</b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @elseif($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <form id="kpiFilterForm" class="row mb-4">
                            <div class="col-md-4">
                                <input
                                    type="text"
                                    class="form-control"
                                    name="search"
                                    placeholder="KPI nomi bo‘yicha izlash"
                                    value="{{ request('search') }}">
                            </div>

                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">Status (barchasi)</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Faollar</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nofaollar</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select">
                                    <option value="">Kategoriya (barchasi)</option>
{{--                                    @foreach($kpi_categories as $category)--}}
{{--                                        <option value="{{ $category->id }}">{{ $category->name }}</option>--}}
{{--                                    @endforeach--}}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info" >Filterlash</button>
                            </div>
                        </form>
                        @php
                            $total = $user->user_kpis()
                                ->whereHas('kpi', function($q) {
                                    $q->where('type', \App\Models\Kpi::SELF_BY_PERSON);
                                })
                                ->sum('target_score') ?? 0;
                            $foiz = round(100 * $total / 60);
                        @endphp

                        <div class="alert alert-info" id="activeScoreInfo">
                            <div class="kpi-scores">
                                <span class="score-badge max">
                                    Faol KPI ballari jami: <span id="activeScoreTotal">{{ $total }}</span>/60
                                </span>
                                <span id="activeScorePercent"> {{ $foiz }}%</span>
                            </div>
                            <div class="progress">
                                <div id="progressBar" class="progress-bar bg-info" style="width: {{ $foiz }}%"></div>
                            </div>
                        </div>


        <!-- KPIs Grid -->
                        <div class="kpis-container">
                            <div class="row" id="kpisGrid">
                                @foreach($kpis as $kpi)
                                    @php
                                        $userKpi = $kpi->user_kpis->firstWhere('user_id', $user->id);
                                        $isActive = $userKpi && 1;
                                        $score = $userKpi->score ?? 0;
                                    @endphp
                                    <div class="col-lg-6 col-xl-4">
                                        <div class="card shadow-sm mb-4 ">
                                            <div class="card-header d-flex justify-content-between align-items-center {{ $isActive ? 'text-white' : 'bg-light' }}">
                                                <h5 class="mb-0 kpi-name" style="max-width: 80%;">{{ $kpi->name }}</h5>

                                                <button class="btn btn-sm toggle-kpi-btn {{ $isActive ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                        data-kpi-id="{{ $kpi->id }}"
                                                        data-active="{{ $isActive ? '1' : '0' }}"
                                                        data-score="{{ $kpi->max_score }}">
                                                    <i class="fa {{ $isActive ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                                    {{ $isActive ? 'Olib tashlash' : 'Faollashtirish' }}
                                                </button>

                                            </div>

                                            <div class="card-body">
                                                <p class="mb-2">
                                                    <strong>Ball:</strong> {{ $kpi->max_score }}
                                                </p>

                                                <!-- Blade HTML -->
                                                <span class="badge kpi-status-badge-{{ $kpi->id }} {{ $isActive ? 'bg-success' : 'bg-danger' }}">
    {{ $isActive ? 'Faol' : 'Faol emas' }}
</span>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </div>
                        </div>

                    @if($kpis->isEmpty())
                        <div id="emptyState" class="empty-state" >
                            <i class="fa fa-chart-line fa-3x"></i>
                            <h4>Ma'lumot topilmadi</h4>
                            <p>Xodimning shaxsiy ko'rsatkichalari bo'yicha hech nima topilmadi.</p>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('.toggle-kpi-btn').on('click', function () {
                const button = $(this);
                const kpiId = button.data('kpi-id');
                const isActive = parseInt(button.data('active'));
                const kpiScore = parseInt(button.data('score'));

                let currentTotal = parseInt($('#activeScoreTotal').text());

                // If activating and it would exceed the 60 limit
                if (!isActive && (currentTotal + kpiScore > 60)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cheklov!',
                        text: 'Faol KPI ballari jami 60 dan oshmasligi kerak.',
                    });
                    return;
                }

                Swal.fire({
                    title: isActive ? 'KPI ni o‘chirmoqchimisiz?' : 'KPI ni faollashtirmoqchimisiz?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: isActive ? 'Ha, o‘chirish' : 'Ha, faollashtirish',
                    cancelButtonText: 'Bekor qilish',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('user-kpi.toggle') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            kpi_id: kpiId,
                            user_id: "{{ $user->id }}"
                        },
                        success: function (response) {
                            // ✅ Update score, percentage, and progress bar
                            const newScore = response.total_active_score;
                            const newPercent = Math.round((newScore / 60) * 100);
                            $('#activeScoreTotal').text(newScore);
                            $('#activeScorePercent').text(`${newPercent}%`);
                            $('#progressBar').css('width', `${newPercent}%`);

                            // ✅ Toggle button styles & content
                            button.data('active', response.is_active ? '1' : '0');
                            button
                                .toggleClass('btn-outline-success btn-outline-danger')
                                .html(
                                    response.is_active
                                        ? '<i class="fa fa-ban"></i> Olib tashlash'
                                        : '<i class="fa fa-check-circle"></i> Faollashtirish'
                                );

                            // ✅ Card border style
                            button.closest('.card')
                                .toggleClass('border-success border-secondary');

                            // ✅ Update KPI status badge
                            const badge = $(`.kpi-status-badge-${kpiId}`);
                            badge
                                .removeClass('bg-success bg-danger')
                                .addClass(response.is_active ? 'bg-success' : 'bg-danger')
                                .text(response.is_active ? 'Faol' : 'Faol emas');

                            // ✅ Success feedback
                            Swal.fire({
                                icon: 'success',
                                title: 'Muvaffaqiyatli',
                                text: response.is_active
                                    ? 'KPI faollashtirildi.'
                                    : 'KPI olib tashlandi.',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Xatolik',
                                text: 'Amalni bajara olmadik. Iltimos, qayta urinib ko‘ring.',
                            });
                        }
                    });
                });

            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#kpiFilterForm input, #kpiFilterForm select').on('input change', function () {
                fetchKpis();
            });

            function fetchKpis() {
                let formData = $('#kpiFilterForm').serialize();

                $.ajax({
                    url: '{{ route("employee.kpis", $user->id) }}',
                    type: 'GET',
                    data: formData,
                    success: function (response) {
                        $('#kpisGrid').html(response.html);
                        $('#activeScoreTotal').text(response.total_active_score);
                    },
                    error: function () {
                        alert("Xatolik yuz berdi.");
                    }
                });
            }

            // Toggle logic stays the same as previous message, or can be reused here
        });
    </script>

@endsection
