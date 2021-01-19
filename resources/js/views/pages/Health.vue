<template>
    <div class="col-md-4 offset-md-4">
        <div v-if="error" class="error">
            <div class="alert alert-danger" role="alert">{{ error }}</div>
        </div>

        <div v-if="loading" class="mb-3 mt-3 p-3 text-center">
            <font-awesome-icon :icon="['fas', 'spinner']" />
        </div>

        <table class="table table-hover" v-if="data">
            <tr>
                <td class="label"><span data-toggle="tooltip" data-placement="left" :title="data.main.info[data.main.status]"><strong>General</strong></span></td>
                <td>{{data.main.status}}</td>
                <td>
                    <font-awesome-icon v-if="data.main.status == 'OK'" :icon="['fas', 'check']" class="text-success"/>
                    <font-awesome-icon v-else :icon="['fas', 'times']" class="text-danger"/>
                </td>
            </tr>
            <tr v-for="(item, key) in data.items" :key="key">
                <td class="label"><span data-toggle="tooltip" data-placement="left" :title="item.info[item.status]">{{key}}</span></td>
                <td>{{item.status}}</td>
                <td>
                    <font-awesome-icon v-if="item.status == 'OK'" :icon="['fas', 'check']" class="text-success"/>
                    <font-awesome-icon v-else :icon="['fas', 'times']" class="text-danger"/>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import api from '../../client';

export default {
    props: ['app'],
    data() {
        return {
            error: null,
            loading: true,
            data: null
        };
    },
    mounted() {
        api.secureClient().get('v1/health/check').then((response) => {
                this.data = response.data;
                Vue.nextTick(function () {
                    $('[data-toggle="tooltip"]').tooltip();
                });
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            }).then(_ => this.loading = false);
    }
};
</script>

<style scoped>
.label {
    cursor: help;
}
</style>