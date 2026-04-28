@php use Illuminate\Support\Facades\Request; @endphp
<div class="sidebar sidebar-dark sidebar-fixed " id="sidebar">
    <div class="sidebar-brand d-none d-md-flex justify-content-around">
        @if (app()->environment('production'))
            <img style="width:40px;" src="/assets/images/logo.png">
            <h2 style="font-size: 20px; color: white; margin: 6px 22px 5px 0; !important;">ECOEKESPERTIZA</h2>
        @endif
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item"><a class="nav-link {{ Request::is('home') ? 'active' : '' }}" href="/home">
                <svg class="nav-icon">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-home"></use>
                </svg>
                Bosh sahifa</a></li>

        @php
            $currentUser = auth()->user();
            $parent_work_zone_id = get_default_parent_work_zone_id();
            $isAdmin     = $currentUser->hasRole(\App\Models\User::ROLE_ADMIN);
            $isDirector  = $currentUser->hasRole(\App\Models\User::ROLE_DIRECTOR);
            $isManager   = $currentUser->hasRole(\App\Models\User::ROLE_MANAGER);
            $isAccountant= $currentUser->hasRole(\App\Models\User::ROLE_ACCOUNTANT);
            $isUser      = $currentUser->hasRole(\App\Models\User::ROLE_USER);
            $isKadrlar   = $currentUser->hasRole(\App\Models\User::ROLE_KADRLAR);
            $isIjro      = $currentUser->hasRole(\App\Models\User::ROLE_IJRO);

            $userRoleNames = array_values(array_filter([
                $isAdmin      ? 'admin'      : null,
                $isDirector   ? 'director'   : null,
                $isManager    ? 'manager'    : null,
                $isAccountant ? 'accountant' : null,
                $isUser       ? 'user'       : null,
                $isKadrlar    ? 'kadrlar'    : null,
                $isIjro       ? 'ijro'       : null,
            ]));

            $allSections = [
                [
                    'title' => 'Foydalanuvchilar',
                    'roles' => ['admin', 'director', 'kadrlar'],
                    'items' => [
                        ['route' => 'works.list',              'param' => null,                   'icon' => 'cil-briefcase',     'label' => "Bo'limlar",                          'active_route' => 'works',           'roles' => ['admin']],
                        ['route' => 'month.index',             'param' => null,                   'icon' => 'cil-calendar',      'label' => 'Oy kunlari',                         'active_route' => 'month',           'roles' => ['admin']],
                        ['route' => 'kpis.user-kpis-dashboard','param' => null,                   'icon' => 'cil-chart-pie',     'label' => 'Baholash mezoni',                    'active_route' => 'kpis',            'roles' => ['admin', 'director']],
                        ['route' => 'working-kpis.index',      'param' => $parent_work_zone_id,   'icon' => 'cil-description',   'label' => "KPI Ko'rsatkichlari",                'active_route' => 'working-kpis',    'roles' => ['admin', 'director']],
                        ['route' => 'employee.kpis.users',     'param' => $parent_work_zone_id,   'icon' => 'cil-bar-chart',     'label' => "Xodimlarning KPI ko'rsatkichlari",   'active_route' => 'employee/users',  'roles' => ['admin', 'director']],
                        ['route' => 'attendances.index', 'param' => null, 'icon' => 'cil-calendar', 'label' => "Davomat ro'yxati", 'active_route' => 'attendances', 'roles' => ['admin', 'kadrlar']],
                    ],
                ],
                [
                    'title' => "Shaxsiy ko'rsatkichlar",
                    'roles' => ['user', 'director', 'accountant', 'ijro'],
                    'items' => [
                        ['route' => 'department.user.detail', 'param' => $currentUser->id, 'icon' => 'cil-check-circle', 'label' => 'Holatni tekshirish',    'active_route' => 'departments/users', 'roles' => ['user', 'director']],
                        ['route' => 'profile.create',          'param' => null,             'icon' => 'cil-cloud-upload', 'label' => "Baholarni to'ldirish",  'active_route' => 'employee-profile',  'roles' => ['user', 'director']],
                        ['route' => 'edodocuments.index',      'param' => null,             'icon' => 'cil-file',         'label' => 'EDO Hujjatlari',        'active_route' => 'edodocuments',      'roles' => ['user', 'director', 'ijro']],
                    ],
                ],
                [
                    'title' => "Bo'lim ko'rsatkichlari",
                    'roles' => ['director'],
                    'items' => [
                        ['route' => 'director.list',         'param' => null,                       'icon' => 'cil-task',  'label' => 'Xodimlarni tekshirish', 'active_route' => 'director-profile',  'roles' => ['director']],
                        ['route' => 'kpi.department.detail', 'param' => $currentUser->work_zone_id, 'icon' => 'cil-chart', 'label' => "Bo'lim ko'rsatkichlar", 'active_route' => 'departments-details','roles' => ['director']],
                    ],
                ],
                [
                    'title' => 'Xodimlarni baholash',
                    'roles' => ['manager', 'admin', 'kadrlar','ijro'],
                    'items' => [
                        ['route' => 'commission.employee.list',    'param' => null,                        'icon' => 'cil-task',          'label' => 'Topshiriqlarni baholash',            'active_route' => 'commission-profile/employee-list',          'roles' => ['manager', 'admin']],
                        ['route' => 'days.list',                   'param' => null,                        'icon' => 'cil-calendar',      'label' => 'Xodimlarning ish kunlari',           'active_route' => 'days/list',                                 'roles' => ['manager', 'admin']],
                        ['route' => 'commission.band_scores.list', 'param' => \App\Models\Kpi::IJRO,      'icon' => 'cil-balance-scale', 'label' => 'Ijro intizomi normalari',   'active_route' => 'commission-profile/user-band-scores/' . \App\Models\Kpi::IJRO,      'roles' => ['manager', 'admin', 'ijro']],
                        ['route' => 'commission.band_scores.list', 'param' => \App\Models\Kpi::BEHAVIOUR, 'icon' => 'cil-task',          'label' => 'Mehnat intizomi normalari', 'active_route' => 'commission-profile/user-band-scores/' . \App\Models\Kpi::BEHAVIOUR, 'roles' => ['manager', 'admin','kadrlar']],
                        ['route' => 'days.activity',               'param' => null,                        'icon' => 'cil-lightbulb',     'label' => "Tashabbuskorlik ko'rsatkichlari",    'active_route' => 'days/activity-list',                        'roles' => ['manager', 'admin']],
                    ],
                ],
                [
                    'title' => "Hisob-kitob bo'limi",
                    'roles' => ['accountant', 'admin'],
                    'items' => [
                        ['route' => 'kpi.departments', 'param' => $parent_work_zone_id, 'icon' => 'cil-chart-line',   'label' => 'Yakuniy natijalar',         'active_route' => 'departments', 'roles' => ['accountant', 'admin']],
                        ['route' => 'bugalter.list',   'param' => null,                 'icon' => 'cil-spreadsheet',  'label' => "Taqsimot holatini ko'rish", 'active_route' => 'bugalter/list',    'roles' => ['accountant', 'admin']],
                        ['route' => 'bugalter.add',    'param' => null,                 'icon' => 'cil-dollar',       'label' => 'Summani kiritish',          'active_route' => 'bugalter/add',     'roles' => ['accountant', 'admin']],
                        ['route' => 'bugalter.check',  'param' => null,                 'icon' => 'cil-check-circle', 'label' => "Taqsimot mablag'lari",     'active_route' => 'bugalter/check',   'roles' => ['accountant', 'admin']],
                    ],
                ],
            ];
        @endphp

        @foreach ($allSections as $section)
            @if (count(array_intersect($userRoleNames, $section['roles'])) > 0)
                <li class="nav-title">{{ $section['title'] }}</li>
                @foreach ($section['items'] as $item)
                    @if (count(array_intersect($userRoleNames, $item['roles'])) > 0)
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is($item['active_route']) || Request::is($item['active_route'] . '/*') ? 'active' : '' }}"
                                href="{{ route($item['route'], $item['param'] ?? null) }}">
                                <svg class="nav-icon">
                                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#{{ $item['icon'] }}"></use>
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        <li class="nav-item"><a class="nav-link"></a></li>
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
