<template>
    <div class="counters-holder">
        <span v-if="messages.length > 0" class="counters-holder_active counters-holder">
            {{ messages.length }}
        </span>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: 'MessagesCounter',
        computed: {
            ...mapGetters([
                'messages'
            ])
        }
    }
</script>
