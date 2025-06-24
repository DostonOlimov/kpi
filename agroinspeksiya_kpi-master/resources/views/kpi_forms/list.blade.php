@extends('layouts.app')

@section('content')
    <!-- head table component -->
    <x-head_table/>
    <!-- search month component -->
    <x-search url="profile.list"/>
    <!--  table component -->
    <x-table :data1="$data1" :data2="$data2" :data3="$data3" :data4="$data4" />

    <h1 style="text-align: center;font-size:35px;font-weight:900;">Xodimning oylik ko'rsatkichlari</h1>
    <div style = "width:70%">
    <div id="chartdiv"></div>
    </div>
    <!-- bar chart component js start-->
    <x-bar_chart :data1="$balls" />
    <!-- bar chart component js start-->
@endsection
