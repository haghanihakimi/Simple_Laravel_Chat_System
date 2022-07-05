<template>
    <div class="errors-container">
        <div class="errors-popup">
            <div class="error-heading"><strong v-html="errors.title">&nbsp;</strong></div>
            <div class="error-body">
                <p v-html="errors.message"></p>
            </div>
            <div class="error-footer">
                <button type="button" role="button" @click="$store.dispatch('updateErrors', [])" v-html="errors.button">&nbsp;</button>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: 'Errors',
        computed: {
            ...mapGetters([
                'errors'
            ])
        }
    }
</script>
