<header class="header header-sticky" style="background-color: #cff0fa">
    <div class="container-fluid" style="flex-wrap: nowrap">
        <button class="header-toggler px-md-0 me-md-3" type="button"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <svg class="icon icon-lg">
                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
        </button>
{{--        @php--}}
{{--            $currentFlag = '/img/flags/uzbekistan.svg';--}}
{{--            if (app()->getLocale() == 'ru') {--}}
{{--                $currentFlag = '/img/flags/russia.svg';--}}
{{--            } elseif (app()->getLocale() == 'en') {--}}
{{--                $currentFlag = '/img/flags/america.svg';--}}
{{--            }--}}
{{--        @endphp--}}
        <div class="d-flex align-items-center justify-content-around" style="width: 86%;">
            <div>
                <div class="dropdown">
                    <img id="currentFlag" src="/img/flags/uzbekistan.svg" class="flag">
                    <div class="my-dropdown-content">
                        <a href="#"><img src="/img/flags/uzbekistan.svg"
                                class="flag">O'zbek</a>
{{--                        <a href="#" onclick="changeLanguage('krill')"><img src="/img/flags/uzbekistan.svg"--}}
{{--                                class="flag">Ўзбек</a>--}}
{{--                        <a href="#" onclick="changeLanguage('en')"><img src="/img/flags/america.svg"--}}
{{--                                class="flag">English</a>--}}
{{--                        <a href="#" onclick="changeLanguage('ru')"><img src="/img/flags/russia.svg"--}}
{{--                                class="flag">Русский</a>--}}
                    </div>
                </div>
            </div>
            <div class="title title-dashboard" style="width: 100%; text-align: center;">
                <h1 style="text-transform: uppercase; font-size: 1.3rem; margin: 7px 15px">
                   DAVLAT EKOLOGIK EKSPERTIZASI MARKAZINING AVTOMATLASHGAN KPI TIZIMI
                </h1>
            </div>
            <div>
                <div class="dropdown" style="padding-right: 5px;">
                    <div id="currentMonth" class="menu-year">
                        @php
                            $months = [
                                1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart',
                                4 => 'Aprel', 5 => 'May', 6 => 'Iyun',
                                7 => 'Iyul', 8 => 'Avgust', 9 => 'Sentyabr',
                                10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr'
                            ];
                            $currentMonth = session('month') ?? (int)date('m');
                            echo get_month_name($currentMonth);
                        @endphp
                    </div>
                    <div class="my-dropdown-year">
                        @foreach($months as $key => $month)
                            <a href="#" onclick="changeMonth('{{ $key }}')"><b>{{ $month }}</b></a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div>
                <div class="dropdown">
                    <div id="currentYear" class="menu-year">@php echo session('year') ?  session('year') : date('Y'); @endphp </div>
                    <div class="my-dropdown-year">
                        <a href="#" onclick="changeYear('2024')"><b>2024</b></a>
                        <a href="#" onclick="changeYear('2025')"><b>2025</b></a>
                    </div>
                </div>
            </div>
        </div>
        <ul class=" ms-3">
            <li class="nav-item dropdown"><a class="nav-link py-0" data-coreui-toggle="dropdown" href="#"
                    role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar-md">
                        <svg class="avatar-img" xmlns="http://www.w3.org/2000/svg" height="16" width="16"
                            viewBox="0 0 512 512">
                            <path
                                d="M406.5 399.6C387.4 352.9 341.5 320 288 320H224c-53.5 0-99.4 32.9-118.5 79.6C69.9 362.2 48 311.7 48 256C48 141.1 141.1 48 256 48s208 93.1 208 208c0 55.7-21.9 106.2-57.5 143.6zm-40.1 32.7C334.4 452.4 296.6 464 256 464s-78.4-11.6-110.5-31.7c7.3-36.7 39.7-64.3 78.5-64.3h64c38.8 0 71.2 27.6 78.5 64.3zM256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-272a40 40 0 1 1 0-80 40 40 0 1 1 0 80zm-88-40a88 88 0 1 0 176 0 88 88 0 1 0 -176 0z" />
                        </svg>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-start pt-0"
                    style="transform: translateX(-180px) translateY(30px)">
                    <div class="dropdown-header bg-light">
                        <div class="fw-semibold">{{ auth()->user()->name . ' ' . auth()->user()->lastname }}</div>
                        <a class="dropdown-item" href="#">
                            <svg class="icon me-2">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                            </svg>{{ trans('message.Shaxsiy ma\'lumotlar') }}</a><a class="dropdown-item"
                            href="#">
                            <svg class="icon me-2">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                            </svg>{{ trans('message.Sozlamalar') }}</a>
                        <a class="dropdown-item" href="#" title="Logout"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <svg class="icon me-2">
                                <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                            </svg>{{ trans('message.Tizimdan chiqish') }} <form id="logout-form"
                                action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form></a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
