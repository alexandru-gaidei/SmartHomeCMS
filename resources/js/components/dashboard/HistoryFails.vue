<template>
    <div class="row">
        <h4 class="col-md-12">History, last fails</h4>
        <div class="col-md-12">
            <error-message :error="error" v-if="error"></error-message>
            <table class="table table-sm" v-if="items.length > 0">
                <tr v-for="item in items" v-bind:key="item.id">
                    <td>{{ item.historyable ? item.historyable.name : 'NA' }}</td>
                    <td>{{ item.ocurrence_at }}</td>
                    <td>{{ item.status }}</td>
                    <td>{{ item.data }}</td>
                </tr>
            </table>
            <div v-if="loading" class="mt-3 p-3">
                <font-awesome-icon :icon="['fas', 'spinner']" />
            </div>
        </div>
        <div v-if="!loading && items.length == 0" class="col-md-12 mb-4">
            All good!
        </div>
        <div class="clearfix col-md-12">&nbsp;</div>
    </div>
</template>

<script>
import client from '../../client';
export default {
    data() {
        return {
            echo: null,
            loading: true,
            error: null,
            items: [],
        };
    },
    created() {
        this.fetchData();
        this.connect();
    },
    methods: {
        fetchData() {
            client
                .all(`v1/history?limit=10&status=${process.env.MIX_HISTORY_STATUS_FAIL}`)
                .then(response => {
                    this.loading = false;
                    this.items = response.data.data;
                }).catch(error => {
                    this.loading = false;
                    this.error = error.response.data.message || error.message;
                });
        },
        connect() {
            if(!this.echo){
                this.echo = Echo.channel('history.fail.ocured')
                    .listen('HistoryFailOcured', (data) => {
                        this.fetchData()
                    });
            }
        }
    }
}
</script>