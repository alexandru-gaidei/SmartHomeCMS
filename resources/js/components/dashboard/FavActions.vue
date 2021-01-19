<template>
    <div>
        <error-message :error="error" v-if="error"></error-message>
        <div class="row">
            <h4 class="col-md-12">Favorite actions</h4>
        </div>
        <draggable class="row" v-model="items" group="sensors" @end="end">
            <div v-for="item in items" :key="item.id" class="col-md-2  col-4 mb-4">
                <button type="button" :disabled="saving" @click.prevent="doAction(item.id)" class="btn btn-primary btn-lg w-100">
                    <div class="name">{{item.name}}</div>
                    <div class="sensor">{{item.favoriteable.sensor.name}}</div>
                </button>
            </div>
            <div v-if="!loading && items.length == 0" class="col-md-12 mb-4">
                No favorite items found, go to actions listing and set some favorites.
            </div>
        </draggable>
        <div v-if="loading" class="mb-3 mt-3 p-3">
            <font-awesome-icon :icon="['fas', 'spinner']" />
        </div>
        <div class="clearfix col-md-12">&nbsp;</div>
    </div>
</template>

<script>
import client from '../../client';
import Swal from 'sweetalert2'
import draggable from 'vuedraggable'

export default {
    components: {
        draggable,
    },
    data() {
        return {
            loading: true,
            saving: false,
            error: null,
            items: [],
        };
    },
    created() {
        client
            .all(`v1/favorite?type=${process.env.MIX_FAVORITE_TYPE_ACTION}`)
            .then(response => {
                this.loading = false;
                this.items = response.data.data;
            }).catch(error => {
                this.loading = false;
                this.error = error.response.data.message || error.message;
            });
    },
    methods: {
        doAction(id) {
            this.saving = true;
            client.secureClient()
                .post(`v1/favorite/do/${id}/action`)
                .then(response => {
                    this.saving = false;

                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Action has been triggered.',
                        showConfirmButton: false,
                        toast: true,
                        timer: 3000
                    });

                }).catch(error => {
                    this.error = error.response.data.message || error.message;
                    this.saving = false;
                });
        },
        end(e) {
            client.secureClient()
                .post('v1/favorite/reorder', this.items)
                .catch(error => {
                    this.error = error.response.data.message || error.message;
                });
        }
    }
}
</script>

<style scoped>
.name {
    font-size: 16px;
}
.sensor {
    font-size: 12px;
}

@media (max-width: 576px) {
    .name {
        font-size: 12px;
    }
    .sensor {
        font-size: 10px;
    }
}
</style>