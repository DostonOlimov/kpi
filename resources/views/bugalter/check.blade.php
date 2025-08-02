@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- Page Header -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-clipboard mr-1"></i>&nbsp; {{ __('Kiritilgan summalar holati') }}
                </li>
            </ol>
        </div>

        <!-- Table Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="mb-0">{{ __('Bugalteriyadan kiritilgan summalar jadvali') }}</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Summa') }}</th>
                                    <th>{{ __('Hisobot oyi') }}</th>
                                    <th>{{ __('Holati') }}</th>
                                    <th>{{ __('Harakat') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ number_format($item->summa, 0, ',', '.') }}</td>
                                        <td>{{ $month[$item->month - 1] }}</td>
                                        <td>
                                            {{ $item->status === 'active' ? __('Taqsimlangan') : __('Taqsimlanmagan') }}
                                        </td>
                                        <td>
                                            @if($item->status !== 'active')
                                                <a href="{{ route('bugalter.edit', [$item->id]) }}" class="btn btn-sm btn-primary">
                                                    {{ __("O'zgartirish") }}
                                                </a>
                                                <a href="{{ route('bugalter.distribution', [$item->id]) }}" class="btn btn-sm btn-info">
                                                    {{ __('Taqsimlashga berish') }}
                                                </a>
                                            @endif
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

