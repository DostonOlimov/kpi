@extends('layouts.app')

@section('content')
    <style>
        .forms-sample input {
            font-size: 16px;
            font-weight: bold;
            line-height: 20px;
        }

        .forms-sample select {
            font-size: 16px;
            font-weight: bold;
            line-height: 20px;
        }

        table th {
            height: auto;
            white-space: normal !important;
            border: 1px solid grey !important;
        }

        table td {
            height: auto;
            white-space: normal !important;
            border: 1px solid grey !important;
        }
    </style>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-layers mr-1"></i>&nbsp; {{ __("Baholash ko'rsatkichlarini qo'shish") }}
                </li>
            </ol>
        </div>

        @if (session()->has('fail'))
            <div class="alert alert-danger">{{ session()->get('fail') }}</div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session()->get('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header bg-mintcream">
                        <h4 class="text-center mb-0">{{ __("Birinchi bo'lim uchun ko'rsatkichlar") }}</h4>
                    </div>

                    <div class="card-body bg-mintcream">
                        @if(count($data) < 5)
                            <div class="alert alert-danger">
                                {{ __("Kamida 5 ta ko'rsatkich kiriting!") }}
                            </div>
                        @endif

                        <table class="table table-bordered table-responsive mb-0">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>Ko'rsatkich nomi</th>
                                <th>Maqsadli ko'rsatkich</th>
                                <th>Vazni</th>
                                <th>Hisobot oyi</th>
                                <th>Harakat</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key => $item)
                                <tr>
                                    <td>1.{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $month_name }}</td>
                                    <td>
                                        <a class="btn btn-danger btn-sm" href="{{ route('director.delete', [$item->id]) }}">
                                            {{ __("O'chirish") }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            @php
                                $weg = array_column($data->toArray(), 'weight');
                                $max_w = array_sum($weg);
                            @endphp

                            @if(count($data) === 0)
                                <tr>
                                    <td colspan="6" class="text-center">{{ __("Ma'lumot yo'q") }}</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body bg-light">
                        <h4 class="text-center mb-4">{{ __("Baholash ko'rsatkichi ma'lumotlari") }}</h4>

                        <form class="forms-sample" action="#">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-9">
                                    <label for="name"><strong>{{ __("Ko'rsatkich nomi") }}</strong></label>
                                    <textarea name="name" class="form-control" id="name" rows="5"
                                              style="font-size: 16px; font-weight: bold; line-height: 18px;"></textarea>
                                </div>
                                <div class="col-md-3 d-flex flex-column justify-content-center">
                                    <div class="form-group">
                                        <label for="works"><strong>{{ __("Maqsadli ko'rsatkich") }}</strong></label>
                                        <input name="works_count" type="number" class="form-control" id="works">
                                    </div>
                                    <div class="form-group">
                                        <label for="weight"><strong>{{ __("Vazni") }}</strong></label>
                                        <input name="weight" type="number" class="form-control" id="weight">
                                        <input name="month_id" type="hidden" value="{{ $month_id }}">
                                        <input name="year" type="hidden" value="{{ $year }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mt-3">
                                <div class="col-md-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-info">{{ __("Qo'shish") }}</button>
                                </div>
                                <div class="col-md-2 d-flex justify-content-center">
                                    <button type="button"
                                            class="btn btn-success"
                                            @if(count($data) < 5 || $max_w != 60) disabled @endif>
                                        {{ __("Saqlash") }}
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
@push('scripts')
    <script>
        var id = $('#id').val();
        $(document).ready(function () {
            $('.btn-info').click(function () {
                if($('#name').val().length > 0 && $('#works').val().length > 0 && $('#weight').val().length > 0){
                    if($('#weight').val() <= (60 - {{ $max_w }}))
                    {
                        var formData = {
                            _token: '{{ csrf_token() }}',
                            name: $('#name').val(),
                            works: $('#works').val(),
                            weight: $('#weight').val(),
                            month_id: $('#month_id').val(),
                            year: $('#year').val(),
                            id:  $('#id').val(),
                        }
                        $.ajax({
                            type: 'post',
                            url: "{{ route('director.store') }}",
                            data: formData,
                            dataType: 'json',
                            enctype: true
                        })
                        setTimeout(() => {
                            location.reload()
                        }, 500)
                    }
                    else {
                        alert('Ko`rsatkichlar vaznlari jamisi 60 dan oshmasligi kerak!');
                    }
                }
                else {
                    alert('Maydonlar to`liq to`ldirilmagan!');
                }
            });
            $('.btn-success').click(function () {
                var formData = {
                    _token: '{{ csrf_token() }}',
                    month_id: $('#month_id').val(),
                    year: $('#year').val(),
                }
                $.ajax({
                    type: 'post',
                    url: "{{ route('director.commit') }}",
                    data: formData,
                    dataType: 'json',
                    enctype: true
                })
                setTimeout(() => {
                    location.href = "{{ route('director.list') }}"
                }, 500)
            });
        })
    </script>
@endpush
