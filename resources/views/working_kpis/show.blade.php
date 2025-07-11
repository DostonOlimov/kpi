@extends('layouts.app')
<style>
    .kpi-card {
        transition: all 0.3s ease;
        border-left: 4px solid #007bff;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .progress-custom {
        height: 8px;
        border-radius: 10px;
    }
    .kpi-card {
        border-left: 4px solid #0d6efd; /* Primary accent border */
        background-color: #f8f9fa;
        transition: box-shadow 0.2s ease-in-out;
    }

    .kpi-card:hover {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .kpi-title {
        font-weight: 600;
        color: #212529;
    }

    .kpi-score {
        background: #e9ecef;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        color: #495057;
    }

    .kpi-actions .btn {
        margin-left: 8px;
    }
</style>
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-bar-chart-2 mr-1"></i>&nbsp;{{ $user->first_name . ' ' . $user->last_name }} ning lavozim bo'yicha ko'rsatkichlari
                </li>
            </ol>
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
        <div class="container-fluid py-4">
            <div class="row">
                <!-- Left Panel - Users -->

                <!-- Right Panel - User KPIs -->
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li class="active">
                                        <a href="{{ route('working-kpis.index') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i>
                                            {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/working-kpis/create?user_id={{ $user->id }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
                                                {{ trans('app.Qo\'shish')}}</b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="userKpisContainer">
                                    @foreach($userKpis as $kpi)
                                    <div class="kpi-card card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                <div style="max-width: 85%">
                                                    <h6 class="card-title kpi-title mb-1">{{ $kpi->name }}</h6>
                                                    <div class="kpi-score">Maksimal ball: {{ $kpi->max_score }}</div>
                                                </div>
                                                <div class="kpi-actions d-flex mt-2 mt-md-0">
                                                    <a href="{{ route('working-kpis.edit', $kpi->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fa fa-pencil"></i> O'zgartirish
                                                    </a>

                                                    <form action="{{ route('working-kpis.destroy', $kpi->id) }}" method="POST" onsubmit="return confirm('Haqiqatan ham o\'chirmoqchimisiz?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fa fa-trash"></i> O'chirish
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
        @section('scripts')
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

            <script>
                $(document).ready(function() {

                    let selectedUserId = null;

                    // User selection
                    $('.user-item').click(function(e) {
                        e.preventDefault();
                        $('.user-item').removeClass('active');
                        $(this).addClass('active');

                        selectedUserId = $(this).data('user-id');
                        const userName = $(this).find('h6').text();

                        $('#selectedUserId').val(selectedUserId);
                        $('#selectedUserName').val(userName);

                        loadUserKPIs(selectedUserId);
                    });

                    // Load user KPIs
                    function loadUserKPIs(userId) {
                        $('#userKpisContainer').html('<div class="text-center"><div class="spinner-border"></div></div>');

                        $.get(`/working-kpis/user/${userId}`)
                            .done(function(userKpis) {
                                let html = '';

                                if (userKpis.length === 0) {
                                    html = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-clipboard-list fa-2x mb-3"></i>
                        <p>Hech qanday ma'lumot topilmadi</p>
                        <a href="/working-kpis/create?user_id=${userId}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Qoʻshish
                        </a>
                    </div>
                `;
                                } else {
                                    html += `
                       <a href="/working-kpis/kpi-index?user_id=${userId}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Qoʻshish
                        </a>
                    `;
                                    userKpis.forEach(function(userKpi) {
                                        const progress = (userKpi.actual_score / userKpi.target_score) * 100;

                                        html += `
                        <div class="kpi-card card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">${userKpi.name}</h6>
                                    <h6 class="card-title mb-0">${userKpi.max_score}</h6>
                                </div>

                                ${userKpi.target_score > 0 ? `
                                    <div class="progress progress-custom">
                                        <div class="progress-bar" style="width: ${Math.min(progress, 100)}%"></div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                                    });
                                }

                                $('#userKpisContainer').html(html);
                            })
                            .fail(function() {
                                $('#userKpisContainer').html('<div class="alert alert-danger">Foydalanuvchi ma\'lumotlarini yuklashda xatolik yuz berdi</div>');
                            });
                    }


                    // Show alert function
                    function showAlert(message, type) {
                        const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                        $('body').append(alertHtml);

                        setTimeout(function() {
                            $('.alert').fadeOut();
                        }, 5000);
                    }
                });
            </script>
@endsection
