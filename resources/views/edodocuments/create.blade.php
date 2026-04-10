@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('edodocuments.index') }}">
                        <i class="fe fe-file-text mr-1"></i> EDO Hujjatlar
                    </a>
                </li>
                <li class="breadcrumb-item active">Yangi hujjat qo'shish</li>
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
                                        <a href="{{ route('edodocuments.index') }}">
                                            <i class="fa fa-list fa-lg">&nbsp;</i> Ro'yxat
                                        </a>
                                    </li>
                                    <li class="active">
                                        <i class="fa fa-plus-circle fa-lg">&nbsp;</i>
                                        <b>Qo'shish</b>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('edodocuments.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Hujjat raqami: <span class="text-danger">*</span></label>
                                        <input type="text" name="document_number" class="form-control" 
                                               value="{{ old('document_number') }}" required 
                                               placeholder="Masalan: 4b/184">
                                        @error('document_number')
                                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Hujjat sanasi: <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control dob" placeholder="<?php echo getDatepicker();?>" name="document_date" value="{{ old('document_date') }}" onkeypress="return false;" required />
                                        </div>
                                        @error('document_date')
                                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Hujjat turi: <span class="text-danger">*</span></label>
                                        <select name="document_type" class="form-control" required>
                                            <option value="">Tanlang...</option>
                                            <option value="Kiruvchi hujjat" {{ old('document_type') == 'Kiruvchi hujjat' ? 'selected' : '' }}>Kiruvchi hujjat</option>
                                            <option value="Chiquvchi hujjat" {{ old('document_type') == 'Chiquvchi hujjat' ? 'selected' : '' }}>Chiquvchi hujjat</option>
                                            <option value="Ichki hujjat" {{ old('document_type') == 'Ichki hujjat' ? 'selected' : '' }}>Ichki hujjat</option>
                                        </select>
                                        @error('document_type')
                                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Bajarish muddati: <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control dob" placeholder="<?php echo getDatepicker();?>" name="due_date" value="{{ old('due_date') }}" onkeypress="return false;" required />
                                        </div>
                                        @error('due_date')
                                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Yuboruvchi:</label>
                                        <input type="text" name="sender" class="form-control" 
                                               value="{{ old('sender') }}" placeholder="Tashkilot nomi">
                                        @error('sender')
                                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Topshiriq yaratilgan sana: <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control dob" placeholder="<?php echo getDatepicker();?>" name="task_created_at" value="{{ old('task_created_at', date('d.m.Y')) }}" onkeypress="return false;" required />
                                        </div>
                                        @error('task_created_at')
                                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Qisqacha mazmuni:</label>
                                        <textarea name="summary" class="form-control" rows="4" 
                                                  placeholder="Hujjat mazmuni haqida qisqacha...">{{ old('summary') }}</textarea>
                                        @error('summary')
                                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group col-md-12 text-center">
                                    <a class="btn btn-secondary" href="{{ route('edodocuments.index') }}">
                                        <i class="fa fa-times"></i> Bekor qilish
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> Saqlash
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
@section('scripts')
    <script src="{{ asset('js/my_js_files/date.js') }}"></script>

@endsection
