<template>
    <div>
        <error-message :error="error" v-if="error"></error-message>
        <div class="row">
            <h4 class="col-md-12">Favorite sensors</h4>
        </div>
        <draggable class="row" v-model="items" group="sensors" @end="end">
            <div v-for="item in items" :key="item.id" class="col-md-2 col-4 mb-4">
                <div class="p-3 text-center border border-secondary">
                    <div class="value">{{ item.favoriteable.value | sensorValue(item.favoriteable) }}</div>
                    <div class="name">{{ item.name }}</div>
                </div>
            </div>
            <div v-if="!loading && items.length == 0" class="col-md-12 mb-4">
                No favorite items found, go to sensors listing and set some favorites.
            </div>
        </draggable>
        <div v-if="loading" class="mb-5 mt-3 p-3">
            <font-awesome-icon :icon="['fas', 'spinner']" />
        </div>
        <div class="clearfix col-md-12">&nbsp;</div>
    </div>
</template>


<script>
import client from '../../client';
import draggable from 'vuedraggable'

export default {
    components: {
        draggable,
    },
    data() {
        return {
            echo: null,
            loading: true,
            error: null,
            items: [],
        };
    },
    created() {
        this.fetchData()
        this.connect()
    },
    methods: {
        fetchData() {
            client
                .all(`v1/favorite?type=${process.env.MIX_FAVORITE_TYPE_SENSOR}`)
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
                this.echo = Echo.channel('data.changed')
                    .listen('FavoriteDataChanged', (data) => {
                        this.fetchData()
                    });
            }
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
.value {
    font-size: 26px;
}
.name {
    font-size: 12px;
}

@media (max-width: 576px) {
    .value {
        font-size: 16px;
    }
    .name {
        font-size: 10px;
    }
}
</style>