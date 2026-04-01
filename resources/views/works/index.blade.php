@extends('layouts.app')

@section('content')
    <style>
        .custom-table {
            border-radius: 10px !important;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        }

        .custom-table thead {
            background: #f0f4ff;
        }

        .custom-table thead th {
            font-weight: 700;
            font-size: 15px;
            color: #2c3e50;
            padding: 14px;
            border-bottom: 2px solid #e2e6f3;
        }

        .custom-table tbody tr:hover {
            background: #f7faff !important;
            transition: 0.2s;
        }

        .table-action-btn {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
            margin-right: 4px;
        }
    </style>
    <div class="section">
        <!-- Page Header -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp; {{ __("Bo'limlar ro'yxati") }}
                </li>
            </ol>
        </div>

        <!-- Page Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list mb-0">
                                <li class="active">
                                    <a href="#">
                                        <i class="fa fa-list fa-lg"></i>&nbsp; {{ __("Ro'yxat") }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('works.create', ['parent_id' => $id]) }}">
                                        <i class="fa fa-plus-circle fa-lg"></i>&nbsp; <strong>{{ __("Qo'shish") }}</strong>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Flash Messages -->
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">{{ $message }}</div>
                        @elseif($message = Session::get('error'))
                            <div class="alert alert-danger">{{ $message }}</div>
                        @endif

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered nowrap display mt-3 custom-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Nomi') }}</th>
                                        <th>{{ __('Harakatlar') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($works as $work)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $work->name }}</td>

                                            <td>

                                                <!-- Users Button -->
                                                <a href="{{ route('employees.list', $work->id) }}"
                                                    class="btn btn-info btn-sm table-action-btn">
                                                    <i class="fe fe-users"></i> {{ __('Xodimlar') }}
                                                </a>

                                                <!-- Edit -->
                                                <a href="{{ route('works.edit', $work->id) }}"
                                                    class="btn btn-primary btn-sm table-action-btn">
                                                    <i class="fe fe-edit"></i> {{ __('Tahrirlash') }}
                                                </a>

                                                <!-- Delete -->
                                                <form action="{{ route('works.destroy', $work->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('{{ __('Ish joyini o\'chirishga ishonchingiz komilmi?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm table-action-btn">
                                                        <i class="fe fe-trash"></i> {{ __('O\'chirish') }}
                                                    </button>
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
