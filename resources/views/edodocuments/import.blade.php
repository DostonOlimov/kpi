@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('edodocuments.index') }}">
                        <i class="fe fe-file-text mr-1"></i> EDO Hujjatlar
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fa fa-file-excel-o mr-1"></i> Import
                </li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fa fa-file-excel-o mr-2"></i> Exceldan import qilish
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Import Status -->
                <div id="importStatus" class="d-none">
                    <div id="statusProcessing" class="alert alert-info d-none">
                        <i class="fa fa-spinner fa-spin mr-2"></i>
                        <strong>Import jarayonda...</strong> Iltimos kuting.
                    </div>
                    <div id="statusCompleted" class="alert alert-success d-none">
                        <i class="fa fa-check-circle mr-2"></i>
                        <strong>Import tugadi!</strong>
                        <span id="statusCount"></span>
                    </div>
                    <div id="statusFailed" class="alert alert-danger d-none">
                        <i class="fa fa-times-circle mr-2"></i>
                        <strong>Import xatoligi:</strong> <span id="statusError"></span>
                    </div>
                    <div id="statusErrorsList" class="alert alert-warning d-none">
                        <i class="fa fa-exclamation-triangle mr-1"></i> <strong>Xatoliklar:</strong>
                        <ul id="errorsList" class="mb-0 mt-1"></ul>
                    </div>
                </div>

                <!-- Template info -->
                <div id="importFormSection">
                    <div class="alert alert-info">
                        <strong>Shablon</strong> — EDO hujjatlarini import qilish uchun.
                        Quyidagi ustunlar tartibida Excel fayl tayyorlang.
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr class="text-center small">
                                    <th>Кириш рақами</th>
                                    <th>Кирувчи сана</th>
                                    <th>Шошилнинг</th>
                                    <th>ХДФУ/ДСП</th>
                                    <th>Бажариш муддати</th>
                                    <th>Ўтиб кетган кунлар</th>
                                    <th>Топшириқ ҳолати</th>
                                    <th>Асосий ижрочи</th>
                                    <th>Жавоб берилган вакт</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="small text-center">
                                    <td>XDFU/102</td>
                                    <td>31.03.2026</td>
                                    <td>Йўқ</td>
                                    <td>Ха</td>
                                    <td>17.04.2026</td>
                                    <td>15</td>
                                    <td>Бажарилмаган</td>
                                    <td>D.R.FARMONOV</td>
                                    <td>17.04.2026 16:02</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ asset('templates/edo_shablon_1.xlsx') }}" class="btn btn-outline-primary btn-sm mb-4" download>
                        <i class="fa fa-download mr-1"></i> Shablonni yuklab olish
                    </a>

                    <hr>

                    <!-- Upload form -->
                    <form action="{{ route('edodocuments.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div class="form-group">
                            <label for="import_file"><strong>Excel fayl</strong></label>
                            <div class="custom-file">
                                <input type="file" name="file" id="import_file" class="custom-file-input @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="import_file">Fayl tanlang...</label>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Qabul qilinadigan formatlar: .xlsx, .xls, .csv</small>
                        </div>

                        <div class="d-flex align-items-center" style="gap:12px;">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fa fa-upload mr-1"></i> Import qilish
                            </button>
                            <a href="{{ route('edodocuments.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left mr-1"></i> Orqaga
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const STATUS_URL = '{{ route("edodocuments.import_status") }}';
    const CACHE_KEY  = '{{ session("import_cache_key", "") }}';

    document.getElementById('import_file').addEventListener('change', function() {
        const label = this.nextElementSibling;
        label.textContent = this.files[0] ? this.files[0].name : 'Fayl tanlang...';
    });

    function showStatusPanel() {
        document.getElementById('importStatus').classList.remove('d-none');
        document.getElementById('importFormSection').classList.add('d-none');
    }

    function setStatus(state) {
        document.getElementById('statusProcessing').classList.toggle('d-none', state !== 'processing');
        document.getElementById('statusCompleted').classList.toggle('d-none', state !== 'completed');
        document.getElementById('statusFailed').classList.toggle('d-none', state !== 'failed');
    }

    function pollStatus(key) {
        fetch(STATUS_URL + '?key=' + encodeURIComponent(key))
            .then(r => r.json())
            .then(data => {
                if (data.status === 'queued' || data.status === 'processing') {
                    setStatus('processing');
                    setTimeout(() => pollStatus(key), 2000);
                } else if (data.status === 'completed') {
                    setStatus('completed');
                    document.getElementById('statusCount').textContent =
                        data.count + ' ta hujjat import qilindi.';
                    if (data.errors && data.errors.length > 0) {
                        document.getElementById('statusErrorsList').classList.remove('d-none');
                        const ul = document.getElementById('errorsList');
                        data.errors.forEach(e => {
                            const li = document.createElement('li');
                            li.textContent = e;
                            ul.appendChild(li);
                        });
                    }
                } else if (data.status === 'failed') {
                    setStatus('failed');
                    document.getElementById('statusError').textContent = data.message;
                }
            })
            .catch(() => setTimeout(() => pollStatus(key), 3000));
    }

    // Auto-poll if redirected with a cache key
    if (CACHE_KEY) {
        showStatusPanel();
        setStatus('processing');
        pollStatus(CACHE_KEY);
    }

    // On form submit, let it POST normally — controller will redirect back with cache key
</script>
@endsection
