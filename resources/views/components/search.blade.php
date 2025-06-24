<div style="display:inline-block">
<form method="GET" action="{{ route(  $url  ) }}">
    <div class="py-2 flex">
        <div class="overflow-hidden flex pl-4" >
            <div style = "display: inline-block;">
                <strong>Oy nomi:</strong>
                @php $_GET ? $a = $_GET['month_id'] : $a = (int)date('m')  ;  @endphp
                <select name="month_id" class="form-control" >
                    @foreach($months as $key => $month)
                        @if($key == $a )
                            <option value="{{$key}}">{{$month}}</option>
                        @endif
                    @endforeach
                    @foreach($months as $key => $month)
                        @if($key !=  $a )
                            <option value="{{$key}}">{{$month}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
           
        </div>
    </div>

</div>
<div style="display:inline-block">

    <div class="py-2 flex">
        <div class="overflow-hidden flex pl-4" >
            <div style = "display: inline-block;">
                <strong>Joriy yil:</strong>
                @php $_GET ? $a = $_GET['year'] : $a = (int)date('Y')  ;  @endphp
                <select name="year" class="form-control" >
                    @foreach($years as $year)
                        @if($year == $a )
                            <option value="{{$year}}">{{$year}}</option>
                        @endif
                    @endforeach
                    @foreach($years as  $year)
                        @if($year !=  $a )
                            <option value="{{$year}}">{{$year}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type='submit' class='btn-primary ml-4 ' style = "display: inline-block;">
                {{ __('Tanlash') }}
            </button>
        </div>
    </div>
</form>
</div>
