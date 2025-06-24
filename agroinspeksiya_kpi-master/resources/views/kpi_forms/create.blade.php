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
                    <div class="card-body" style="background-color: honeydew;">
                        <h4 class="text-center">Birinchi bo'lim uchun bo'limda mavjud bandlar</h4>
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> № </th>
                                <th> Band nomi </th>
                                <th> Maqsadli ko'rsatkich </th>
                                <th> Qoldiq maqsadli ko'rsatkich </th>
                                <th> Vazni </th>
                                <th> Hisobot oyi </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key => $item)
                                <tr>
                                    <td>1.{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->works_count - $item->taken_works }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ \App\Models\Month::getMonth($item->month)  }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr class="m-0">
                    <div class="card-body" style="background-color: mintcream;">
                        <h4 class="text-center">Birinchi bo'lim uchun xodimning ko'rsatkichlar</h4>
                        @if(count($data1) < 5 )
                            <div class="alert alert-danger">
                                Saqlash uchun kamida 5 ta ko'rsatkich kiriting!
                            </div>
                        @endif
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> № </th>
                                <th> Band raqami </th>
                                <th> Ko'rsatkich nomi </th>
                                <th> Maqsadli ko'rsatkich </th>

                                <th> Vazni </th>
                                <th> Hisobot oyi </th>
                                <th> Harakat </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data1 as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>1.{{ $item->band_id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{  \App\Models\Month::getMonth($item->month) }}</td>
                                    <td>
                                        <a class="btn btn-danger" href="{{ route('profile.delete', [ $item->id]) }}">O'chirish</a>
                                    </td>
                                </tr>
                            @endforeach
                            @php
                                $weg = [];
                                foreach ($data1 as $item){
                                    array_push($weg, $item->weight);
                                }
                                $max_w = array_sum($weg);
                                $curent_works = "";
                                foreach ($data as $item){
                                    $curent_works .= '&'.strval($item->works_count-$item->taken_works);
                                }
                             //   var_dump($curent_works);die();
                            @endphp
                            @if(count($data1) == 0 )
                                <tr>
                                    <td colspan="7" class="text-center">Ma'lumot yo'q</td>
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
                                <div class="form-group col-6">
                                    <label for="">Ko'rsatkich nomi</label>
                                    <textarea name="name" class="form-control" id="name" rows="5"
                                              style="font-size: 16px; font-weight: bold; line-height: 18px;"></textarea>
                                </div>
                                <div class="col-3 d-flex flex-column">
                                    <div class="form-group">
                                        <label for="">Vazni</label>
                                        <input name="weight" type="number" class="form-control" id="weight">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Bandni tanlang</label>
                                        <select name="band" id="band" class="form-control">
                                            @foreach($data as $key => $item)
                                                <option value="{{$item->band_id.'&'.$item->id}}">1.{{ $item->band_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3 d-flex flex-column">
                                    <div class="form-group">
                                        <label for="">Maqsadli ko'rsatkich</label>
                                        <input name="works_count" type="number" class="form-control" id="works">
                                        <input name="month_id" type="hidden" id="month_id" value = {{ $month }}>
                                        <input name="year" type="hidden" id="year" value = {{ $year }}>
                                    </div>
                                    <input type="hidden" id= "curent" value={{ $curent_works }}>
                                    <div class="form-group">
                                        <label class="alert alert-danger">Maqsadli ko'rsatkichlar soni tanlangan bandning qoldiq maqsadli ko'rsatkich sonidan oshmasligi kerak!</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-info">Qo'shish</button>
                                </div>
                                <div class="col-2 d-flex justify-content-center">
                                    <button type="button"
                                            @if(count($data1) < 5 || $max_w != 60) disabled @endif
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
        $(document).ready(function () {
            $('.btn-info').click(function () {
                if($('#name').val().length > 0 && $('#works').val().length > 0 && $('#weight').val().length > 0){


                 let id = $('#band').val().split('&')[0];
                 let arr = $('#curent').val().split('&');


                 if( arr[id] - $('#works').val() >= 0)
                 {
                    if($('#weight').val() <= (60 - {{ $max_w }})){

                        var formData = {
                            _token: '{{ csrf_token() }}',
                            name: $('#name').val(),
                            works: $('#works').val(),
                            weight: $('#weight').val(),
                            band: $('#band').val(),
                            month_id : $('#month_id').val(),
                            year : $('#year').val(),
                        }
                        $.ajax({
                            type: 'post',
                            url: "{{ route('profile.save') }}",
                            data: formData,
                            dataType: 'json',
                            enctype: true
                        })

                        setTimeout(() => {
                            location.reload()
                        }, 500)
                    }
                    else {
                        alert('Ko`rsatkichlar vaznlari jamisi 60 dan oshmasligi kerak!')
                    }
                }else{
                    alert('Maqsadli ko\'rsatkichlar soni tanlangan bandning qoldiq maqsadli ko\'rsatkich sonidan oshmasligi kerak!!')
                }
            }
                else {
                    alert('Maydonlar to`liq to`ldirilmagan!')
                }
            });
            $('.btn-success').click(function () {
                var formData = {
                    _token: '{{ csrf_token() }}',
                    month_id : $('#month_id').val(),
                    year : $('#year').val(),
                }
                $.ajax({
                    type: 'post',
                    url: "{{ route('profile.commit') }}",
                    data: formData,
                    dataType: 'json',
                    enctype: true
                })
                setTimeout(() => {
                    location.href = "{{ route('profile.list') }}"
                }, 500)
            });
        })
    </script>
@endpush
