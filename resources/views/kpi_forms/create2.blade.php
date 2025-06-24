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
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-center">Baholash shakli</h4>
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> T/r </th>
                                <th> Ko'rsatkich nomi </th>
                                <th> O'lchov birligi </th>
                                <th> Vazni </th>
                                <th> Maqsadli ko'rsatkich </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>{{ $kpi_req[0]->name }}</td>
                                <td>%</td>
                                <td>10</td>
                                @if (!$data3)
                                <td> <form style="width:auto;height:auto;color:black;margin:0;" action="{{ route('profile.save2') }}" method="POST" enctype="multipart/form-data">
                                 @csrf
                                <input type="hidden" name="month" value ={{ $month }} >
                                <input type="hidden" name="year" value ={{ $year }} >
                                 <input type="hidden" name="id" value = {{ $kpi_req[0]->id }}>
                                <input type="number" name="name" class="form-control" placeholder="Maqsadli ko'rsatkichlar sonini kiriting">
                                 @error('name')
                                @enderror
                                <button class="btn btn-success" type="submit">Saqlash</button>
                                </form></td>
                                @else
                                    <td>{{ $data3->works_count }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>{{ $kpi_req[1]->name }}</td>
                                <td>%</td>
                                <td>10</td>
                            @if(!$data4)
                                <td> <form style="width:auto;height:auto;color:black;margin:0;" action="{{ route('profile.save2') }}" method="POST" enctype="multipart/form-data">
                                 @csrf
                                <input type="hidden" name="month" value ={{ $month }} >
                                <input type="hidden" name="year" value ={{ $year }} >
                                <input type="hidden" name="id" value = {{ $kpi_req[1]->id }}>
                                <input type="number" name="name" class="form-control" placeholder="Maqsadli ko'rsatkichlar sonini kiriting">
                                 @error('name')
                                @enderror
                                <button class="btn btn-success" type="submit">Saqlash</button>
                                </form></td>
                                @else
                                    <td>{{ $data4->works_count }}</td>
                                @endif
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

