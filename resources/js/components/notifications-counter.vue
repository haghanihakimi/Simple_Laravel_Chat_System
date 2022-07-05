<template>
    <div class="counters-holder">
        <span v-if="notifications.length > 0" class="counters-holder_active counters-holder">
            {{ notifications.length }}
        </span>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: 'NotificationsCounter',
        computed: {
            ...mapGetters([
                'notifications'
            ])
        }
    }
</script>
