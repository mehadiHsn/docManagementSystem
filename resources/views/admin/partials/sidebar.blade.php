<ul class="nav flex-column nav-pills nav-fill">
    <li class="custom-nav nav-item">
        <router-link to="/admin" class="nav-link"> Home</router-link>
    </li>
    <li class="custom-nav nav-item">
        <router-link :to="{name:'Addnew'}" class="nav-link"> Add New Attributes</router-link>
    </li>
    <li class="custom-nav nav-item">
        <router-link :to="{name:'Updateold'}" class="nav-link"> Update Attributes</router-link>
    </li>
    <li class="custom-nav nav-item">
        <router-link :to="{name:'Usermanager'}" class="nav-link"> User Management</router-link>
    </li>
    <li class="custom-nav nav-item">
        <router-link :to="{name:'Ownermanager'}" class="nav-link"> Owner Management</router-link>
    </li>
   <!--  <li class="custom-nav nav-item">
        <a href="{{route('admin')}}" class="nav-link">Reload</a>
    </li> -->
</ul>