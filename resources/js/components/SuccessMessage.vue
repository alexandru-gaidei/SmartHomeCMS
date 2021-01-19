<script>
    Vue.component('success-message', {
        data(){
            return {}
        },
        props: ['message'],
        template: `<div class="row"><div v-if="message" class="message col-md-12">
                        <div class="alert alert-success" role="alert">{{ message }}</div>
                    </div></div>`,
    })
</script>