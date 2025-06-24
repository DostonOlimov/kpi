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
    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Baholash ko'rsatkichlarini qo'shish</span></h4>
                </div>
            </div>
        </div>
        @if (session()->has('fail'))
            <div class="alert alert-danger">
                {{ session()->get('fail') }}
            </div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body" style="background-color: mintcream;">
                        <h4 class="text-center">Birinchi bo'lim uchun ko'rsatkichlar</h4>
                        @if(count($data) < 5 )
                            <div class="alert alert-danger">
                                Kamida 5 ta ko'rsatkich kiriting!
                            </div>
                        @endif
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> № </th>
                                <th> Ko'rsatkich nomi </th>
                                <th> Maqsadli ko'rsatkich </th>
                                <th> Vazni </th>
                                <th> Hisobot oyi </th>
                                <th> Harakat </th>
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
                                        <a class="btn btn-danger" href="{{ route('director.delete', [ $item->id]) }}">O'chirish</a>
                                    </td>
                                </tr>
                            @endforeach
                            @php
                                $weg = [];
                                foreach ($data as $item){
                                    array_push($weg, $item->weight);
                                }
                                $max_w = array_sum($weg);
                            @endphp
                            @if(count($data) == 0 )
                                <tr>
                                    <td colspan="6" class="text-center">Ma'lumot yo'q</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <hr class="m-0">
                    <div class="card-body" style="background-color: whitesmoke;">
                        <h4 class="text-center">Baholash ko'rsatkichi ma'lumotlari</h4>
                        <form class="forms-sample" action="#">
                            @csrf
                            <div class="row">
                                <div class="form-group col-9">
                                    <label for="">Ko'rsatkich nomi</label>
                                    <textarea name="name" class="form-control" id="name" rows="5"
                                              style="font-size: 16px; font-weight: bold; line-height: 18px;"></textarea>
                                </div>
                                <div class="col-3 d-flex flex-column justify-content-center">
                                    <div class="form-group">
                                        <label for="">Maqsadli ko'rsatkich</label>
                                        <input name="works_count" type="number" class="form-control" id="works">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Vazni</label>
                                        <input name="weight" type="number" class="form-control" id="weight">
                                        <input name="month_id" type="hidden" id = "month_id" value = {{ $month_id }}>
                                        <input name="year" type="hidden" id = "year" value = {{ $year }}>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-info">Qo'shish</button>
                                </div>
                                <div class="col-2 d-flex justify-content-center">
                                    <button type="button"
                                            @if(count($data) < 5 || $max_w != 60) disabled @endif
                                            class="btn btn-success">Saqlash
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
