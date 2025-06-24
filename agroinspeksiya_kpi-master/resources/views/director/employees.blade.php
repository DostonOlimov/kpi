@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h2>Xodimlar ro'yxati</h2>
                        <!-- search month component start -->
                        <x-search url="director.employees"/>
                        <!-- search month component end -->
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <table class="table table-bordered table">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>Ismi</th>
                                <th>Familiyasi</th>
                                <th>Sharifi</th>
                                <th>Oylik maoshi</th>
                                <th>Lavozimi</th>
                                <th>Ish joyi</th>
                                <th>Yakuniy bahosi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->father_name }}</td>
                                    <td>{{ $user->salary }}</td>
                                    <td>{{ $user->lavozimi }}</td>
                                    <td>{{ $user->work_zone->name ?? '' }}</td>
                                    <td>{{ optional($user->totalBalls->first())->current_ball }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="p-4 pr-5 border-bottom bg-light d-sm-flex justify-content-between">
        <h4 class="card-title mb-0">Xodimlarning o'rtacha ko'rsatkichlari</h4>
        <div id="chartdiv-chart-legend" class="mr-4"></div>
    
      </div>
      <div class="card-body d-flex flex-column">
        <div id="chartdiv"></div>
      </div>
    </div>
  </div>

  </div>
   

    <!-- pie chart component js start-->
    <x-pie_chart :data1="$chart_data"  />
    <!-- pie chart component js start-->
@endsection

