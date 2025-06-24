@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"> Xodimning o'zini o'zi baholash bo'limi
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
                                    <th> Toifasi</th>
                                    <th> Nomi</th>
                                    <th style="background-color: goldenrod;"> Ball miqdori</th>
                                    <th> Ishlar miqdori(dona)</th>
                                    <th> Asoslovchi hujjat</th>
                                    <th style="background-color: limegreen;"> To'plangan ball</th>
                                    <th style="background-color: indianred"> Maxsimal ball</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $key => $item)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="id-{{ $key + 1 }}"
                                                   value="{{$item->id}}">
                                            {{ $key + 1 }}
                                        </td>
                                        <td style="height: auto; white-space: normal!important; line-height: 20px;">
                                            {{ $types[$item->type] }}
                                        </td>
                                        <td style="height: auto; white-space: normal!important; line-height: 20px;">
                                            {{ $item->name }}
                                        </td>
                                        <td style="background-color: goldenrod">
                                            <input id="" class="form-control ball-{{ $key + 1 }}" type="text"
                                                   readonly value="{{ $item->ball }}">
                                        </td>
                                        <td>
                                            <input class="form-control works_count-{{ $key + 1 }}" name="works_count-{{ $key + 1 }}" type="number">
                                        </td>
                                        <td style="width: 240px; height: auto; white-space: normal!important;">
                                            <input class="form-control p-1 file-{{ $key + 1 }}" type="file" name="file-{{ $key + 1 }}">
                                            <label for="">maximal 2 mb hajmli (.pdf, .doc, .docx, .jpg, png) fayl
                                                yuklang</label>
                                        </td>
                                        <td style="background-color: limegreen">
                                            <input id="" class="form-control current_ball-{{ $key + 1 }}" type="text"
                                                   name="current_ball-{{ $key + 1 }}"
                                                   readonly>
                                        </td>
                                        <td style="background-color: indianred">
                                            <input id="" class="form-control max_ball-{{ $key + 1 }}" type="text"
                                                   readonly value="{{ $item->max_ball }}">
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="background-color: lavender">
                                    <td colspan="6" style="text-align: center; font-weight: bold;">Jami:</td>
                                    <td class="current_total" style="text-align: center; font-weight: bold;">0</td>
                                    <td class="max_total" style="text-align: center; font-weight: bold;">0</td>
                                </tr>
                                <input type="hidden" name="fields" value="{{count($data)}}">
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
