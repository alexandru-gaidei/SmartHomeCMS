<script>
    Vue.component('validation-errors', {
        data(){
            return {}
        },
        props: ['errors'],
        template: `<div class="row"><div v-if="validationErrors" class="col-md-12">
                    <ul class="alert alert-danger">
                        <li v-for="(value, key, index) in validationErrors">{{ value }}</li>
                    </ul>
                </div></div>`,
        computed: {
            validationErrors(){
                let errors = Object.values(this.errors);
                errors = errors.flat();
                return errors;
            }
        }
    })
</script>