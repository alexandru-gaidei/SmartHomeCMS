Vue.directive('loader', {
    bind: function (el, binding) {
        if(binding.value.loading) {
            el.innerHTML = '. . .'
            el.disabled = true
        }
    },
    update: function(el, binding, vnode) {
        if(binding.value.loading) {
            el.innerHTML = '. . .'
            el.disabled = true
        } else {
            vnode.key += '1'
            vnode.context.$forceUpdate()
        }
    }
})