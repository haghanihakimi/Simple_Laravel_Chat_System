<template>
    <div class="logout-left-panel left-panel-innerbox">
        <form action="/" @submit.prevent="signout" method="POST" enctype="multipart/form-data">
            <button v-if="!signResponse" type="submit" role="button">
                <span class="material-symbols-rounded">logout</span>
            </button>
            <div v-if="signResponse" class="loader">
                <svg viewBox="0 0 676 676">
                    <circle cx="338" cy="338" r="308"/>
                </svg>
            </div>
        </form>
    </div>
</template>
<script>
    import { mapGetters } from 'vuex'

    export default {
        name: "Signout",
        computed: {
            ...mapGetters([
                'signResponse',
            ]),
        },
        methods: {
            signout () {
                if (!this.signResponse) {
                    this.$store.dispatch('signout')
                }
            }
        }
    }
</script>
