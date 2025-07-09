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
                    <i class="fe fe-bar-chart-2 mr-1"></i>&nbsp; {{ __('KPI Ko‘rsatkichlari') }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ __('KPI Ko‘rsatkichlari') }}</h3>
                        <a href="{{ route('kpis.create') }}" class="btn btn-success">
                            <i class="fa fa-plus-circle"></i>&nbsp; {{ __('Yangi KPI qo‘shish') }}
                        </a>
                    </div>

                    <div class="card-body">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">{{ $message }}</div>
                        @endif
                        @if ($error = Session::get('error'))
                            <div class="alert alert-danger">{{ $error }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mt-3">
                                <thead>
                                <tr>
                                    <th colspan="2">#</th>
                                    <th>{{ __('KPI nomi') }}</th>
                                    <th>{{ __('Maksimal ball') }}</th>
                                    <th>{{ __('Yaratilgan') }}</th>
                                    <th>{{ __('O\'zgartirilgan') }}</th>
                                    <th width="200">{{ __('Amallar') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($kpis as $category)
                                    <tr class="table-primary">
                                        <td style="text-align: center" colspan="2">{{ $loop->iteration }}</td>
                                        <td colspan="5">
                                            <strong>{{ $category->name }}</strong>
                                            <div class="float-end">
                                                <a href="{{ route('kpis.edit', $category->id) }}" class="btn btn-sm btn-outline-primary">
                                                    {{ __('Tahrirlash') }}
                                                </a>
                                                <form action="{{ route('kpis.destroy', $category->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ __('Haqiqatan ham o\'chirmoqchimisiz?') }}')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">{{ __('O\'chirish') }}</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    @foreach ($category->children as $item)
                                        <tr>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td style="text-align: center">{{ $loop->iteration }}</td>
                                            <td>— {{ $item->name }}</td>
                                            <td style="text-align: center">{{ $item->max_score }}</td>
                                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $item->updated_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('kpis.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                                    {{ __('Tahrirlash') }}
                                                </a>
                                                <form action="{{ route('kpis.destroy', $item->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ __('Haqiqatan ham o\'chirmoqchimisiz?') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        {{ __('O\'chirish') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                                {{ $kpis->links() }}
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection
