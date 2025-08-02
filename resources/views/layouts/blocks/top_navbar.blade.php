<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <h2 style="font-size: 22px; color: white; margin: 0!important;">O'ZAGROINSPEKSIYA</h2>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center">
        <ul class="navbar-nav">
            <li class="nav-item font-weight-semibold">
                <i class="fa fa-bars" id="toggle-icon"></i>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            @auth
            <li class="nav-item">
                {{ auth()->user()->first_name. ' '.auth()->user()->last_name }}
            </li>
            <li class="nav-item d-none d-xl-inline-block">
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-logout"></i>Chiqish</button>
                </form>
            </li>
            @endauth
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
