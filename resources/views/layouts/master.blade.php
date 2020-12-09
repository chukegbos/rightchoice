<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $setting->sitename }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  </head>

  <body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
          <!--<li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="fa fa-bell"></i>
              <small>
                <span class="badge badge-warning navbar-badge">{{ $count_unviewed }}</span>
              </small>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              
              <span class="dropdown-item dropdown-header">
                {{ $count_unviewed }} Notification(s)
              </span>
              
              @forelse($unviewed as $view)
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="fas fa-envelope mr-2"></i> {{ $view->purpose}}
                  <span class="float-right text-muted text-sm">
                    {{ \Carbon\Carbon::createFromTimeStamp(strtotime($view->created_at))->diffForHumans() }}
                  </span>
                </a>
              @empty
              @endforelse
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
          </li>-->
         
          <li class="nav-item">
            <router-link to="/sale/shopping-cart" class="nav-link">
                <i class="fa fa-shopping-cart"></i>
                <small>
                  <span class="badge badge-danger navbar-badge">
                    {{ Session::has('cart') ? Session::get('cart')->totalQty : '' }}
                  </span>
                </small>
            </router-link>
          </li>
        </ul>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ url('/home') }}" class="brand-link" style="background: white">
          <img src="{{ asset('img/logo/logo_white.png') }}" alt="AdminLTE Logo" class="brand-image img-circle">
          <span class="brand-text" style="color: #264a9f; font-weight: bolder;">RIGHTCHOICE</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
              <h5>{{ Auth::user()->name }}</h5>
              <p>
                {{ $my_store->name }}
              </p>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

              <li class="nav-item">
                <router-link to="/home" class="nav-link">
                  <i class="nav-icon fa fa-th"></i>
                  <p>Dashboard</p>
                </router-link>
              </li>

              @if(Auth::user()->role==1)
                <li class="nav-item">
                  <router-link to="/store/trade/{{ $my_store->code }}" class="nav-link">
                    <i class="nav-icon fa fa-product-hunt"></i>
                    <p>Shop Stock</p> 
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link to="/admin/customers" class="nav-link">
                    <i class="nav-icon fa fa-users"></i>
                    <p>Customers</p>
                  </router-link>
                </li>
              @elseif(Auth::user()->role==2)
                <li class="nav-item">
                  <router-link to="/admin/cashiers" class="nav-link">
                    <i class="nav-icon fa fa-user"></i>
                    <p>Cashiers</p> 
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link to="/admin/customers" class="nav-link">
                    <i class="nav-icon fa fa-users"></i>
                    <p>Customers</p>
                  </router-link>
                </li>

                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-product-hunt"></i>
                    <p>
                      Shop Stock
                      <i class="right fa fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <router-link to="/store/trade/{{ $my_store->code }}" class="nav-link ml-3">
                        <!--{{ $my_store->name }}--> Shop Stock
                      </router-link>

                      <!--<router-link to="/store/inventory" class="nav-link ml-3">
                         Shop Stock
                      </router-link>-->
                    </li>

                    <li class="nav-item">
                      <router-link to="/store/request" class="nav-link ml-3">
                        Product Request
                      </router-link>
                    </li>
                  </ul>
                </li>
              @elseif(Auth::user()->role==3)
                <li class="nav-item">
                  <router-link to="/admin/stores" class="nav-link">
                    <i class="nav-icon fa fa-home"></i>
                    <p>Outlets</p>
                    <span class="badge badge-info pull-right">{{ $all_stores }}</span>
                  </router-link>
                </li>

                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-user"></i>
                    <p>
                      Staff
                      <i class="right fa fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <router-link to="/admin/cashiers" class="nav-link ml-3">
                        Cashiers
                      </router-link>
                    </li>

                    <li class="nav-item">
                      <router-link to="/admin/store-manager" class="nav-link ml-3">
                       Store Managers
                      </router-link>
                    </li>

                    <li class="nav-item">
                      <router-link to="/admin/admins" class="nav-link ml-3">
                        Admins
                      </router-link>
                    </li>
                  </ul>
                </li>

              

                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-product-hunt"></i>
                    <p>
                      Inventory
                      <i class="right fa fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <router-link to="/admin/inventory/category" class="nav-link ml-3">
                        Product Category
                      </router-link>
                    </li>

                   <li class="nav-item">
                      <router-link to="/admin/inventory" class="nav-link ml-3">
                        Company Stock
                      </router-link>
                    </li>

                  </ul>
                </li>

                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-truck"></i>
                    <p>
                      Utilities
                      <i class="right fa fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <router-link to="/admin/customers" class="nav-link ml-3">
                        Customers
                      </router-link>
                    </li>

                    <li class="nav-item">
                      <router-link to="/admin/suppliers" class="nav-link ml-3">
                       Suppliers
                      </router-link>
                    </li>
                  </ul>
                </li>

                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-paper-plane"></i>
                    <p>
                      Requests
                      <i class="right fa fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <router-link to="/store/request" class="nav-link ml-3">
                        Outlet Request
                      </router-link>
                    </li>
                  </ul>
                </li>

                <li class="nav-item">
                  <router-link to="/admin/setting" class="nav-link">
                    <i class="nav-icon fa fa-cogs"></i>
                    <p>App Setting</p>
                  </router-link>
                </li>

              @endif

                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-book"></i>
                    <p>
                      Accounts
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <router-link to="/admin/report" class="nav-link">
                        <p class="ml-2">Sales History</p>
                      </router-link>
                    </li>

                    <li class="nav-item">
                      <router-link to="/store/debtors" class="nav-link">
                        <p class="ml-2">Debtors</p> 
                        <span class="badge badge-info pull-right">{{ $all_depts }}</span>
                      </router-link>
                    </li>

                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <p>
                          Income
                          <i class="right fas fa-angle-left"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <router-link to="/admin/account/income/category" class="nav-link">
                            Income Category
                          </router-link>
                        </li>
                        <li class="nav-item">
                          <router-link to="/admin/account/income" class="nav-link">
                            Income Statement
                          </router-link>
                        </li>
                      </ul>
                    </li>

                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <p class="ml-2">Expenditure
                          <i class="right fas fa-angle-left"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <router-link to="/admin/account/expenditure/category" class="nav-link">
                            Add Category
                          </router-link>
                        </li>
                        <li class="nav-item">
                          <router-link to="/admin/account/expenditure" class="nav-link">
                            View Expenditure
                          </router-link>
                        </li>
                      </ul>
                    </li>

                    <li class="nav-item">
                      <router-link to="/admin/account/balance" class="nav-link">
                        <p class="ml-2">Balance Sheet</p>
                      </router-link>
                    </li>
                    @if(Auth::user()->role==3)
                      <li class="nav-item">
                        <router-link to="/admin/purchases" class="nav-link">
                          <p class="ml-2">Purchases</p>
                        </router-link>
                      </li>
                    @endif
                  </ul>
                </li> 
              <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                  <i class="nav-icon fa fa-power-off text-red"></i>
                  <p>{{ __('Logout') }}</p>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              </li>
            </ul>
          </nav>

        </div>
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper pt-4">
        <router-view></router-view>
        <vue-progress-bar></vue-progress-bar>
      </div>
 
      <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="{{ url('/') }}">{{ $setting->sitename }}</a>.</strong> 
      </footer>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
  </body>
</html>
