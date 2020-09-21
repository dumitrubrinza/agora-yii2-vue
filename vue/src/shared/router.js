import Vue from 'vue'
import Router from 'vue-router'
import DefaultLayout from "./../core/components/layout/DefaultLayout/DefaultLayout";
import AuthLayout from "./../core/components/layout/AuthLayout/AuthLayout";
import NotFoundComponent from "./../core/components/pages/NotFoundComponent";
import Dashboard from "@/modules/Dashboard/Dashboard";
import Login from "@/modules/Auth/Login";
import Register from "@/modules/Auth/Register";
import Setup from "@/modules/setup/Setup";
import CountryList from "@/modules/setup/countries/CountryList";
import Orgchart from "../modules/OrgChart/Orgchart";

Vue.use(Router);

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/orgchart',
      name: 'orgchart',
      component: Orgchart,
      children: []
    },
    {
      path: '/auth',
      name: 'auth',
      redirect: '/login',
      component: AuthLayout,
      children: [
        {
          path: 'login',
          component: Login,
        },
        {
          path: 'register',
          component: Register,
        }
      ]
    },
    {
      path: '/login',
      redirect: '/auth/login'
    },
    {
      path: '/',
      redirect: '/dashboard',
      component: DefaultLayout,
      children: [
        {path: 'dashboard', name: 'dashboard', component: Dashboard,},
        {path: '/setup', name: 'setup', component: Setup,},
        {path: '/setup/countries', name: 'countries', component: CountryList}
      ]
    },
    {
      path: '*',
      component: NotFoundComponent,
      children: []
    }
  ]
});

// router.beforeEach((to, from, next) => {
//   if (to.matched.some(record => record.meta.requiresAuth)) {
//     // this route requires auth, check if logged in
//     // if not, redirect to login page.
//     if (!auth.loggedIn()) {
//       next({path: '/login'})
//     } else {
//       next()
//     }
//   } else if (to.matched.some(record => record.meta.guest) && auth.loggedIn()) {
//     next({path: '/'})
//   } else {
//     next() // make sure to always call next()!
//   }
// });

export default router;
