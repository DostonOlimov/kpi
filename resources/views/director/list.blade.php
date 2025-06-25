@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- Page Header -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-pie-chart mr-1"></i>&nbsp; {{ __("Bo'limning erishgan ko'rsatkichlari") }}
                </li>
            </ol>
        </div>
    <!-- search month component -->
    <x-search url="director.list"/>
    @php
    $ball = 0;
    $prasent = 0;
    $key = 0;
    @endphp
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center">Baholash shakli ({{ $month }} oyi uchun)</h4>
                    <table class="table table-bordered table-responsive">
                        <thead>
                        <tr>
                            <th>T/r</th>
                            <th>Ko'rsatkich nomi</th>
                            <th>O'lchov birligi</th>
                            <th>Vazni</th>
                            <th>Maqsadli ko'rsatkich</th>
                            <th>Xodimlarning maqsadli ko'rsatkichi</th>
                            <th>Amaldagi ko'rsatkich</th>
                            <th>Ijro (%)</th>
                            <th>Amaldagi Ijro (%)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr style="background-color: lightskyblue; font-weight: bold;">
                            <td>1.</td>
                            <td>{{ $razdel[0]->name }}</td>
                            <td></td>
                            <td>{{ $razdel[0]->weight }}</td>
                            <td colspan="5"></td>
                        </tr>
                        @php
                            $prasent = 0;
                            $ball = 0;
                        @endphp
                        @foreach($data1 as $key => $item)
                            @php
                                $prasent += $item->count_works / $item->weight * 100;
                                $ball += $item->current_prasent;
                            @endphp
                            <tr>
                                <td>1.{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td style="color: orangered">dona</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{ $item->works_count }}</td>
                                <td>{{ $item->count_works }}</td>
                                <td>{{ $item->current_works }}</td>
                                <td>{{ round($item->count_works / $item->weight * 100, 2) }}%</td>
                                <td>{{ round($item->current_prasent, 2) }}%</td>
                            </tr>
                        @endforeach
                        <tr style="background-color: lightskyblue; font-weight: bold;">
                            <td>2.</td>
                            <td>{{ $razdel[1]->name }}</td>
                            <td></td>
                            <td>{{ $razdel[1]->weight }}</td>
                            <td colspan="4"></td>
                            <td>{{ $razdel[1]->weight }}</td>
                        </tr>
                        <tr style="background-color: wheat; font-weight: bold;">
                            <td colspan="2" class="text-center">Jami</td>
                            <td></td>
                            <td>60</td>
                            <td>{{ round($SumWorksCount, 2) }}</td>
                            <td>{{ round($TotalWorksCount, 2) }}</td>
                            <td>{{ round($TotalCurrentWorks, 2) }}</td>
                            <td>{{ round($prasent / ($key + 1), 2) }}%</td>
                            <td>{{ round($ball / ($key + 1), 2) }}%</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>


@endsection
