@extends('layouts.app')

@section('content')
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
                                    <a href="{{ route('works.create') }}">
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
                            <table class="table table-striped table-bordered nowrap display mt-3">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Nomi') }}</th>
                                    <th>{{ __('Yaratilgan') }}</th>
                                    <th>{{ __('O\'zgartirilgan') }}</th>
                                    <th width="220px">{{ __('Harakatlar') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($works as $work)
                                    <tr>
                                        <td>{{ $work->id }}</td>
                                        <td>{{ $work->name }}</td>
                                        <td>{{ $work->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $work->updated_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('works.edit', $work->id) }}" class="btn btn-sm btn-primary">
                                                {{ __('Tahrirlash') }}
                                            </a>

                                            <form action="{{ route('works.destroy', $work->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Ish joyini o\'chirishga ishonchingiz komilmi?') }}')">
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

                            <!-- Pagination -->
                            <div class="mt-3">
                                {!! $works->links() !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
