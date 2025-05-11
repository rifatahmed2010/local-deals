@php use Illuminate\Support\Facades\Auth; @endphp
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <a class="nav-link" href="{{route('dashboard')}}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <a class="nav-link" href="{{route('users')}}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    User Management
                </a>

                <a class="nav-link collapsed" style="margin-left:-3px" href="#" data-bs-toggle="collapse" data-bs-target="#collapseProfile" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon">
                        <span class="fa-stack fa-xs">
                          <i class="fa fa-certificate fa-stack-2x"></i>
                          <i class="fa fa-tag fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>
                    Deal Management
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseProfile" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link" href="{{asset('deals')}}">Deal List</a>
                        <a class="nav-link" href="{{asset('deals/create')}}">Add New</a>
                    </nav>
                </div>

                <a class="nav-link" href="{{asset('business_requests')}}">
                    <div class="sb-nav-link-icon"><i class="fa fa-paper-plane"></i></div>
                    Business Request
                </a>

                <a class="nav-link" href="{{asset('contact_informations')}}">
                    <div class="sb-nav-link-icon"><i class="fas fa-message"></i></div>
                    Contact Informations
                </a>

                <a class="nav-link" href="{{asset('push_notifications')}}">
                    <div class="sb-nav-link-icon"><i class="fas fa-message"></i></div>
                    Push Notifications
                </a>

                <a class="nav-link collapsed" style="margin-left:-3px" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAnalytic" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon">
                          <i class="fa fa-bar-chart"></i>
                    </div>
                    Analytics
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <div class="collapse" id="collapseAnalytic" aria-labelledby="headingTwo" data-bs-parent="#analytic">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="analytic">
                        <a class="nav-link" href="{{asset('analytic-deal')}}">Deal Analytic</a>
                        <a class="nav-link" href="{{asset('analytic-user')}}">User Analytic</a>
{{--                        <a class="nav-link" href="{{asset('business-analytic')}}">Business Analytic</a>--}}
                        <a class="nav-link" href="{{asset('chart')}}">Graphical Analytic</a>
                    </nav>
                </div>

            </div>
    </nav>
</div>
