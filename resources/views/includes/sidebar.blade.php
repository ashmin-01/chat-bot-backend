<div class="sidebar">
    <!--
      Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red"
  -->
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="javascript:void(0)" class="simple-text logo-mini">
                SC
            </a>
            <a href="javascript:void(0)" class="simple-text logo-normal">
                Syriatel Chatbot
            </a>
        </div>
        <ul class="nav">
            <!-- Dashboard -->
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ url('/dashboard') }}">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- Typography (Vectorstore) -->
            <li class="{{ request()->is('typography') ? 'active' : '' }}">
                <a href="{{ url('/typography') }}">
                    <i class="tim-icons icon-align-center"></i>
                    <p>Vectorstore</p>
                </a>
            </li>

            <!-- Icons (APIs) -->
            <li class="{{ request()->is('icons') ? 'active' : '' }}">
                <a href="{{ url('/icons') }}">
                    <i class="tim-icons icon-atom"></i>
                    <p>MODEL CONFIGURATIONS</p>
                </a>
            </li>

            <!-- Map (Variables) -->
            <li class="{{ request()->is('map') ? 'active' : '' }}">
                <a href="{{ url('/map') }}">
                    <i class="tim-icons icon-pin"></i>
                    <p>PROMPT TEMPLATE</p>
                </a>
            </li>

            <!-- UPLOAD FILES -->
            <li class="{{ request()->is('user') ? 'active' : '' }}">
                <a href="{{ url('/user') }}">
                    <i class="tim-icons icon-single-02"></i>
                    <p>UPLOAD FILES</p>
                </a>
            </li>

            <!-- Tables (Feedback) -->
            <li class="{{ request()->is('tables') ? 'active' : '' }}">
                <a href="{{ url('/tables') }}">
                    <i class="tim-icons icon-puzzle-10"></i>
                    <p>Feedback</p>
                </a>
            </li>

            <!-- Logout -->
            <li>
                <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="tim-icons icon-button-power"></i>
                    <p>Logout</p>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>
