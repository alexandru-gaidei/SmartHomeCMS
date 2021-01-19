Vue.filter('sensorValue', function (value, item) {
    if (value) {
        if (item.value_type == process.env.MIX_SENSOR_VAL_TYPE_NUM) {
            return parseFloat(value).toFixed(2)
        } else {
            return value != 0 ? process.env.MIX_SENSOR_VAL_TYPE_BOOL_TRUE : process.env.MIX_SENSOR_VAL_TYPE_BOOL_FALSE
        }
    } else {
        return '...'
    }
})