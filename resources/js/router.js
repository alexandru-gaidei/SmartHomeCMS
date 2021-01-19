window.Vue = require('vue');
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Home from './views/pages/Home'
import Login from './views/pages/Login'
import Dashboard from './views/pages/Dashboard'
import NotFound from './views/pages/NotFound'

import GroupsIndex from './views/groups/GroupsIndex'
import GroupsForm from './views/groups/GroupsForm'

import UsersIndex from './views/users/UsersIndex'
import UsersForm from './views/users/UsersForm'

import SensorsIndex from './views/sensors/SensorsIndex'
import SensorsForm from './views/sensors/SensorsForm'
import SensorsView from './views/sensors/SensorsView'

import ActionsIndex from './views/actions/ActionsIndex'
import ActionsForm from './views/actions/ActionsForm'
import ActionsView from './views/actions/ActionsView'

import CamsIndex from './views/cams/CamsIndex'
import CamsForm from './views/cams/CamsForm'
import CamsView from './views/cams/CamsView'

import Health from './views/pages/Health'


const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: Dashboard
        },


        {
            path: '/groups',
            name: 'groups.index',
            component: GroupsIndex
        },
        {
            path: '/groups/create',
            name: 'groups.create',
            component: GroupsForm,
        },
        {
            path: '/groups/:id/edit',
            name: 'groups.edit',
            component: GroupsForm,
        },


        {
            path: '/users',
            name: 'users.index',
            component: UsersIndex
        },
        {
            path: '/users/create',
            name: 'users.create',
            component: UsersForm,
        },
        {
            path: '/users/:id/edit',
            name: 'users.edit',
            component: UsersForm,
        },


        {
            path: '/sensors',
            name: 'sensors.index',
            component: SensorsIndex
        },
        {
            path: '/sensors/create',
            name: 'sensors.create',
            component: SensorsForm,
        },
        {
            path: '/sensors/:id/edit',
            name: 'sensors.edit',
            component: SensorsForm,
        },
        {
            path: '/sensors/:id',
            name: 'sensors.show',
            component: SensorsView,
        },


        {
            path: '/actions',
            name: 'actions.index',
            component: ActionsIndex
        },
        {
            path: '/actions/create',
            name: 'actions.create',
            component: ActionsForm,
        },
        {
            path: '/actions/:id/edit',
            name: 'actions.edit',
            component: ActionsForm,
        },
        {
            path: '/actions/:id',
            name: 'actions.show',
            component: ActionsView,
        },


        {
            path: '/cams',
            name: 'cams.index',
            component: CamsIndex
        },
        {
            path: '/cams/create',
            name: 'cams.create',
            component: CamsForm,
        },
        {
            path: '/cams/:id/edit',
            name: 'cams.edit',
            component: CamsForm,
        },
        {
            path: '/cams/:id',
            name: 'cams.show',
            component: CamsView,
        },

        
        { path: '/health', name: 'health', component: Health },
        { path: '/404', name: '404', component: NotFound },
        { path: '*', redirect: '/404' },
    ],
});

export default {
    router
}
