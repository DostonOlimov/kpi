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
    .kpi-card {
        border-left: 4px solid #4e73df; /* Accent border */
        background: #f8f9fc;
        transition: box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    .kpi-title {
        font-weight: 600;
        color: #343a40;
    }

    .kpi-score {
        background: #5aea60;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        color: #495057;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
</style>
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-bar-chart-2 mr-1"></i>&nbsp; {{ __('Xodimlarning funksioanal vazifalari') }}
                </li>
            </ol>
        </div>
        <div class="container-fluid py-4">
            <div class="row">
                <!-- Left Panel - Users -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white" ><i class="fa fa-users me-2"></i>Xodimlar</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($users as $user)
                                    <a href="#" class="list-group-item list-group-item-action user-item" data-user-id="{{ $user->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                @if(preg_match('/[A-Za-z]/u', $user->first_name))
                                                    {{ strtoupper(mb_substr($user->first_name, 0, 1, 'UTF-8')) }}
                                                @else
                                                    {{ mb_substr($user->first_name, 0, 1, 'UTF-8') }}
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->first_name .' ' .$user->last_name}}</h6>
                                                <small class="text-muted">{{ $user->role->name }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>

                <!-- Right Panel - User KPIs -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fa fa-line-chart me-2"></i>Xodimning vazifa ko'rsatkichlari</h5>
                        </div>
                        <div class="card-body">
                            <div id="userKpisContainer">
                                <div class="text-center text-muted py-5">
                                    <i class="fa fa-user-plus fa-3x mb-3"></i>
                                    <p>Xodimni tanlang va uning ko‘rsatkichlarini ko‘ring</p>
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
                        <a href="/working-kpis/kpi-index?user_id=${userId}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Qoʻshish
                        </a>
                    </div>
                `;
                    } else {
                          html += `
                       <a href="/working-kpis/kpi-index?user_id=${userId}" class="btn btn-primary pb-2">
                            <i class="fa fa-plus"></i> Ko'rsatkichlarni o'zagartirish
                        </a>
                    `;
                    userKpis.forEach(function(userKpi) {
                    const progress = (userKpi.actual_score / userKpi.target_score) * 100;

                    html += `
                       <div class="kpi-card card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-title">${userKpi.name}</div>
                                <div class="kpi-score">${userKpi.max_score}</div>
                            </div>
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
