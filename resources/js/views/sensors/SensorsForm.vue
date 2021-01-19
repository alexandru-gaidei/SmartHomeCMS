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
                <label for="user_groups">Group</label>
                <select id="user_groups" class="form-control" v-model="item.group_id">
                    <option v-for="group in groups" :key="group.id" v-bind:value="group.id">{{ group.name }}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="source_type">Source type</label>
                <select id="source_type" class="form-control" v-model="item.source_type">
                    <option v-for="(item, key) in srcTypes" :key="key" v-bind:value="key">{{ item }}</option>
                </select>
            </div>
            <div class="form-group" v-if="item.source_type == typeFetch">
                <label for="source_url_fetch">URL to fetch</label>
                <input id="source_url_fetch" class="form-control" v-model="item.source_url_fetch" />
            </div>
            <div class="form-group" v-if="item.source_type != typeFetch">
                <label for="identifier">Identifier</label>
                <input id="identifier" class="form-control" v-model="item.identifier" />
            </div>

            <div class="form-group">
                <label for="parameter">Parameter from fetched data (ex: variables.temperature, humidity, etc)</label>
                <input id="parameter" class="form-control" v-model="item.parameter" />
            </div>

            <div class="form-group">
                <label for="value_type">Value type</label>
                <select id="value_type" class="form-control" v-model="item.value_type">
                    <option v-for="(item, key) in valTypes" :key="key" v-bind:value="key">{{ item }}</option>
                </select>
            </div>

            <div class="form-group" v-if="item.value_type == valTypeNum">
                <label for="min_value">Min value to action</label>
                <input id="min_value" class="form-control" v-model="item.min_value" />
            </div>
            <div class="form-group" v-if="item.value_type == valTypeNum">
                <label for="max_value">Max value to action</label>
                <input id="max_value" class="form-control" v-model="item.max_value" />
            </div>

            <div class="row" id="rrule" v-if="item.source_type == typeFetch">
                <div class="form-group col-md-6">
                    <label for="rruleInterval">Fetch every</label>
                    <select id="rruleInterval" class="form-control" v-model="item.rrule_interval">
                        <option v-for="(item, key) in rruleIntervals" :key="key" v-bind:value="key">{{ item }}</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="rruleFreq">&nbsp;</label>
                    <select id="rruleFreq" class="form-control" v-model="item.rrule_freq">
                        <option v-for="(item, key) in rruleFreqs" :key="key" v-bind:value="key">{{ item }}</option>
                    </select>
                </div>
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
            valTypeNum: process.env.MIX_SENSOR_VAL_TYPE_NUM,
            typeFetch: process.env.MIX_SENSOR_SRC_TYPE_FETCH,
            editing: false,
            error: null,
            validationErrors: null,
            message: null,
            loaded: false,
            saving: false,
            groups: [],
            srcTypes: [],
            valTypes: [],
            rruleFreqs: [],
            rruleIntervals: [],
            item: {}
        };
    },
    methods: {
        onSubmit(event) {
            this.saving = true;

            const data = {
                group_id: this.item.group_id,
                name: this.item.name,
                favorite_name: this.item.favorite_name,
                source_type: this.item.source_type,
                source_url_fetch: this.item.source_url_fetch,
                parameter: this.item.parameter,
                identifier: this.item.identifier,
                value_type: this.item.value_type,
                min_value: this.item.min_value,
                max_value: this.item.max_value,
                is_favorite: this.item.is_favorite,
            };

            if(this.item.rrule_freq && this.item.rrule_interval) {
                data.execute_at_rrule = `FREQ=${this.item.rrule_freq};INTERVAL=${this.item.rrule_interval}`;
            }

            Object.keys(data).forEach((key) => (!data[key]) && delete data[key]);

            const req = this.editing 
                ? client.update('v1/sensors', this.item.id, data)
                : client.create('v1/sensors', data);
            
            req.then((response) => {
                this.item = response.data.data;
                setTimeout(() => this.message = null, 3000);

                if(this.editing) {
                    this.message = 'Sensor updated';
                }
                else {
                    this.message = 'Sensor created';
                    this.editing = true;
                    this.$router.push({ name: 'sensors.edit', params: { id: this.item.id } });
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

        client.all('v1/groups').then((response) => {
            this.groups = response.data.data;
            this.loaded = true;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });

        if(this.editing) {
            client.find('v1/sensors', this.$route.params.id).then((response) => {
                this.loaded = true;
                this.item = response.data.data;
            }).catch(error => {
                this.error = error.response.data.message || error.message;
            });
        }

        client.secureClient().get('v1/sensors/metadata').then((response) => {
            this.loaded = true;
            this.srcTypes = response.data.src_types;
            this.valTypes = response.data.val_types;
            this.rruleFreqs = response.data.rrule_freq;
            this.rruleIntervals = response.data.rrule_interval;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });
    }
};
</script>