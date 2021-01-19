<script>
    Vue.component('error-message', {
        data(){
            return {}
        },
        props: ['error'],
        template: `<div class="row"><div v-if="error" class="error col-md-12">
                        <div class="alert alert-danger" role="alert">{{ error }}</div>
                    </div></div>`,
    })
</script>