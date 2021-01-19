<template>
    <div class="col-md-12" v-if="this.app.user">
        <error-message :error="error" v-if="error"></error-message>

        <cam-live-record v-if="loaded && item.id" :cam="item"></cam-live-record>

        <h3>{{ item.name }}</h3>
        <table class="table table-sm">
        <tbody>
            <tr>
                <th scope="row">Stream</th>
                <td>>>{{ item.stream_url }}</td>
            </tr>
            <tr>
                <th scope="row">Store</th>
                <td>{{ item.store ? 'YES' : 'NO' }}</td>
            </tr>
            <tr v-if="item.store">
                <th scope="row">Segment length in seconds</th>
                <td>{{ item.length }}</td>
            </tr>
            <tr v-if="item.store">
                <th scope="row">Resolution</th>
                <td>{{ item.size_height }}p</td>
            </tr>
            <tr v-if="item.store">
                <th scope="row">Keep videos in days</th>
                <td>{{ item.keep_days ? item.keep_days : 'INFINITE' }}</td>
            </tr>
            <tr v-if="item.store">
                <th scope="row">PID</th>
                <td>{{ item.pid }}</td>
            </tr>
            <tr v-if="item.store">
                <th scope="row">Recording status</th>
                <td>
                    {{ item.is_rec ? 'OK' : 'STOPPED' }} |
                    <a href="#" :hidden="item.is_rec" @click.prevent="startRecord()">Start recording</a>
                    <a href="#" :hidden="!item.is_rec" @click.prevent="stopRecord()">Stop recording</a>
                </td>
            </tr>
        </tbody>
        </table>
        <hr>
        <router-link v-if="this.app.user.is_admin" class="btn btn-primary btn-sm" :to="{ name: 'cams.edit', params: { id: item.id } }">Edit</router-link>
        
        <div v-if="records.length > 0" class="records">
            <hr>
            <div class="row">
                <div v-for="record in records" :key="record.id" class="col-md-3 mb-4">
                    <cam-record :url="record.url" :name="record.created_at" :key="record.id" :inList="true" :width="'240px'" :height="'200px'"></cam-record>
                </div>
            </div>
            <hr>
            <pagination :data="data" @pagination-change-page="fetchData" :limit="9"></pagination>
        </div>
    </div>
</template>

<script>
import client from '../../client';

export default {
    props: ['app'],
    data() {
        return {
            error: null,
            loaded: false,
            item: {},
            records: [],
            data: {},
            page: null,
        };
    },
    created() {
        client.find('v1/cams', this.$route.params.id).then((response) => {
            this.item = response.data.data;
            this.loaded = true;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });

        this.fetchData();
    },
    methods: {
        fetchData(page = 1) {
            this.page = page;
            client.secureClient().get(`v1/cams/${this.$route.params.id}/records?page=${this.page}`).then((response) => {
                this.loaded = true;
                this.records = response.data.data;
                this.data = response.data;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            });
        },
        startRecord() {
            client.secureClient().get(`v1/cams/${this.item.id}/recording/start`).then((response) => {
                this.item.is_rec = true;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            });
        },
        stopRecord() {
            client.secureClient().get(`v1/cams/${this.item.id}/recording/stop`).then((response) => {
                this.item.is_rec = false;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            });
        }
    }
};
</script>

<style scoped>
.video-player {
    display: inline-block;
}
</style>