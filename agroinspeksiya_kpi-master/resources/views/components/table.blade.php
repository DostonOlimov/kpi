@php
    $count_works = 0;
    $curent_works = 0;
    $result =0 ;
    $ball = 0;
    $fine_ball = 0;
  $_GET ? $a = $_GET['month_id'] : $a = (int)date('m')  ;
@endphp
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center">Baholash shakli ( {{ \App\Models\Month::getMonth($a) }} oyi uchun)</h4>
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
                            <th>Jarima </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr style="background-color: lightskyblue; font-weight: bold;">
                            <td>1.</td>
                            <td>{{ $razdel[0]->name }}</td>
                            <td></td>
                            <td>{{ $razdel[0]->weight }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($data1 as $key => $item)
                            <tr>
                                @php
                                    $count_works += $item->works_count;
                                    $curent_works +=  $item->current_works;
                                    $ball += $item->max_ball;
                                    $result += $item->weight * $item->current_prasent /100;
                                    $fine_ball += $item->fine_ball;
                                @endphp
                                <td>1.{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td style="color: orangered">dona</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{  $item->works_count }}</td>
                                <td>{{  $item->current_works ? $item->current_works :0 }}</td>
                                <td>{{ round($item->current_prasent,2) }}%</td>
                                <td>{{ round($item->max_ball,2) }}</td>
                                <td>{{ $item->fine_ball }}</td>
                            </tr>
                        @endforeach
                        <tr style="background-color: lightskyblue; font-weight: bold;">
                            <td>2.</td>
                            <td>{{ $razdel[1]->name }}</td>
                            <td></td>
                            <td>{{ $razdel[1]->weight }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $razdel[1]->weight }}</td>
                            <td></td>
                        </tr>

                        @foreach($data2 as $key => $item)
                            <tr>
                                @php
                                    $count_works += $item->works_count;
                                    $curent_works +=  $item->current_works;
                                    $ball += $item->max_ball;
                                    $result += $item->weight * $item->current_prasent / 100;
                                    $fine_ball += $item->fine_ball;
                                @endphp
                                <td>2.{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td style="color: orangered">%</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{ $item->works_count }}</td>
                                <td>{{ $item->current_works }}</td>
                                <td>{{ round($item->current_prasent,2) }}%</td>
                                <td>{{ round($item->max_ball,2) }}</td>
                                <td>{{ $item->fine_ball }}</td>
                            </tr>
                        @endforeach

                        <tr style="background-color: lightskyblue; font-weight: bold;">
                            <td>3.</td>
                            <td>{{ $razdel[2]->name }}</td>
                            <td></td>
                            <td>{{ $razdel[2]->weight }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $razdel[2]->weight }}</td>
                            <td></td>
                        </tr>

                        @foreach($data3 as $key => $item)
                            <tr>
                                @php $count_works += $item->works_count;
                                    $curent_works +=  $item->current_works;
                                    $result += (5 * $item->current_works) / $item->works_count  ;
                                    $ball += ($item->weight * $item->current_works) / $item->works_count;
                                    $fine_ball += $item->fine_ball;
                                @endphp
                                <td>3.{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td style="color: orangered">dona</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{ $item->works_count }}</td>
                                <td>{{ $item->current_works }}</td>
                                <td>{{ round($item->current_prasent,2) }}%</td>
                                <td>{{ round($item->max_ball,2) }}</td>
                                <td>{{ $item->fine_ball}} </td>
                            </tr>
                        @endforeach

                        @if ($data4)

                        @foreach($data4 as $key => $item)
                            <tr style="background-color: lightskyblue; font-weight: bold;">
                                @php
                                    $count_works += $item->works_count;
                                    $curent_works +=  $item->current_works;
                                    $result += ( 10 * $item->current_works) / $item->works_count  ;
                                    $ball += ($item->weight * $item->current_works) / $item->works_count;
                                    $fine_ball += $item->fine_ball;
                                @endphp
                                <td>4</td>
                                <td>{{ $item->name }}</td>
                                <td style="color: orangered">%</td>
                                <td>{{ $item->weight }}</td>
                                <td>{{ $item->works_count }}</td>
                                <td>{{ $item->current_works }}</td>
                                <td>{{ round($item->current_prasent,2) }}%</td>
                                <td>{{ round($item->max_ball,2) }}</td>
                                <td>{{ $item->fine_ball}} </td>
                            </tr>
                        @endforeach
                        @else
                            <tr style="background-color: lightskyblue; font-weight: bold;">
                                <td>4.</td>
                                <td>{{ $razdel[3]->name }}</td>
                                <td></td>
                                <td>{{ $razdel[3]->weight }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ $razdel[3]->weight }}</td>
                                <td></td>
                            </tr>
                            @endif
                        <tr style="background-color: wheat; font-weight: bold;">
                            <td colspan="2" style="text-align: center;">Jami</td>
                            <td></td>
                            <td>100</td>
                        
                            <td>{{ $count_works }}</td>
                            <td>{{ $curent_works }}</td>
                            <td>{{ round($result,2) }}%</td>
                            <td>{{ round($ball,2) }}</td>
                            <td>{{ $fine_ball }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <h2 style = "text-align:center">Yakuniy ball:{{ round($ball - $fine_ball,2) }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
