@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Xodimning o'zini o'zi baholash bo'limi</span>
                    </h4>
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
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('profile.store')}}" method="POST" enctype="multipart/form-data" multiple>
                            @csrf
                            <h4>Qilingan ishlarni baholash me'zonlari formasi</h4>
                            <table class="table table-bordered table-responsive">
                                <thead>
                                <tr>
                                    <th> №</th>
                                    <th> Ko'rsatkich nomi</th>
                                    <th style="background-color: goldenrod;"> Ball miqdori</th>
                                    <th> Qilingan ishlar miqdori(dona)</th>
                                    <th> Asoslovchi hujjat</th>
                                    <th style="background-color: limegreen;"> To'plangan ball</th>
                                    <th style="background-color: indianred"> Maxsimal ball</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($types))
                                    @foreach($types as $key => $item)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="id-{{ $key + 1 }}"
                                                       value="{{$item->id}}">
                                                {{ $key + 1 }}
                                            </td>
                                            <td style="height: auto; white-space: normal!important; line-height: 20px;">
                                                {{ $item->name }}
                                            </td>
                                            <td style="background-color: goldenrod">
                                                <input id="" class="form-control ball-{{ $key + 1 }}" type="text"
                                                       readonly value="{{ 1 }}">
                                            </td>
                                            <td>
                                                <input class="form-control works_count-{{ $key + 1 }}"
                                                       name="works_count-{{ $key + 1 }}" type="number">
                                            </td>
                                            <td style="width: 240px; height: auto; white-space: normal!important;">
                                                <input class="form-control p-1 file-{{ $key + 1 }}" type="file"
                                                       name="file-{{ $key + 1 }}">
                                                <label for="">maximal 2 mb hajmli (.pdf, .doc, .docx, .jpg, png) fayl
                                                    yuklang</label>
                                            </td>
                                            <td style="background-color: limegreen">
                                                <input id="" class="form-control current_ball-{{ $key + 1 }}"
                                                       type="text"
                                                       name="current_ball-{{ $key + 1 }}"
                                                       readonly>
                                            </td>
                                            <td style="background-color: indianred">
                                                <input id="" class="form-control max_ball-{{ $key + 1 }}" type="text"
                                                       readonly value="{{ $item->weight }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr style="background-color: lavender">
                                    <td colspan="5" style="text-align: center; font-weight: bold;">Jami:</td>
                                    <td class="current_total" style="text-align: center; font-weight: bold;">0</td>
                                    <td class="max_total" style="text-align: center; font-weight: bold;">0</td>
                                </tr>
                                <input type="hidden" name="fields" value="{{count($data)}}">
                                <input id="max_ball" type="hidden" name="max_ball" value="">
                                </tbody>
                            </table>
                            <div class="row mt-4 justify-content-end">
                                <div class="col-1">
                                    <button type="submit" class="btn btn-success">Saqlash</button>
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
        let total_count = {{ count($data) }};
        let balls = [];
        let works_count = [];
        let files = [];
        let current_balls = [];
        let max_balls = [];
        for (let i = 1; i <= total_count; i++) {
            works_count.push(document.querySelector('.works_count-' + i))
            files.push(document.querySelector('.file-' + i))
            current_balls.push(document.querySelector('.current_ball-' + i))
            balls.push(document.querySelector('.ball-' + i))
            max_balls.push(document.querySelector('.max_ball-' + i))
        }
        for (let i = 1; i < total_count; i++) {
            works_count[i].setAttribute('readonly', true);
            files[i].classList.add('d-none');
            files[0].classList.add('d-none');
        }
        let max_total = document.querySelector('.max_total');
        let max_ball = document.querySelector('#max_ball');
        let current_total = document.querySelector('.current_total');
        let max_total_ball = 0;
        let current_total_ball = 0;
        for (let i = 0; i < total_count; i++) {
            max_total_ball += +max_balls[i].value
        }
        max_total.innerHTML = max_total_ball;
        max_ball.valeu = max_total_ball;
        for (let i = 0; i < total_count; i++) {
            works_count[i].addEventListener('change', () => {
                if ((+works_count[i].value === 0) && (i < total_count - 1)) {
                    works_count[i + 1].removeAttribute('readonly')
                    files[i].classList.add('d-none');
                    current_balls[i].value = 0
                    works_count[i].setAttribute('readonly', true)
                } else if ((+works_count[i].value !== 0) && (i < total_count - 1)) {
                    files[i].classList.remove('d-none');
                    works_count[i + 1].setAttribute('readonly', true)
                } else if ((+works_count[i].value === 0) && (i === total_count - 1)) {
                    files[i].classList.add('d-none');
                    current_balls[i].value = 0
                    works_count[i].setAttribute('readonly', true)
                } else if ((+works_count[i].value !== 0) && (i === total_count - 1)) {
                    files[i].classList.remove('d-none');
                }
            })
            files[i].addEventListener('change', () => {
                if (files[i].value !== null && i < total_count - 1) {
                    works_count[i + 1].removeAttribute('readonly')
                    files[i].setAttribute('readonly', true)
                    works_count[i].setAttribute('readonly', true)
                    current_balls[i].value = ((balls[i].value * works_count[i].value) <= max_balls[i].value) ? (balls[i].value * works_count[i].value) : max_balls[i].value
                    current_total_ball += +current_balls[i].value
                    current_total.innerHTML = current_total_ball;
                } else if (files[i].value !== null && i === total_count - 1) {
                    files[i].setAttribute('readonly', true)
                    works_count[i].setAttribute('readonly', true)
                    current_balls[i].value = ((balls[i].value * works_count[i].value) <= max_balls[i].value) ? (balls[i].value * works_count[i].value) : max_balls[i].value
                    current_total_ball += +current_balls[i].value
                    current_total.innerHTML = current_total_ball;
                }
            })
        }
    </script>
@endpush
