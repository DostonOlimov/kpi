@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- Page Header -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-clipboard mr-1"></i>&nbsp;{{ __('Taqsimot uchun kiritilgan mablag\'lar jadvali') }}
                </li>
            </ol>
        </div>

        <!-- Table Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="tab_wrapper page-tab">
                        <ul class="tab_list mb-0">
                            <li class="active">
                                <a href="#">
                                    <i class="fa fa-list fa-lg"></i>&nbsp; {{ __("Ro'yxat") }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bugalter.add') }}">
                                    <i class="fa fa-plus-circle fa-lg"></i>&nbsp; <strong>{{ __("Qo'shish") }}</strong>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            <!-- Card Body -->
                <div class="card-body ">
                    <!-- Flash Messages -->
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3 rounded" role="alert">
                            <i class="fa fa-check-circle"></i> {{ Session::get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @elseif (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3 rounded" role="alert">
                            <i class="fa fa-exclamation-triangle"></i> {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                            <!-- Responsive Table -->
                    <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-muted">#</th>
                                            <th class="text-muted">{{ __('Hisobot oyi') }}</th>
                                            <th class="text-muted">{{ __('Summa') }}</th>
                                            <th class="text-muted">{{ __('Holati') }}</th>
                                            <th class="text-muted">{{ __('Harakat') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data as $key => $item)
                                            <tr class="border-bottom">
                                                <td><strong>{{ $key + 1 }}</strong></td>
                                        
                                                <td><span class="badge bg-secondary">{{ get_month_name($item->month) }}</span></td>
                                                  <td class="fw-bold text-success">{{ number_format($item->summa, 0, ',', '.') }} so'm</td>
                                                <td>
                                                    <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $item->status === 'active' ? __('Taqsimlangan') : __('Taqsimlanmagan') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($item->status !== 'active')
                                                        <a href="{{ route('bugalter.edit', $item->id) }}" 
                                                        class="btn btn-sm btn-outline-primary me-1" 
                                                        title="{{ __('O\'zgartirish') }}">
                                                            <i class="fa fa-edit"></i>{{ __('O\'zgartirish') }}
                                                        </a>
                                                        <a href="{{ route('bugalter.distribution', $item->id) }}" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        title="{{ __('Taqsimlashga berish') }}">
                                                            <i class="fa fa-share"></i>{{ __('Taqsimlashga berish') }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted fst-italic">{{ __('Jarayon yakunlangan') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fa fa-folder-open fa-lg"></i><br>
                                                    <small>{{ __('Ma\'lumot topilmadi') }}</small>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

