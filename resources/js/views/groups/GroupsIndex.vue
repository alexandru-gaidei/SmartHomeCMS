<template>
    <div v-if="this.app.user" class="items">
        <error-message :error="error" v-if="error"></error-message>

        <h3 class="float-left">
            Groups&nbsp;
            <small>
                <router-link :to="{ name: 'groups.create' }"><font-awesome-icon :icon="['fas', 'plus-circle']" /></router-link>
            </small>
        </h3>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>name</th>
                        <th v-if="app.user.is_admin"></th>
                    </tr>
                </thead>
                <tbody v-if="items">
                    <tr v-for="item in items" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td>{{ item.name }}</td>
                        <td class="text-center" v-if="app.user.is_admin">
                            <router-link class="btn btn-primary btn-sm" :to="{ name: 'groups.edit', params: { id: item.id } }">
                                <font-awesome-icon :icon="['fas', 'pencil-alt']" />
                            </router-link>
                            <button class="btn btn-danger btn-sm" @click.prevent="deleteItem(item.id)">
                                <font-awesome-icon :icon="['fas', 'trash-alt']" />
                            </button>
                        </td>
                    </tr>
                    <tr v-if="items.length == 0"><td :colspan="app.user.is_admin ? 3 : 2">No entries</td></tr>
                </tbody>
            </table>
            <pagination :data="data" @pagination-change-page="fetchData"></pagination>
            <div v-if="loading" class="mb-3 mt-3 p-3">
                <font-awesome-icon :icon="['fas', 'spinner']" />
            </div>
        </div>
    </div>
</template>

<script>
import client from '../../client';
import Swal from 'sweetalert2'

export default {
    props: ['app'],
    data() {
        return {
            loading: false,
            items: null,
            data: {},
            page: null,
            error: null,
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData(page = this.$route.query.page || 1) {
            this.error = null;
            this.loading = true;
            this.page = page;
            this.$router.push({query: {page: this.page}}).catch(() => {});
            client
                .all(`v1/groups?page=${this.page}`)
                .then(response => {
                    this.loading = false;
                    this.data = response.data;
                    this.items = response.data.data;
                }).catch(error => {
                    this.loading = false;
                    this.error = error.response.data.message || error.message;
                });
        },
        deleteItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    this.error = null;
                    this.loading = true;
                    client
                        .delete('v1/groups', id)
                        .then(response => {
                            this.loading = false;
                            this.fetchData(this.page);
                        }).catch(error => {
                            this.loading = false;
                            this.error = error.response.data.message || error.message;
                        });

                    Swal.fire({
                        title: 'Deleted!',
                        html: 'Group has been deleted.',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                    })
                }
            });
        }
    }
}
</script>