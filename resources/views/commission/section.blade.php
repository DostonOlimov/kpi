
@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Page Title Header Starts -->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title">
                    <span class="text-primary">
                        Bo'limlarning o'rtacha baholar ro'yxati ({{ $month_name }} oyi uchun)
                    </span>
                    </h4>
                </div>
            </div>
        </div>

        <!-- search month component start -->
        <x-search url="commission.section"/>
        <!-- search month component end -->

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4>Xodimlar baholari jadvali</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="thead-light">
                                <tr>
                                    <th>№</th>
                                    <th>Bo'limning nomi</th>
                                    <th>Xodimlar soni</th>
                                    <th>Bo'limning o'rtacha ko'rsatkichi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($section as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->count_employees }}</td>
                                        <td>{{ $item->section_ball }}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="p-4 pr-5 border-bottom bg-light d-sm-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Bo'limlarning o'rtacha ko'rsatkichlari</h4>
                        <div id="chartdiv-chart-legend" class="mr-4"></div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div id="chartdiv" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
