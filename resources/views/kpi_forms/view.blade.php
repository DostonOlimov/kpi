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
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-bar-chart mr-1"></i>&nbsp; {{ __("Baholash me'zonlarini qo'shish") }}
                </li>
            </ol>
        </div>

        @if (session()->has('fail'))
            <div class="alert alert-danger">{{ session('fail') }}</div>
        @endif

        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="mb-0">{{ __("Joriy KPI me'zonlari") }}</h4>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered table-responsive mb-4">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>{{ __("Me'zon nomi") }}</th>
                                <th>{{ __("Qilinadigan ishlar (dona)") }}</th>
                                <th>{{ __("Maksimal ball") }}</th>
                                <th>{{ __("Hisobot oyi") }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($types as $key => $item)
                                <tr>
                                    <td>1.{{ $key + 1 }}</td>
                                    <td style="white-space: normal;">{{ $item->name }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->month }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __("Ma'lumot yo'q") }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        <hr>

                        @if(count($data) < 5)
                            <div class="alert alert-danger">
                                {{ __("Kamida 5 ta me'zon kiriting!") }}
                            </div>
                        @endif

                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>{{ __("Me'zon nomi") }}</th>
                                <th>{{ __("KPI nomi") }}</th>
                                <th>{{ __("Qilinadigan ishlar (dona)") }}</th>
                                <th>{{ __("Maksimal ball") }}</th>
                                <th>{{ __("Hisobot oyi") }}</th>
                                <th>{{ __("Harakat") }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($data as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>-</td>
                                    <td style="white-space: normal;">{{ $item->name }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->month }}</td>
                                    <td>
                                        <a href="{{ route('director.delete', $item->id) }}" class="btn btn-danger btn-sm">
                                            {{ __("O'chirish") }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __("Ma'lumot yo'q") }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body border-top">
                        <h4 class="mb-4">{{ __("Baholash me'zoni ma'lumotlari") }}</h4>
                        <form class="forms-sample" action="#">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __("KPI nomi") }}</label>
                                    <textarea name="name" id="name" rows="6" class="form-control" style="font-size: 16px; font-weight: bold;"></textarea>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="works">{{ __("Qilinadigan ishlar soni (dona)") }}</label>
                                    <input name="works_count" type="number" id="works" class="form-control">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="balls">{{ __("Olinishi mumkin bo'lgan ball") }}</label>
                                    <input name="max_ball" type="number" id="balls" class="form-control">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="month">{{ __("Hisobot oyi") }}</label>
                                    <select name="month" id="month" class="form-control">
                                        @foreach($month as $m)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="type">{{ __("KPI me'zoni") }}</label>
                                    <select name="work_type" id="type" class="form-control">
                                        @foreach($types as $key => $t)
                                            <option value="{{ $t->id }}">1.{{ $key + 1 }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-info btn-block">{{ __("Qo'shish") }}</button>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success btn-block" @if(count($data) < 5) disabled @endif>
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

