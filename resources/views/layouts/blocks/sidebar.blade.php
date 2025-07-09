<div class="sidebar sidebar-dark sidebar-fixed " id="sidebar">
    <div class="sidebar-brand d-none d-md-flex justify-content-around">
        <img style="width:40px;" src="/assets/images/log3o.png">
        <h2 style="font-size: 20px; color: white; margin: 6px 22px 5px 0; !important;">ECOEKESPERTIZA</h2>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item"><a class="nav-link" href="/home">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-home"></use>
                </svg>Bosh sahifa</a></li>

        @if(auth()->user()->role_id === \App\Models\User::ROLE_ADMIN)
            <li class="nav-title">Foydalanuvchilar</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('employees.list') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                    </svg>
                    Ro'yxatni ko'rish
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('roles.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-shield-alt"></use>
                    </svg>
                    Foydalanuvchi rollari
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('works.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-briefcase"></use>
                    </svg>
                    Foydalanuvchi ish joylari
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('month.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-calendar"></use>
                    </svg>
                    Ish kunlari
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('kpis.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-chart-pie"></use>
                    </svg>
                    KPI Ko‘rsatkichlari
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('working-kpis.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-description"></use>
                    </svg>
                    Lavozim yo'riqnomalari
                </a>
            </li>
        @endif

        @if(auth()->user()->role_id != \App\Models\User::ROLE_ADMIN and auth()->user()->role_id != \App\Models\User::ROLE_MANAGER)

            <li class="nav-title">Shaxsiy ko'rsatkichlar</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('profile.list') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-check-circle"></use>
                    </svg>
                    Holatni tekshirish
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('profile.create') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-cloud-upload"></use>
                    </svg>
                    Baholarni to'ldirish
                </a>
            </li>
        @endif

        @if(auth()->user()->role_id === \App\Models\User::ROLE_DIRECTOR)
            <li class="nav-title">Bo'lim ko'rsatkichlari</li>

             <li class="nav-item">
                <a class="nav-link" href="{{ route('user-kpis.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-bar-chart"></use>
                    </svg>
                    Xodimlarning kpilari
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('director.list') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-task"></use>
                    </svg>
                    Xodimlarni tekshirish
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('director.employees') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-chart"></use>
                    </svg>
                    Bo'lim ko'rsatkichlar
                </a>
            </li>
        @endif

        @if(auth()->user()->role_id === \App\Models\User::ROLE_MANAGER || auth()->user()->role_id === \App\Models\User::ROLE_ADMIN)
            <li class="nav-title">Xodimlarni baholash</li>

{{--            <li class="nav-item">--}}
{{--                <a class="nav-link" href="{{ route('commission.list') }}">--}}
{{--                    <svg class="nav-icon">--}}
{{--                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-chart-line"></use>--}}
{{--                    </svg>--}}
{{--                    Xodimlar natijalari--}}
{{--                </a>--}}
{{--            </li>--}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('user-kpis.index') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-bar-chart"></use>
                    </svg>
                    Xodimlarning kpilari
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('commission.employee.list') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-task"></use>
                    </svg>
                    Xodimlarning topshiriqlari
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('days.list') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-calendar"></use>
                    </svg>
                    Xodimlar ish kunlari
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('days.behavior') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                    </svg>
                    Odob axloq normalari
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('days.activity') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-lightbulb"></use>
                    </svg>
                    Tashabbuskorlik ko'rsatkichlari
                </a>
            </li>

        @endif

{{--        @if(auth()->user()->role_id === 7 || auth()->user()->role_id === \App\Models\User::ROLE_ADMIN)--}}
{{--            <li class="nav-title">Shaxsiy profil</li>--}}

{{--            <li class="nav-item">--}}
{{--                <a class="nav-link" href="{{ route('commission.section') }}">--}}
{{--                    <svg class="nav-icon">--}}
{{--                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-building"></use>--}}
{{--                    </svg>--}}
{{--                    Bo'limlar ro'yxati--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <li class="nav-item">--}}
{{--                <a class="nav-link" href="{{ route('commission.list') }}">--}}
{{--                    <svg class="nav-icon">--}}
{{--                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>--}}
{{--                    </svg>--}}
{{--                    Xodimlar ro'yxati--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <li class="nav-item">--}}
{{--                <a class="nav-link" href="{{ route('bugalter.list') }}">--}}
{{--                    <svg class="nav-icon">--}}
{{--                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-file"></use>--}}
{{--                    </svg>--}}
{{--                    Oylik hisobotlar--}}
{{--                </a>--}}
{{--            </li>--}}
{{--        @endif--}}
  @if(auth()->user()->role_id === \App\Models\User::ROLE_ACCOUNTANT || auth()->user()->role_id === \App\Models\User::ROLE_ADMIN)
            <li class="nav-title">Xodimlar natijalari</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('bugalter.list') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-spreadsheet"></use>
                    </svg>
                    Taqsimot holatini ko'rish
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('bugalter.add') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-dollar"></use>
                    </svg>
                    Summani kiritish
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('bugalter.check') }}">
                    <svg class="nav-icon">
                        <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-check-circle"></use>
                    </svg>
                    Holatni tekshirish
                </a>
            </li>
        @endif

        <li class="nav-item"><a class="nav-link"></a></li>
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
