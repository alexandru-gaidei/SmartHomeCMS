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
            <div v-if="item.is_favorite" class="form-group">
                <label for="favorite_name">Favorite Name</label>
                <input id="favorite_name" class="form-control" v-model="item.favorite_name" />
            </div>

            <div class="form-group">
                <label for="sensor">Sensor</label>
                <select id="sensor" class="form-control" v-model="item.sensor_id">
                    <option v-for="sensor in sensors" :key="sensor.id" v-bind:value="sensor.id">{{ sensor.name }}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="value_type">Value type</label>
                <select id="value_type" class="form-control" v-model="item.value_type">
                    <option v-for="(item, key) in valTypes" :key="key" v-bind:value="key">{{ item }}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" class="form-control" v-model="item.type">
                    <option v-for="(item, key) in types" :key="key" v-bind:value="key">{{ item }}</option>
                </select>
            </div>

            <div class="form-group" v-if="item.type == typeHttpGet">
                <label for="subject">Subject</label>
                <input id="subject" class="form-control" v-model="item.subject" />
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
            typeHttpGet: process.env.MIX_ACTION_TYPE_HTTP_GET,
            editing: false,
            error: null,
            validationErrors: null,
            message: null,
            loaded: false,
            saving: false,
            sensors: [],
            valTypes: [],
            types: [],
            item: {}
        };
    },
    methods: {
        onSubmit(event) {
            this.saving = true;

            const data = {
                sensor_id: this.item.sensor_id,
                name: this.item.name,
                favorite_name: this.item.favorite_name,
                value_type: this.item.value_type,
                type: this.item.type,
                subject: this.item.subject,
                is_favorite: this.item.is_favorite,
            };

            Object.keys(data).forEach((key) => (!data[key]) && delete data[key]);

            const req = this.editing 
                ? client.update('v1/actions', this.item.id, data)
                : client.create('v1/actions', data);
            
            req.then((response) => {
                this.item = response.data.data;
                setTimeout(() => this.message = null, 3000);

                if(this.editing) {
                    this.message = 'Sensor action updated';
                }
                else {
                    this.message = 'Sensor action created';
                    this.editing = true;
                    this.$router.push({ name: 'actions.edit', params: { id: this.item.id } });
                }
                this.error = null;
                this.validationErrors = null;
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

        client.all('v1/sensors').then((response) => {
            this.sensors = response.data.data;
            this.loaded = true;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });

        if(this.editing) {
            client.find('v1/actions', this.$route.params.id).then((response) => {
                this.loaded = true;
                this.item = response.data.data;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            });
        }

        client.secureClient().get('v1/actions/metadata').then((response) => {
            this.loaded = true;
            this.valTypes = response.data.val_types;
            this.types = response.data.types;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });
    }
};
</script>