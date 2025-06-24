<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <img style="width: 100px; margin: auto;" src="{{ url('/assets/images/logoNEW.png') }}" alt="logo">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="text-wrapper">
                    <p class="profile-name m-0">{{ auth()->user()->first_name. ' '.auth()->user()->last_name }}</p>
                </div>
            </a>
        </li>
        @if(auth()->user()->role_id == 1)
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#employees" aria-expanded="false"
                   aria-controls="ui-basic">
                    <i class="menu-icon typcn typcn-coffee"></i>
                    <span class="menu-title">Foydalanuvchilar</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="employees">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('employees.list')}}">Ro'yxatni ko'rish</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('roles.index')}}">Foydalanuvchi rollari</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('works.index')}}">Foydalanuvchi ish joylari</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('month.index')}}">Ish kunlari</a>
                        </li>
                    </ul>
                </div>
            </li>
        @elseif(auth()->user()->role_id == 3)
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#profile" aria-expanded="false"
                   aria-controls="ui-basic">
                    <i class="menu-icon typcn typcn-coffee"></i>
                    <span class="menu-title">Baholash ko'rsatkichlari</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="profile">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('profile.list')}}">Holatni tekshirish</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('profile.add2')}}">Ko'rsatkichlarni qo'shish</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('profile.upload')}}">Baholarni to'ldirish</a>
                        </li>

                    </ul>
                </div>
            </li>
        @elseif(auth()->user()->role_id == 2)
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#kpi" aria-expanded="false" aria-controls="ui-basic">
                    <i class="menu-icon typcn typcn-coffee"></i>
                    <span class="menu-title">Baholash ko'rsatkichlari</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="kpi">
                    <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                            <a class="nav-link" href="{{route('director.list')}}">Holatni tekshirish</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('director.add2')}}">Ko'rsatgichlar qo'shish</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('director.employees')}}">Bo'lim xodimlari</a>
                        </li>

                    </ul>
                </div>
            </li>
        @elseif(auth()->user()->role_id == 6)
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#kpi" aria-expanded="false" aria-controls="ui-basic">
                    <i class="menu-icon typcn typcn-coffee"></i>
                    <span class="menu-title">Xodimlar natijalari</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="kpi">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('bugalter.list')}}">Taqsimot holatini ko'rish</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('bugalter.add')}}">Summani kiritish</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('bugalter.check')}}">Holatni tekshirish</a>
                        </li>
                    </ul>
                </div>
            </li>
        @elseif(auth()->user()->role_id == 4)
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#profile" aria-expanded="false"
                   aria-controls="ui-basic">
                    <i class="menu-icon typcn typcn-coffee"></i>
                    <span class="menu-title">Shaxsiy profil</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="profile">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('commission.list')}}">Xodimlar natijalari</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('days.select')}}">Xodimlar ish kunlari</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('commission.section')}}">Bo'limlar ro'yxati</a>
                        </li>
                    </ul>
                </div>
            </li>
        @elseif(auth()->user()->role_id == 7)
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#profile" aria-expanded="false"
                   aria-controls="ui-basic">
                    <i class="menu-icon typcn typcn-coffee"></i>
                    <span class="menu-title">Shaxsiy profil</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="profile">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('commission.section')}}">Bo'limlar ro'yxati</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('commission.list')}}">Xodimlar ro'yxati</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('bugalter.list')}}">Oylik hisobotlar</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif
    </ul>
</nav>
<style>
    .nav-link {
        white-space: normal !important;
    }
</style>

<script>

</script>
