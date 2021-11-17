
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import Router from 'vue-router'


import { store } from './store'
window.Vue = require('vue');
window.Vue.use(Router);
Vue.prototype.$userToken = document.querySelector("meta[name='user-token']").getAttribute('content');

import Home from './components/Home.vue';
import AddDocument from './components/AddDocuement';
import AHome from './components/Admin/Home';
import Addnew from './components/Admin/Addnew';
import Updateold from './components/Admin/Updateold';
/* User management*/
import UserManager from './components/Admin/UserManagement';
import OwnerManager from './components/Admin/OwnerManagement';
import OwnerNotice from './components/OwnerNotice';
import ChangeRequest from './components/ChangeRequest';
import AccountSettings from './components/AccountSettings';
/*Search */
import SearchResult from './components/SearchResult';

let routes = [
  {
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/new-document',
    name: 'addnewdocument',
    component: AddDocument,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/make-change-request/:id',
    name: 'CR',
    component: ChangeRequest,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/notice',
    name: 'Notice',
    component: OwnerNotice,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/settings',
    name: 'ASettings',
    component: AccountSettings,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/search/:q',
    name: 'searchresult',
    component: SearchResult,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/admin',
    name: 'AHome',
    component: AHome,
    children: [
      {
        path: 'add-new',
        name: 'Addnew',
        component: Addnew,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'update-old',
        name: 'Updateold',
        component: Updateold,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'user-management',
        name: 'Usermanager',
        component: UserManager,
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'owner-management',
        name: 'Ownermanager',
        component: OwnerManager,
        meta: {
          requiresAuth: true
        }
      }
    ]
  },

]

let router = new Router({
  mode: 'history',
  routes
})

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (Vue.prototype.$userToken == 0) {
      window.location.href = '/';
    } else if (Vue.prototype.$userToken == 'admin') {
      window.location.href = '/admin/login';
    } else {
      next();
    }
  } else {
    next();
  }
})


const app = new Vue({
  store: store,
  el: '#app',
  router
});
