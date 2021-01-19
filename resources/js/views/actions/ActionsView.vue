<template>
    <div class="col-md-8 offset-md-2" v-if="this.app.user">
        <error-message :error="error" v-if="error"></error-message>

        <h3>{{ item.name || "&nbsp;" }} <small class="float-right">{{ item.sensor ? item.sensor.name : "" }}</small></h3>
        <table class="table table-sm">
        <tbody>
            <tr>
                <th scope="row">Value type</th>
                <td>{{ item.value_type }}</td>
            </tr>
            <tr>
                <th scope="row">Type</th>
                <td>{{ item.type }}</td>
            </tr>
            <tr>
                <th scope="row">Subject</th>
                <td>{{ item.subject }}</td>
            </tr>
        </tbody>
        </table>
        <hr>
        <router-link v-if="app.user.is_admin" class="btn btn-primary btn-sm" :to="{ name: 'actions.edit', params: { id: item.id } }">Edit</router-link>
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
            item: {}
        };
    },
    created() {
        client.find('v1/actions', this.$route.params.id).then((response) => {
            this.loaded = true;
            this.item = response.data.data;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });
    }
};
</script>