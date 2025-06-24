@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Page Title Header Starts-->
        <div class="row page-title-header">
            <div class="col-12">
                <div class="page-header">
                    <h4 class="page-title"><span class="text-primary"></span> KPI me'zonlarini o'zgartirish </h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">KPI baholash ma'lumotlari</h4>
                        <form class="forms-sample" action="{{ '/update/'.$id }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="">Nomi</label>
                                <textarea name="name" class="form-control" id="" rows="4"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Turi</label>
                                <select name="type" class="form-control" id="">
                                    <option value="1">Ижро интизоми йўналиши</option>
                                    <option value="2">Асосий фаолият йўналишлари</option>
                                    <option value="3">Қонунчилик фаолият йўналиши</option>
                                    <option value="4">Қонунчилик тарғиботи йўналиши</option>
                                    <option value="5">Давлат хизматларини кўрсатиш йўналиши</option>
                                    <option value="6">Самарадорлик ва натижадорлик кўрсаткичларидан чегирмалар</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="">Baholash(har bir ish uchun ball miqdori)</label>
                                    <input name="ball" type="text" class="form-control" id="" placeholder="">
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Olinishi mumkin bo'lgan yuqori ball</label>
                                    <input name="max_ball" type="text" class="form-control" id="" placeholder="">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mr-2">Saqlash</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
