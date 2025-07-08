@extends('layouts.app')
@section('styles')
    <style>
    .kpi-card {
        transition: all 0.3s ease;
        border-left: 4px solid #007bff;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .category-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .progress-custom {
        height: 8px;
        border-radius: 10px;
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
    .loading-spinner {
        display: none;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-bar-chart-2 mr-1"></i>&nbsp; {{ __('Xodimning KPI Ko‘rsatkichlari') }}
                </li>
            </ol>
        </div>
        <div class="container-fluid py-4">
            <div class="row">
                <!-- Left Panel - Users -->
                <div class="col-md-3">
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
                        </div>
                    </div>
                </div>

                <!-- Middle Panel - KPI Assignment -->
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 text-white"><i class="fa fa-plus-circle me-2"></i>KPI larni biriktirish</h5>
                        </div>
                        <div class="card-body">
                            <form id="assignKpiForm">
                                <div class="mb-3">
                                    <label class="form-label">Tanlangan xodim </label>
                                    <input type="text" class="form-control" id="selectedUserName" readonly placeholder="Xodimni chap paneldan tanlang">
                                    <input type="hidden" id="selectedUserId">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kategoriya</label>
                                    <select class="form-select" id="categorySelect">
                                        <option value="">Kategoriyani tanlang</option>
                                        @foreach($kpis as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">KPI maqsadli ko'rsatkich</label>
                                    <select class="form-select" id="kpiSelect" disabled>
                                        <option value="">KPI ni tanlang</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">To'planishi kerak bo'lgan ball</label>
                                    <input type="number" class="form-control" id="targetScore" min="0" placeholder="Eng yoqori ko'rsatkich" readonly>
                            
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fa fa-plus me-2"></i>KPI ni biriktirish
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - User KPIs -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fa fa-chart-line me-2"></i>Xodimning KPI ko'rsatkichlari</h5>
                        </div>
                        <div class="card-body">
                            <div id="userKpisContainer">
                                <div class="text-center text-muted py-5">
                                    <i class="fa fa-user-plus fa-3x mb-3"></i>
                                    <p>Xodimni tanlang va uning KPI ko‘rsatkichlarini ko‘ring</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- KPI ni tahrirlash oynasi -->
    <div class="modal fade" id="editKpiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">KPI ni o'zgartirish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editKpiForm">
                        <input type="hidden" id="editKpiId">
                        <div class="mb-3">
                            <label class="form-label">KPI nomi</label>
                            <input type="text" class="form-control" id="editKpiName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maqsadli ball</label>
                            <input type="number" readonly class="form-control" id="editTargetScore" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hozirgi ball</label>
                            <input type="number" readonly class="form-control" id="editCurrentScore" min="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="button" class="btn btn-primary" id="saveKpiChanges">O'zgarishlarni saqlash</button>
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
            // CSRF token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let selectedUserId = null;
            let editKpiModal = new bootstrap.Modal(document.getElementById('editKpiModal'));

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

            // Category selection
            $('#categorySelect').change(function() {
                const categoryId = $(this).val();
                $('#kpiSelect').prop('disabled', true).html('<option value="">Select KPI</option>');

                if (categoryId) {
                    $.get(`/kpis/category/${categoryId}`)
                        .done(function(kpis) {
                            let options = '<option value="">Select KPI</option>';
                            kpis.forEach(function(kpi) {
                                options += `<option value="${kpi.id}" data-max-score="${kpi.max_score}">${kpi.name} (Max: ${kpi.max_score})</option>`;
                            });
                            $('#kpiSelect').html(options).prop('disabled', false);
                        })
                        .fail(function() {
                            showAlert('KPI larni yuklashda xatolik yuz berdi', 'danger');
                        });
                }
            });

            // KPI selection - auto-fill target score
            $('#kpiSelect').change(function() {
                const selectedOption = $(this).find('option:selected');
                const maxScore = selectedOption.data('max-score');
                if (maxScore) {
                    $('#targetScore').attr('max', maxScore).attr('placeholder', `Max score: ${maxScore}`);
                }
            });

            // Assign KPI form
            $('#assignKpiForm').submit(function(e) {
                e.preventDefault();

                if (!selectedUserId) {
                   showAlert('Iltimos, avval foydalanuvchini tanlang', 'warning');
                    return;
                }

                const formData = {
                    user_id: selectedUserId,
                    kpi_id: $('#kpiSelect').val(),
                    target_score: $('#targetScore').val() || null
                };
            
                if (!formData.kpi_id) {
                   showAlert('Iltimos, KPI ni tanlang', 'warning');
                    return;
                }

                const submitBtn = $(this).find('button[type="submit"]');
             //   const spinner = submitBtn.find('.loading-spinner');

                submitBtn.prop('disabled', true);
            //    spinner.show()

                $.post('/user-kpis', formData)
                    .done(function(response) {
                        showAlert(response.message, 'success');
                        $('#assignKpiForm')[0].reset();
                        $('#kpiSelect').prop('disabled', true);
                        loadUserKPIs(selectedUserId);
                    })
                    .fail(function(xhr) {
                        const response = xhr.responseJSON;
                        showAlert(response.message, 'danger');
                    })
                    .always(function() {
                        submitBtn.prop('disabled', false);
                  //      spinner.hide();
                    });
            });

            // Load user KPIs
            function loadUserKPIs(userId) {
                $('#userKpisContainer').html('<div class="text-center"><div class="spinner-border"></div></div>');

                $.get(`/user-kpis/user/${userId}`)
                    .done(function(userKpis) {
                        let html = '';

                        if (userKpis.length === 0) {
                            html = `
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-clipboard-list fa-2x mb-3"></i>
                                    <p>No KPIs assigned yet</p>
                                </div>
                            `;
                        } else {
                            userKpis.forEach(function(userKpi) {
                                const progress = userKpi.target_score > 0 ? (userKpi.current_score / userKpi.target_score) * 100 : 0;
                                const progressClass = progress >= 100 ? 'bg-success' : progress >= 50 ? 'bg-warning' : 'bg-danger';

                                html += `
                                    <div class="kpi-card card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">${userKpi.kpi.name}</h6>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item edit-kpi" href="#" data-id="${userKpi.id}" data-kpi-name="${userKpi.kpi.name}" data-target="${userKpi.target_score}" data-current="${userKpi.current_score}">
                                                            <i class="fa fa-edit me-2"></i>Tahrirlash
                                                        </a></li>
                                                        <li><a class="dropdown-item text-danger delete-kpi" href="#" data-id="${userKpi.id}">
                                                            <i class="fa fa-trash me-2"></i>Olib tashlash
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="row text-center mb-2">
                                                <div class="col-4">
                                                    <small class="text-muted">Erishgan ko'rsatkichi</small>
                                                    <div class="fw-bold">${userKpi.current_score}</div>
                                                </div>
                                                <div class="col-4">
                                                    <small class="text-muted">Max ko'rsatkich</small>
                                                    <div class="fw-bold">${userKpi.target_score || 'N/A'}</div>
                                                </div>
                                                <div class="col-4">
                                                    <small class="text-muted">Jarayon</small>
                                                    <div class="fw-bold">${Math.round(progress)}%</div>
                                                </div>
                                            </div>
                                            ${userKpi.target_score > 0 ? `
                                                <div class="progress progress-custom">
                                                    <div class="progress-bar ${progressClass}" style="width: ${Math.min(progress, 100)}%"></div>
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
                        $('#userKpisContainer').html('<div class="alert alert-danger">Foydalanuvchi KPI larini yuklashda xatolik yuz berdi</div>');
                    });
            }

            // Edit KPI
            $(document).on('click', '.edit-kpi', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const kpiName = $(this).data('kpi-name');
                const target = $(this).data('target');
                const current = $(this).data('current');

                $('#editKpiId').val(id);
                $('#editKpiName').val(kpiName);
                $('#editTargetScore').val(target);
                $('#editCurrentScore').val(current);

                editKpiModal.show();
            });

            // Save KPI changes
            $('#saveKpiChanges').click(function() {
                const id = $('#editKpiId').val();
                const data = {
                    target_score: $('#editTargetScore').val(),
                    current_score: $('#editCurrentScore').val()
                };

                $.ajax({
                    url: `/user-kpis/${id}`,
                    method: 'PUT',
                    data: data
                })
                    .done(function(response) {
                        showAlert(response.message, 'success');
                        editKpiModal.hide();
                        loadUserKPIs(selectedUserId);
                    })
                    .fail(function() {
                        sshowAlert('KPI ni yangilashda xatolik yuz berdi', 'danger');
                    });
            });

            // Delete KPI
            $(document).on('click', '.delete-kpi', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                if (confirm('Ushbu KPI ni o‘chirishga ishonchingiz komilmi?')) {
                    $.ajax({
                        url: `/user-kpis/${id}`,
                        method: 'DELETE'
                    })
                        .done(function(response) {
                            showAlert(response.message, 'success');
                            loadUserKPIs(selectedUserId);
                        })
                        .fail(function() {
                            showAlert('KPI ni o‘chirishda xatolik yuz berdi', 'danger');
                        });
                }
            });

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
