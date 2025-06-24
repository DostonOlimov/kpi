@extends('layouts.app')

@section('content')
    @php $_GET ? $a = $_GET['month_id'] : $a = (int)date('m')  ;  @endphp
    <!-- head table component -->
    <x-head_table/>
    <!-- search month component -->
    <x-search url="profile.upload"/>

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
                                <th> Asoslovchi hujjat fayli</th>
                                <th>Action</th>

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

                            </tr>
                            @foreach($data1 as $key => $item)
                                <tr>
                                    <td>1.{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td style="color: orangered">dona</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    @if(!$item->current_works)
                                    <form action="{{ route('profile.image.store') }}" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value={{ $item->id }} >
                                    @csrf
                                    <td> <input type="number" name="curent_works" class="form-control"></td>
                                    <td>
                                    <input type="file" name="file" class="form-control">
                                    </td>
                                    <td>
                                    <button type="submit" class="btn btn-success">Saqlash</button>
                                    </form>
                                     </td>
                                    @else
                                    <td>{{ $item->current_works }}</td>
                                    <td><a class="btn btn-primary" href="{{ route('profile.download',$item->id) }}">Yuklash</a></td>
                                        <td>{{ round(\App\Models\KpiEmployees::find($item->id)->CalculatePrasent(),2);   }}%</td>
                                    @endif
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
                                <td></td>
                            </tr>
                            @foreach($data2 as $key => $item)
                                <tr>
                                    <td>2.{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td style="color: orangered">%</td>
                                    <td>{{ $item->weight }}</td>
                                    <td>{{ $item->works_count }}</td>
                                    @if(!$item->current_works)
                                    <form action="{{ route('profile.image.store') }}" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value={{ $item->id }} >
                                    @csrf
                                    <td> <input type="number" name="curent_works" class="form-control"></td>
                                    <td>
                                    <input type="file" name="file" class="form-control">
                                    </td>
                                    <td>
                                    <button type="submit" class="btn btn-success">Saqlash</button>
                                    </form>
                                     </td>
                                    @else
                                    <td>{{ $item->current_works }}</td>
                                    <td><a class="btn btn-primary" href="{{ route('profile.download',$item->id) }}">Yuklash</a></td>
                                        <td>{{ round($item->current_works / $item->works_count,2) * 100  }}%</td>
                                    @endif
                                </tr>
                            @endforeach
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
@endsection
