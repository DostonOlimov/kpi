@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-file-text mr-1"></i> EDO Hujjatlar
                </li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li class="active">
                                        <a href="#">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i>
                                            {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('edodocuments.create') }}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
                                                {{ trans('app.Qo\'shish')}}</b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                <!-- Filters -->
                <div class="filters-wrapper bg-light p-3 rounded">
                    <form action="{{ route('edodocuments.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Qidirish..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">Barchasi</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Jarayonda</option>
                                    <option value="vaqtida_bajarilgan" {{ request('status') == 'vaqtida_bajarilgan' ? 'selected' : '' }}>Vaqtida bajarilgan</option>
                                    <option value="muddati_o_tib_bajarilgan" {{ request('status') == 'muddati_o_tib_bajarilgan' ? 'selected' : '' }}>Muddati o'tib</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ request('date_from') }}" placeholder="Dan">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ request('date_to') }}" placeholder="Gacha">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Qidirish
                                </button>
                                <a href="{{ route('edodocuments.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Tozalash
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> {{ session('message') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-nowrap">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Hujjat raqami</th>
                                <th>Hujjat sanasi</th>
                                <th>Hujjat turi</th>
                                <th>Yuboruvchi</th>
                                <th>Bajarish muddati</th>
                                <th>Holat</th>
                                <th>Harakatlar</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($documents as $index => $document)
                                <tr class="text-center align-middle">
                                    <td>{{ $documents->firstItem() + $index }}</td>
                                    <td><strong>{{ $document->document_number }}</strong></td>
                                    <td>{{ $document->document_date->format('d.m.Y') }}</td>
                                    <td>{{ $document->document_type }}</td>
                                    <td>{{ $document->sender ?? '-' }}</td>
                                    <td>
                                        <strong>{{ $document->due_date->format('d.m.Y') }}</strong>
                                        @if($document->isOverdue())
                                            <br><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> Muddati o'tgan</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $document->status_badge_class }}">
                                            {{ $document->status_display }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('edodocuments.show', $document->id) }}" 
                                               class="btn btn-info" title="Ko'rish">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @if(in_array($document->status, ['vaqtida_bajarilgan', 'muddati_o_tib_bajarilgan']))
                                                <button type="button" class="btn btn-secondary" disabled title="Tasdiqlangan - tahrirlash mumkin emas">
                                                    <i class="fa fa-lock"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('edodocuments.edit', $document->id) }}" 
                                                   class="btn btn-success" title="Tahrirlash">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif
                                            @if(!in_array($document->status, ['vaqtida_bajarilgan', 'muddati_o_tib_bajarilgan']))
                                                <button type="button" class="btn btn-primary complete-btn" 
                                                        data-id="{{ $document->id }}" title="Bajarildi">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            @endif
                                            @if(in_array($document->status, ['vaqtida_bajarilgan', 'muddati_o_tib_bajarilgan']))
                                                <button type="button" class="btn btn-secondary" disabled title="Tasdiqlangan - o'chirish mumkin emas">
                                                    <i class="fa fa-lock"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-danger delete-btn" 
                                                        data-id="{{ $document->id }}" title="O'chirish">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fe fe-folder fa-2x mb-2"></i>
                                        <p class="mb-0">Hujjatlar topilmadi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $documents->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="completeForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Hujjatni bajarish</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Rostdan ham bu hujjatni bajarilgan deb belgilamoqchimisiz?</p>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            Agar joriy vaqt bajarish muddatidan o'tgan bo'lsa, 
                            <strong>"Muddati o'tib bajarilgan"</strong> deb, aks holda 
                            <strong>"Vaqtida bajarilgan"</strong> deb saqlanadi.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-success">Bajarildi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Complete button handler
        document.querySelectorAll('.complete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById('completeForm').action = '/edodocuments/' + id + '/complete';
                $('#completeModal').modal('show');
            });
        });

        // Delete button handler
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                if (confirm('Rostdan ham bu hujjatni o\'chirmoqchimisiz?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/edodocuments/' + id;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
