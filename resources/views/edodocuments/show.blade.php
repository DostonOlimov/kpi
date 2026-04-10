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
                <li class="breadcrumb-item active">{{ $document->document_number }}</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Hujjat ma'lumotlari</h5>
                        <div>
                            @if(in_array($document->status, ['vaqtida_bajarilgan', 'muddati_o_tib_bajarilgan']))
                                <span class="btn btn-secondary" disabled>
                                    <i class="fa fa-lock"></i> Tahrirlash o'chirilgan
                                </span>
                            @else
                                <a href="{{ route('edodocuments.edit', $document->id) }}" class="btn btn-success">
                                    <i class="fa fa-edit"></i> Tahrirlash
                                </a>
                            @endif
                            <a href="{{ route('edodocuments.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Orqaga
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 200px;">Hujjat raqami</th>
                                        <td><strong>{{ $document->document_number }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Hujjat sanasi</th>
                                        <td>{{ $document->document_date->format('d.m.Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Hujjat turi</th>
                                        <td>{{ $document->document_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bajarish muddati</th>
                                        <td>
                                            <strong>{{ $document->due_date->format('d.m.Y') }}</strong>
                                            @if($document->isOverdue())
                                                <br><span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Muddati o'tgan</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 200px;">Yuboruvchi</th>
                                        <td>{{ $document->sender ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Yaratilgan sana</th>
                                        <td>{{ $document->task_created_at->format('d.m.Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Holat</th>
                                        <td>
                                            <span class="badge {{ $document->status_badge_class }}">
                                                {{ $document->status_display }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($document->completed_at)
                                    <tr>
                                        <th>Bajarilgan vaqt</th>
                                        <td>{{ $document->completed_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($document->summary)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6><strong>Qisqacha mazmuni:</strong></h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $document->summary }}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!in_array($document->status, ['vaqtida_bajarilgan', 'muddati_o_tib_bajarilgan']))
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <form action="{{ route('edodocuments.complete', $document->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-check"></i> Bajarildi deb belgilash
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
