<template>
    <div class="account__delete__warning">
        <div class="account__delete__popup">
            <h2 class="alert__title">WARNING!</h2>
            <p class="alert__message">
                &bullet;&nbsp;Your account will be deleted permanently.<br/>
                &bullet;&nbsp;Your comments, posts, messages and contacts and all your other activities will be deleted.<br/>
            </p>
            <div class="alert__controls">
                <form action="/" method="POST" @submit.prevent="deleteAccount" enctype="multipart/form-data">
                    <button role="button" :class="[pendingFetch ? 'inActive' : 'active']" :disabled="pendingFetch" @click="$store.dispatch('toggleDeletionAlert', false)" type="button">Cancel</button>
                    <button role="button" :class="[pendingFetch ? 'inActive' : 'active']" :disabled="pendingFetch" type="submit">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    import { mapGetters } from 'vuex'

    export default {
        name: 'AccountDeleteWarning',
        data () {
            return {
            }
        },
        computed: {
            ...mapGetters([
                'user',
                'deletionAlert',
                'pendingFetch',
            ]),
        },
        methods: {
            async deleteAccount () {
                if (!this.pendingFetch){
                    await this.$store.dispatch('deleteAccount')
                }
            }
        },
        mounted () {
        }
    }
</script>
