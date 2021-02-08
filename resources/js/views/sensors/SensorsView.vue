<template>
    <div class="col-md-12" v-if="this.app.user">
        <error-message :error="error" v-if="error"></error-message>

        <router-link v-if="app.user.is_admin" class="btn btn-primary btn-sm float-left" :to="{ name: 'sensors.edit', params: { id: item.id } }">Edit</router-link>
        <span class="float-left" v-if="app.user.is_admin" >&nbsp;&nbsp;</span>
        <h3>{{ item.name || "&nbsp;" }} <small class="float-right">{{ item.value | sensorValue(item) }}</small></h3>
        <table class="table table-sm">
        <tbody>
            <tr>
                <th scope="row">Group</th>
                <td>{{ item.group ? item.group.name : "" }}</td>
            </tr>
            <tr>
                <th scope="row">Source type</th>
                <td>{{ item.source_type }}</td>
            </tr>
            <tr v-if="item.source_type == srcTypeFetch">
                <th scope="row">Fetch URL</th>
                <td>{{ item.source_url_fetch }}</td>
            </tr>
            <tr v-if="item.source_type != srcTypeFetch">
                <th scope="row">Push Identifier</th>
                <td>{{ item.push_url }}</td>
            </tr>
            <tr>
                <th scope="row">Response parameter</th>
                <td>{{ item.parameter }}</td>
            </tr>
            <tr v-if="valTypes">
                <th scope="row">Value type</th>
                <td>{{ valTypes[item.value_type] }}</td>
            </tr>
            <tr v-if="item.source_type == srcTypeFetch">
                <th scope="row">Execute at</th>
                <td>{{ item.rrule_human }}</td>
            </tr>
            <tr v-if="item.value_type == valTypeNum">
                <th scope="row">Min value</th>
                <td>{{ item.min_value }}</td>
            </tr>
            <tr v-if="item.value_type == valTypeNum">
                <th scope="row">Max value</th>
                <td>{{ item.max_value }}</td>
            </tr>
        </tbody>
        </table>

        <div v-if="item.actions && item.actions.length > 0">
            <hr>
            <div>&nbsp;</div>
            <h4>Actions ({{ item.actions.length }})</h4>
            <div>&nbsp;</div>
            <p v-for="action in item.actions" :key="action.id">
                <router-link v-if="app.user.is_admin" class="btn btn-primary btn-sm" :to="{ name: 'actions.edit', params: { id: action.id } }">Edit</router-link>
                <strong>{{ action.name }}</strong> triggered on the <strong>{{ action.value_type }}</strong> value of sensor and notify via <strong>{{ action.type }}</strong>.
            </p>
        </div>

        <div v-if="hasChart">
            <hr>
            <div>&nbsp;</div>
            <h4 v-if="item.history_chart">History last values</h4>
            <div>&nbsp;</div>
        </div>
        <canvas v-if="hasChart" id="chart"></canvas>

        <div v-if="item.history_last && item.history_last.length > 0">
            <hr>
            <div>&nbsp;</div>
            <h4>Last History</h4>
            <div>&nbsp;</div>
            <table class="table table-sm">
                <tr v-for="history_item in item.history_last" v-bind:key="history_item.id">
                    <td>{{ history_item.ocurrence_at }}</td>
                    <td>{{ history_item.status }}</td>
                    <td>{{ history_item.data }}</td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
import client from '../../client';
import Chart from 'chart.js';

export default {
    props: ['app'],
    data() {
        return {
            valTypes: null,
            valTypeNum: process.env.MIX_SENSOR_VAL_TYPE_NUM,
            srcTypeFetch: process.env.MIX_SENSOR_SRC_TYPE_FETCH,
            error: null,
            loaded: false,
            hasChart: true,
            item: {},
        };
    },
    methods: {
        drawChart() {
            new Chart('chart', {
                type: 'line',
                data: {
                    labels: this.item.history_chart.labels,
                    datasets: [{
                        label: `${this.item.name} value`,
                        data: this.item.history_chart.data,
                        fill: false,
                        borderColor: "rgba(255,205,86,1)",
                        pointBorderColor: "red",
                        pointBackgroundColor: "yellow",
                    }],
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        }
    },
    mounted() {
        client.find('v1/sensors', this.$route.params.id).then((response) => {
            this.loaded = true;
            this.item = response.data.data;
            if(this.item.value_type == this.valTypeNum) {
                this.drawChart();
            } else {
                this.hasChart = false;
            }
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });

        client.secureClient().get('v1/sensors/metadata').then((response) => {
            this.loaded = true;
            this.valTypes = response.data.val_types;
        }).catch(error => {
            this.error = error.response.data.message || error.message;
        });
    }
};
</script>

<style scoped>
#chart {
    width: 100%;
    height: 200px;
}
</style>