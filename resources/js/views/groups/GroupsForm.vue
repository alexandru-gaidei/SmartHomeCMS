<template>
    <div class="col-md-6 offset-md-3" v-if="this.app.user">
        <error-message :error="error" v-if="error"></error-message>
        <validation-errors :errors="validationErrors" v-if="validationErrors"></validation-errors>
        <success-message :message="message" v-if="message"></success-message>

        <form @submit.prevent="onSubmit($event)">
            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" class="form-control" v-model="item.name" />
            </div>
            <div>&nbsp;</div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" v-loader="{ loading: saving }">{{ editing ? 'Update' : 'Create' }}</button>
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
            item: {
                id: null,
                name: "",
            }
        };
    },
    methods: {
        onSubmit(event) {
            this.saving = true;

            const req = this.editing 
                ? client.update('v1/groups', this.item.id, {
                    name: this.item.name
                })
                : client.create('v1/groups', {
                    name: this.item.name
                });
            
            req.then((response) => {
                this.item = response.data.data;
                setTimeout(() => this.message = null, 3000);

                if(this.editing) {
                    this.message = 'Group updated';
                }
                else {
                    this.message = 'Group created';
                    this.editing = true;
                    this.$router.push({ name: 'groups.edit', params: { id: this.item.id } });
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
            client.find('v1/groups', this.$route.params.id).then((response) => {
                this.loaded = true;
                this.item = response.data.data;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            });
        }
    }
};
</script>