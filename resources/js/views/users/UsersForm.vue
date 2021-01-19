<template>
    <div class="col-md-6 offset-md-3" v-if="this.app.user">
        <error-message :error="error" v-if="error"></error-message>
        <validation-errors :errors="validationErrors" v-if="validationErrors"></validation-errors>
        <success-message :message="message" v-if="message"></success-message>

        <form @submit.prevent="onSubmit($event)">
            <div class="form-group">
                <label for="user_name">Name</label>
                <input id="user_name" class="form-control" v-model="user.name" />
            </div>
            <div class="form-group">
                <label for="user_email">Email</label>
                <input id="user_email" class="form-control" type="email" v-model="user.email" />
            </div>
            <div class="form-group" v-if="!editing">
                <label for="user_password">Password</label>
                <input id="user_password" class="form-control" type="password" v-model="user.password" />
            </div>
            <div class="form-group">
                <label for="user_groups">Groups</label>
                <select id="user_groups" class="form-control" v-model="user.groups" multiple>
                    <option v-for="group in groups" :key="group.id" v-bind:value="group.id">{{ group.name }}</option>
                </select>
            </div>
            <div>&nbsp;</div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" :disabled="saving">{{ editing ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</template>

<script>
import client from '../../client';

export default {
    props: ['app'],
    data() {
        return {
            editing: false,
            error: null,
            validationErrors: null,
            message: null,
            loaded: false,
            saving: false,
            groups: [],
            user: {
                id: null,
                name: "",
                email: "",
                password: "",
                groups: []
            }
        };
    },
    methods: {
        onSubmit(event) {
            this.saving = true;

            const req = this.editing 
                ? client.update('v1/users', this.user.id, {
                    name: this.user.name,
                    email: this.user.email,
                    groups: this.user.groups,
                })
                : client.create('v1/users', {
                    name: this.user.name,
                    email: this.user.email,
                    password: this.user.password,
                    groups: this.user.groups,
                });
            
            req.then((response) => {
                this.user = response.data.data;
                setTimeout(() => this.message = null, 3000);

                if(this.editing) {
                    this.message = 'User updated';
                }
                else {
                    this.message = 'User created';
                    this.editing = true;
                    this.$router.push({ name: 'users.edit', params: { id: this.user.id } });
                }
                this.error = null;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
                if (error.response.status == 422){
                    this.validationErrors = error.response.data.errors;
                }
            }).then(_ => this.saving = false);
        }
    },
    created() {
        this.editing = typeof this.$route.params.id !== 'undefined';

        client.all('v1/groups').then((response) => {
            this.loaded = true;
            this.groups = response.data.data;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });

        if(this.editing) {
            client.find('v1/users', this.$route.params.id).then((response) => {
                this.loaded = true;
                this.user = response.data.data;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            });
        }
    }
};
</script>