<template>
    <div class="notification-body" style="padding:12px;font-size:16px;letter-spacing:1px;">
        <h4 class="alert-heading" style="padding:0;margin:0;" v-html="contents"></h4>
    </div>
</template>

<script>
    export default {
        name: 'contactNotificationsBody',
        props: ['contents'],
        data () {
            return {
            }
        },
        mounted () {
        }
    }
</script>