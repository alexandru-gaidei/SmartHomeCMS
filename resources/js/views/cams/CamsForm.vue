<template>
    <div class="col-md-6 offset-md-3" v-if="this.app.user">
        <error-message :error="error" v-if="error"></error-message>
        <validation-errors :errors="validationErrors" v-if="validationErrors"></validation-errors>
        <success-message :message="message" v-if="message"></success-message>

        <form @submit.prevent="onSubmit($event)">
            <div class="form-group">
                <label for="group_id">Groups</label>
                <select id="group_id" class="form-control" v-model="item.group_id">
                    <option v-for="group in groups" :key="group.id" v-bind:value="group.id">{{ group.name }}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" class="form-control" v-model="item.name" />
            </div>

            <div class="form-group">
                <label for="stream_url">Stream URL</label>
                <input id="stream_url" class="form-control" v-model="item.stream_url" />
            </div>

            <div class="form-group">
                <label>Store</label><br>
                <label><input type="radio" value="0" id="store-0" v-model="item.store" /> NO</label><br>
                <label><input type="radio" value="1" id="store-1" v-model="item.store" /> YES</label>
            </div>

            <div class="form-group" v-if="item.store == 1">
                <label for="length">Segment length</label>
                <input type="number" id="length" class="form-control" v-model="item.length" />
            </div>

            <div class="form-group" v-if="item.store == 1">
                <label for="size_heights">Resolution</label>
                <select id="size_heights" class="form-control" v-model="item.size_height">
                    <option v-for="(item, key) in size_heights" :key="key" v-bind:value="key">{{ item }}</option>
                </select>
            </div>

            <div class="form-group" v-if="item.store == 1">
                <label for="keep_days">Keep records in days (0 for keep infinite)</label>
                <input type="number" id="keep_days" class="form-control" v-model="item.keep_days" />
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
            size_heights: [],
            item: {}
        };
    },
    methods: {
        onSubmit(event) {
            this.saving = true;

            const data = {
                group_id: this.item.group_id,
                name: this.item.name,
                store: this.item.store,
                length: this.item.length,
                size_height: this.item.size_height,
                stream_url: this.item.stream_url,
                keep_days: this.item.keep_days,
            };

            Object.keys(data).forEach((key) => (!data[key] && data[key] != 0) && delete data[key]);

            const req = this.editing 
                ? client.update('v1/cams', this.item.id, data)
                : client.create('v1/cams', data);
            
            req.then((response) => {
                this.item = response.data.data;
                setTimeout(() => this.message = null, 3000);

                if(this.editing) {
                    this.message = 'Camera updated, please run cam-servers.sh restart';
                }
                else {
                    this.message = 'Camera created, please run cam-servers.sh restart';
                    this.editing = true;
                    this.$router.push({ name: 'cams.edit', params: { id: this.item.id } });
                }
                this.error = null;
                this.validationErrors = null;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
                if (error.response.status == 422){
                    this.validationErrors = error.response.data.errors;
                }
            }).then(_ => this.saving = false);
        },
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
            client.find('v1/cams', this.$route.params.id).then((response) => {
                this.loaded = true;
                this.item = response.data.data;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            });
        }

        client.secureClient().get('v1/cams/metadata').then((response) => {
            this.loaded = true;
            this.size_heights = response.data.size_heights;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });
    }
};
</script>