<template>
<div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <router-link class="navbar-brand" :to="{ name: 'home' }">BRAND</router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul v-if="user" class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'dashboard' }">Dashboard</router-link>
                    </li>
                    <li class="nav-item" v-if="user && user.is_admin">
                        <router-link class="nav-link" :to="{ name: 'groups.index' }">Groups</router-link>
                    </li>
                    <li class="nav-item" v-if="user && user.is_admin">
                        <router-link class="nav-link" :to="{ name: 'users.index' }">Users</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'sensors.index' }">Sensors</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'actions.index' }">Actions</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'cams.index' }">Cameras</router-link>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li v-if="!user" class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'login' }">Login</router-link>
                    </li>
                    <li v-if="user" class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'health' }">
                            <span class="badge badge-primary">services</span>
                        </router-link>
                    </li>
                    <li v-if="user" class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ user.email }} <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#" @click.prevent="logout()">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div>&nbsp;</div>
    <div class="container">
        <router-view :app="this"></router-view>
    </div>
</div>
</template>

<script>
    import api from '../client';
    import toastr from 'toastr'

    export default {
        data() {
            return {
                echo: null,
                client_id: process.env.MIX_PASSPORT_CLIENT_ID,
                client_secret: process.env.MIX_PASSPORT_CLIENT_SECRET,
                access_token: localStorage.getItem('access_token') || null,
                token_type: localStorage.getItem('token_type') || null,
                user: JSON.parse(localStorage.getItem('user')) || null
            };
        },
        created() {
            this.connect();
        },
        methods: {
            logout() {
                api.secureClient().post('v1/users/logout')
                .then((response) => {
                    this.user = null;
                    localStorage.removeItem('user');
                    this.$router.push({ name: 'home'});
                }).catch(error => {
                    if (error.response.status == 401){
                        localStorage.removeItem('user');
                        this.$router.push({ name: 'home'});
                        location.reload();
                    }
                });
            },
            connect() {
                if(!this.echo && this.user){
                    this.echo = Echo.channel('action.ui.notify.on.group')
                        .listen('ActionUINotify', (data) => {

                            if(!this.user.groups.includes(data.data.group_id)) {
                                return;
                            }

                            toastr.options.closeButton = true;
                            toastr.options.newestOnTop = false;
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.options.timeOut = 0;
                            toastr.options.extendedTimeOut = 0;
                            toastr.info(
                                `${data.data.datetime}<br>
                                ${data.data.action_name} on sensor ${data.data.sensor.name}<br>
                                Value: ${parseFloat(data.data.sensor.value).toFixed(2)} 
                                (${parseFloat(data.data.sensor.min_value).toFixed(2)} - ${parseFloat(data.data.sensor.max_value).toFixed(2)})`
                            )
                        });
                }
            },
        }
    }
</script>