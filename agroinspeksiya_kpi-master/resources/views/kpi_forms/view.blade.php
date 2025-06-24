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
    </style>
    <div class="content-wrapper">
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Baholash me'zonlarini qo'shish</span></h4>
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
                    <div class="card-body">
                        <table class="table table-bordered table-responsive mb-2">
                            <thead>
                            <tr>
                                <th> №</th>
                                <th> Me'zon nomi</th>
                                <th> Qilinadigan ishlar(dona)</th>
                                <th> Maxsimal ball</th>
                                <th> Hisobot oyi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($types as $key => $item)
                                <tr>
                                    <td>1.{{ $key + 1 }}</td>
                                    <td style="height: auto; white-space: normal!important;">
                                        {{ $item->name }}
                                    </td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->month }}</td>
                                </tr>
                            @endforeach
                            @if(count($types) == 0 )
                                <tr>
                                    <td colspan="7" class="text-center">Ma'lumot yo'q</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        @if(count($data) < 5 )
                            <div class="alert alert-danger">
                                Kamida 5 ta me'zon kiriting!
                            </div>
                        @endif
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> №</th>
                                <th> Me'zon nomi</th>
                                <th> Kpi nomi</th>
                                <th> Qilinadigan ishlar(dona)</th>
                                <th> Maxsimal ball</th>
                                <th> Hisobot oyi</th>
                                <th> Harakat</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td style="height: auto; white-space: normal!important;">{{ '' }}</td>
                                    <td style="height: auto; white-space: normal!important;">
                                        {{ $item->name }}
                                    </td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->month }}</td>
                                    <td>
                                        <a class="btn btn-danger" href="{{ route('director.delete', [ $item->id]) }}">O'chirish</a>
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($data) == 0 )
                                <tr>
                                    <td colspan="7" class="text-center">Ma'lumot yo'q</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <h4>Baholash me'zoni ma'lumotlari</h4>
                        <form class="forms-sample" action="#">
                            @csrf
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="">Kpi nomi</label>
                                    <textarea name="name" class="form-control" id="name" rows="6"
                                              style="font-size: 16px; font-weight: bold; line-height: 18px;"></textarea>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex">
                                        <div class="form-group">
                                            <label for="">Qilinadigan ishlar soni (dona)</label>
                                            <input name="works_count" type="number" class="form-control" id="works">
                                        </div>
                                        <div class="form-group ml-3">
                                            <label for="">Olinishi mumkin bo'lgan ball</label>
                                            <input name="max_ball" type="number" class="form-control" id="balls">
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="form-group">
                                            <label for="">Hisobot oyi</label>
                                            <select name="month" id="month" class="form-control">
                                                @foreach($month as $m)
                                                    <option value="{{$m}}">{{$m}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group ml-3">
                                            <label for="">Kpi me'zoni</label>
                                            <select name="work_type" id="type" class="form-control">
                                                @foreach($types as $key => $t)
                                                    <option value="{{$t->id}}">1.{{$key + 1}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-info">Qo'shish</button>
                                </div>
                                <div class="col-2 d-flex justify-content-center">
                                    <button type="button"
                                            @if(count($data) < 5) disabled @endif
                                            class="btn btn-success">Saqlash</button>
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
                var formData = {
                    _token: '{{ csrf_token() }}',
                    name: $('#name').val(),
                    works: $('#works').val(),
                    balls: $('#balls').val(),
                    month: $('#month').val(),
                    type: $('#type').val()
                }
                $.ajax({
                    type: 'post',
                    {{--url: "{{ route('profile.update') }}",--}}
                    data: formData,
                    dataType: 'json',
                    enctype: true
                })
                setTimeout(()=>{
                    location.reload()
                }, 1000)
            })
        })
    </script>
@endpush

