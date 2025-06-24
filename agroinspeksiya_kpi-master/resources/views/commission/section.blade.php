
@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Bo'limlarning o'rtacha baholar ro'yxati ( {{ $month_name }} oyi uchun)</span></h4>
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
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th> №</th>
                                <th> Bo'limning nomi</th>
                                <th> Xodimlar soni</th>
                                <th> Bo'limning o'rtacha ko'rsatkichi</th>
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
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="p-4 pr-5 border-bottom bg-light d-sm-flex justify-content-between">
        <h4 class="card-title mb-0">Bo'limlarning o'rtacha ko'rsatkichlari</h4>
        <div id="chartdiv-chart-legend" class="mr-4"></div>
    
      </div>
      <div class="card-body d-flex flex-column">
        <div id="chartdiv"></div>
      </div>
    </div>
  </div>

  </div>
   

    <!-- pie chart component js start-->
    <x-xbar_chart :data1="$data1"  />
    <!-- pie chart component js start-->
  

@endsection
