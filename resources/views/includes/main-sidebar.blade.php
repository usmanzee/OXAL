<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('images/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>
            </div>
        </div>
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN MENU</li>
            <li>
                <a href="{{ url('admin') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ url('admin/users') }}">
                    <i class="fa fa-users"></i> <span>Users</span>
                </a>
            </li>
            <li>
                <a href="{{ url('admin/categories') }}">
                    <i class="fa fa-th"></i> <span>Categories</span>
                </a>
            </li>
            <li>
                <a href="{{ url('admin/products') }}">
                    <i class="fa fa-product-hunt"></i> <span>Products(Ads)</span>
                </a>
            </li>
        </ul>
    </section>
</aside>