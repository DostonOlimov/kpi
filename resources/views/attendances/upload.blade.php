@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('attendances.index') }}">
                        <i class="fe fe-users mr-1"></i> Davomat ro'yxati
                    </a>
                </li>
                <li class="breadcrumb-item active">Excel yuklash</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li>
                                        <a href="{{ route('attendances.index', ['date' => $date]) }}">
                                            <i class="fa fa-list fa-lg">&nbsp;</i> {{ __("Ro'yxat") }}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <i class="fa fa-upload fa-lg">&nbsp;</i>
                                        <b>{{ __('Excel yuklash') }}</b>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if(session('status'))
                            <div class="alert alert-success mb-1 mt-1">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger mb-1 mt-1">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('attendances.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <!-- Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Sana:</label>
                                        <input type="date" name="date" class="form-control" 
                                               value="{{ $date }}" max="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <!-- Type -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Davomat turi:</label>
                                        <select name="type" class="form-control" required>
                                            <option value="kirish" {{ $type == 'kirish' ? 'selected' : '' }}>Kirish (Kelish)</option>
                                            <option value="chiqish" {{ $type == 'chiqish' ? 'selected' : '' }}>Chiqish (Ketish)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Excel File -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Excel fayl:</label>
                                        <input type="file" name="file" class="form-control" 
                                               accept=".xlsx,.xls,.csv" required>
                                        <small class="text-muted">
                                            Fayl formati: .xlsx, .xls, .csv (Max: 2MB)
                                        </small>
                                    </div>
                                </div>

                                <!-- Info Box -->
                                <div class="col-md-12">
                                    <div class="alert alert-info" role="alert">
                                        <h6 class="alert-heading">Excel fayl formati:</h6>
                                        <hr>
                                        <p class="mb-0">Excel faylda quyidagi ustunlar bo'lishi kerak:</p>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>A ustun:</strong> External ID (Tuniket ID / ch_id)</li>
                                            <li><strong>B ustun:</strong> Ism</li>
                                            <li><strong>C ustun:</strong> Bo'lim (ixtiyoriy)</li>
                                            <li><strong>D ustun:</strong> Vaqt (HH:MM formatida, masalan: 08:00, 17:30)</li>
                                            <li><strong>E ustun:</strong> Holat (ixtiyoriy, masalan: kech qoldi, ta'tilda)</li>
                                            <li><strong>F ustun:</strong> Izoh (ixtiyoriy)</li>
                                        </ul>
                                        <hr>
                                        <p class="mb-0 mt-2">
                                            <strong>Eslatma:</strong> Birinchi qator sarlavha sifatida qabul qilinadi va o'tkazib yuboriladi.
                                        </p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="form-group col-md-12 text-center">
                                    <a class="btn btn-secondary" href="{{ route('attendances.index', ['date' => $date]) }}">
                                        <i class="fa fa-arrow-left"></i> Bekor qilish
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-upload"></i> Yuklash
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
