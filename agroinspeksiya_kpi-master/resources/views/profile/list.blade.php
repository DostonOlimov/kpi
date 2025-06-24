@extends('layouts.app')

@section('content')
    <style>
        table th{
            height: auto;
            white-space: normal!important;
            border: 1px solid grey!important;
        }
        table td {
            height: auto;
            white-space: normal!important;
            border: 1px solid grey!important;
        }
    </style>
    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title">
                        <span class="text-primary">
                            Давлат органи таркибий тузилмаси, ҳудудий ва идоравий мансуб ташкилотларнинг давлат фуқаролик хизматчиси фаолиятини ЭМСК асосида
                        </span>
                    </h4>
                </div>
            </div>
        </div>
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
                                <th> Amaldagi ko'rsatkich </th>
                                <th> Ijro(%) </th>
                                <th> Vazn hisobga olingan holdagi ko'rsatkich (ball) </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7=6/5*100</td>
                                <td>8=4*7</td>
                            </tr>
                            <tr style="background-color: lightskyblue; font-weight: bold;">
                                <td>1.</td>
                                <td>{{ $razdel[0]->name }}</td>
                                <td></td>
                                <td>{{ $razdel[0]->max_ball }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ '-' }}</td>
                            </tr>
                            @foreach($data1 as $key => $item)
                                <tr>
                                    <td>1.{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td style="color: orangered">dona</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->current_works }}</td>
                                    <td>{{ (100 * $item->current_works) / $item->works_count }}</td>
                                    <td>{{ ($item->weight * $item->current_works) / $item->works_count }}</td>
                                </tr>
                            @endforeach
                            <tr style="background-color: lightskyblue; font-weight: bold;">
                                <td>2.</td>
                                <td>{{ $razdel[1]->name }}</td>
                                <td></td>
                                <td>{{ $razdel[1]->max_ball }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ '-' }}</td>
                            </tr>
                            @foreach($data2 as $key => $item)
                                <tr>
                                    <td>2.{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td style="color: orangered">%</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->current_works }}</td>
                                    <td>{{ (100 * $item->current_works) / $item->works_count }}</td>
                                    <td>{{ ($item->weight * $item->current_works) / $item->works_count }}</td>
                                </tr>
                            @endforeach
                            <tr style="background-color: lightskyblue; font-weight: bold;">
                                <td>3.</td>
                                <td>{{ $razdel[2]->name }}</td>
                                <td></td>
                                <td>{{ $razdel[2]->max_ball }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ '-' }}</td>
                            </tr>
                            @foreach($data3 as $key => $item)
                                <tr>
                                    <td>3.{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td style="color: orangered">dona</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    <td>{{ $item->current_works }}</td>
                                    <td>{{ (100 * $item->current_works) / $item->works_count }}</td>
                                    <td>{{ ($item->weight * $item->current_works) / $item->works_count }}</td>
                                </tr>
                            @endforeach
                            <tr style="background-color: lightskyblue; font-weight: bold;">
                                <td>4.</td>
                                <td>{{ $razdel[3]->name }}</td>
                                <td></td>
                                <td>{{ $razdel[3]->max_ball }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ '-' }}</td>
                            </tr>
                            <tr style="background-color: wheat; font-weight: bold;">
                                <td colspan="2" style="text-align: center;">Jami</td>
                                <td></td>
                                <td>100</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ '-' }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
