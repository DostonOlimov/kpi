@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- Page Header -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-user-check mr-1"></i>&nbsp; {{ __('Xodimning o\'zini o\'zi baholash bo\'limi') }}
                </li>
            </ol>
        </div>

        <!-- Flash Messages -->
        @if (session('fail'))
            <div class="alert alert-danger">{{ session('fail') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Form Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="mb-0">{{ __("Qilingan ishlarni baholash mezonlari formasi") }}</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data" multiple>
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Toifasi') }}</th>
                                        <th>{{ __('Nomi') }}</th>
                                        <th style="background-color: goldenrod;">{{ __('Ball miqdori') }}</th>
                                        <th>{{ __('Ishlar miqdori (dona)') }}</th>
                                        <th>{{ __('Asoslovchi hujjat') }}</th>
                                        <th style="background-color: limegreen;">{{ __('To\'plangan ball') }}</th>
                                        <th style="background-color: indianred;">{{ __('Maksimal ball') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $key => $item)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="id-{{ $key + 1 }}" value="{{ $item->id }}">
                                                {{ $key + 1 }}
                                            </td>
                                            <td style="white-space: normal;">{{ $types[$item->type] }}</td>
                                            <td style="white-space: normal;">{{ $item->name }}</td>
                                            <td style="background-color: goldenrod;">
                                                <input class="form-control" type="text" readonly value="{{ $item->ball }}">
                                            </td>
                                            <td>
                                                <input class="form-control" name="works_count-{{ $key + 1 }}" type="number">
                                            </td>
                                            <td>
                                                <input class="form-control" type="file" name="file-{{ $key + 1 }}">
                                                <small class="text-muted">Max 2 MB (.pdf, .doc, .docx, .jpg, .png)</small>
                                            </td>
                                            <td style="background-color: limegreen;">
                                                <input class="form-control" type="text" name="current_ball-{{ $key + 1 }}" readonly>
                                            </td>
                                            <td style="background-color: indianred;">
                                                <input class="form-control" type="text" readonly value="{{ $item->max_ball }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr style="background-color: lavender;">
                                        <td colspan="6" class="text-center font-weight-bold">{{ __('Jami:') }}</td>
                                        <td class="text-center font-weight-bold current_total">0</td>
                                        <td class="text-center font-weight-bold max_total">0</td>
                                    </tr>
                                    <input type="hidden" name="fields" value="{{ count($data) }}">
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-success">{{ __('Saqlash') }}</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
