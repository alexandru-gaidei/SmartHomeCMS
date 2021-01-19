<template>
    <div class="col-md-4 offset-md-4">
        <div v-if="error" class="error">
            <div class="alert alert-danger" role="alert">{{ error }}</div>
        </div>

        <form @submit.prevent="onSubmit($event)">
            <div class="form-group">
                <label for="user_email">Email</label>
                <input id="user_email" class="form-control" type="email" v-model="user.email" />
            </div>
            <div class="form-group">
                <label for="user_password">Password</label>
                <input id="user_password" class="form-control" type="password" v-model="user.password" />
            </div>
            <div>&nbsp;</div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" :disabled="loading">Login</button>
            </div>
        </form>
    </div>
</template>

<script>
import api from '../../client';

export default {
    props: ['app'],
    data() {
        return {
            error: null,
            loading: false,
            user: {
              email: null,
              password: null,
            }
        };
    },
    methods: {
        onSubmit(event) {
            this.loading = true;

            api.client().post('v1/oauth/token', {
				username: this.user.email,
				password: this.user.password,
				grant_type: 'password',
				client_id: this.app.client_id,
				client_secret: this.app.client_secret
            }).then((response) => {
                this.app.access_token = response.data.access_token;
                this.app.token_type = response.data.token_type;
                localStorage.setItem('access_token', this.app.access_token)
                localStorage.setItem('token_type', this.app.token_type)

                api.secureClient().get('v1/users/my')
                .then((response) => {
                    this.app.user = response.data.data;
                    localStorage.setItem('user', JSON.stringify(this.app.user));
                    this.$router.push({ name: 'dashboard'});
                }).catch(error => {
                    this.error = error.response.data.message || error.message;
                });

            }).catch(error => {
                this.error = error.response.data.message || error.message;
            }).then(_ => this.loading = false);
        }
    }
};
</script>