@extends('layouts.app')

@section('content')

    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-users mr-1"></i>&nbsp; {{ __("Xodimlar ro'yxati") }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <!-- Search Component -->

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
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
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>{{ $user->father_name }}</td>
                                        <td>{{ number_format($user->salary, 0, ',', ' ') }} so'm</td>
                                        <td>{{ $user->lavozimi }}</td>
                                        <td>{{ $user->work_zone->name ?? '-' }}</td>
                                        <td>{{ optional($user->totalBalls->first())->current_ball ?? '—' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-sm-flex justify-content-between bg-light">
                        <h4 class="card-title mb-0">{{ __("Xodimlarning o'rtacha ko'rsatkichlari") }}</h4>
                        <div id="chartdiv-chart-legend" class="mr-4"></div>
                    </div>
                    <div class="card-body">
                        <div id="chartdiv"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie chart component -->
        <x-pie_chart :data1="$chart_data" />
    </div>


@endsection

