@extends('layouts.app')
<style>
        .forms-sample input{
            font-size: 16px;
            font-weight: bold;
        }
        .forms-sample select{
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-calendar mr-1"></i>&nbsp; {{ __('Oy ish kunlari') }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ __('Oy ish kunlari') }}</h3>

                    </div>

                    <div class="card-body">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @elseif($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mt-3">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Oy nomi') }}</th>
                                    <th>{{ __('Ish kuni') }}</th>
                                    <th width="200">{{ __('Amallar') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ App\Models\Month::getMonth($role->month_id) }}</td>
                                        <td>{{ $role->days }}</td>
                                        <td>
                                            <a href="{{ route('month.edit', $role->id) }}" class="btn btn-sm btn-primary">
                                                {{ __('Tahrirlash') }}
                                            </a>
                                            <form action="{{ route('month.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Haqiqatan ham o\'chirmoqchimisiz?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    {{ __('O\'chirish') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
