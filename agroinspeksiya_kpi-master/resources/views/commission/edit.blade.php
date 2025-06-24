@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary">Xodim baholarini tekshirish bo'limi</span>
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
                            <div class="d-flex justify-content-between">
                                <h4>Qilingan ishlarni tekshirish formasi</h4>
                                <h4><span class="text-primary">{{$user->first_name.' '.$user->last_name}}</span></h4>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                <tr style = "background-color:bisque;">
                                    <th> №</th>
                                    <th> Ko'rsatkich nomi</th>
                                    <th> Maqsadli ko'rsatkich</th>
                                    <th> Amaldagi ko'rsatkich</th>
                                    <th> Asoslovchi hujjat</th>
                                    <th> To'plagan balli</th>
                                    <th > Chegirma balli</th>
                                    <th > Chegirmaga izoh</th>
                                    <th >Fayl yuklash</th>
                                    <th >Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data))
                                    @foreach($data as $key => $item)
                                        <tr>
                                            <td>{{ $item->razdel.'.'.$item->band_id }}</td>
                                            <td style="height: auto; white-space: normal!important; line-height: 20px;">
                                                {{ $item->name }}
                                            </td>
                                            <td>
                                                {{ $item->works_count }}
                                            </td>
                                            <td>
                                                {{ $item->current_works }}
                                            </td>
                                            <td>
                                                @if ($item->file_path)
                                                <a class="btn btn-primary" href="{{ route('profile.download',$item->id) }}">Yuklash</a>
                                                @endif

                                            </td>
                                            <td >
                                                {{ $current_ball = \App\Models\KpiEmployees::find($item->id)->CalculateBall() }}
                                            </td>
                                            @php
                                                $fine = \App\Models\FineBall::where('user_id','=',$item->user_id)->where('kpi_id','=',$item->id)->first();
                                            @endphp
                                            @if($current_ball != 0)
                                            @if (!($fine))
                                                @if(auth()->user()->role_id != 7)
                                            <td style="background-color:  rgb(242, 241, 237)">
                                                <form action="{{ route('commission.upload') }}" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="id" value={{ $item->id }} >
                                                    @csrf
                                                <input class="form-control"style="padding:5px;"
                                                       type="number" name="fine_ball">
                                            </td>
                                            <td style="background-color:  rgb(242, 241, 237)">
                                                <textarea cols="30" rows="3" class="form-control" name="commit">
                                                </textarea>
                                            </td>
                                            <td style="background-color:  rgb(242, 241, 237)">
                                            <input type="file" name="file" class="form-control">
                                            </td>
                                            <td  style="background-color: rgb(242, 241, 237)"><button type="submit" class="btn btn-success">Saqlash</button></td>
                                            </form>
                                                @else
                                                    <td style="background-color:  rgb(242, 241, 237)"></td>
                                                    <td style="background-color:  rgb(242, 241, 237)"></td>
                                                    <td style="background-color:  rgb(242, 241, 237)"></td>
                                                    <td style="background-color:  rgb(242, 241, 237)"></td>
                                                @endif
                                            @else
                                                <td style="background-color:  rgb(242, 241, 237)">{{ $fine->fine_ball }}</td>
                                                <td style="background-color:  rgb(242, 241, 237)">{{ $fine->comment }}</td>
                                                <td style="background-color:  rgb(242, 241, 237)">
                                                    @if ($fine->order_file)
                                                        <a class="btn btn-primary" href="{{ route('commission.download',$fine->id) }}">Yuklash</a>
                                                    @endif
                                                </td>
                                                <td style="background-color:  rgb(242, 241, 237)"></td>
                                            @endif
                                        @else
                                        <td style="background-color:  rgb(242, 241, 237)"></td>
                                                    <td style="background-color:  rgb(242, 241, 237)"></td>
                                                    <td style="background-color:  rgb(242, 241, 237)"></td>
                                                    <td style="background-color:  rgb(242, 241, 237)"></td>
                                        @endif
                                        </tr>
                                    @endforeach
                                @endif
                            
                                </tbody>
                            </table>
                        @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong></strong> Ma'lumotlar to'liq kiritilmagan yoki noto'g'ri ma'lumot kiritilgan.
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1 style="text-align: center;font-size:35px;font-weight:900;">Xodimning oylik ko'rsatkichlari</h1>
    <div style = "width:70%">
    <canvas id="bar_chart"></canvas>
    </div>
    <div id="chartdiv"></div>
    <!-- bar chart component js start-->
    <x-bar_chart :data1="$balls"   />
    <!-- bar chart component js start-->
@endsection
