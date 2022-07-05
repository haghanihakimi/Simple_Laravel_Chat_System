<template>
    <div class="preferences">
        <h2 class="panel-title">Preferences</h2>
        <div class="preferences-innerbox">
            <div class="preference-item-box">
                <div class="preference-dark-mode modesToggles" :class="{'savingDisabled': saving}">
                    <form action="/" @submit.prevent="updateDarkMode" method="post" enctype="multipart/form-data">
                        <label for="darkMode">Dark Mode</label>
                        <button type="submit" :disabled="saving" role="button"><span :class="{'toggleOn': preferences.settings.dark_mode, 'toggleOff': !preferences.settings.dark_mode}">&nbsp;</span></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: 'preferences',
        computed: {
            ...mapGetters([
                'preferences',
                'saving'
            ])
        },
        methods: {
            updateDarkMode () {
                if (this.preferences.settings.dark_mode) {
                    this.$store.dispatch('updateDarkMode', {'darkMode': 0})
                } else {
                    this.$store.dispatch('updateDarkMode', {'darkMode': 1})
                }
            },
            updateNotificationsSound () {
                if (this.preferences.settings.notification_sound) {
                    this.$store.dispatch('updateNotificationSound', {'notificationsSound': 0})
                } else {
                    this.$store.dispatch('updateNotificationSound', {'notificationsSound': 1})
                }
            },
            updateMessagesSound () {
                if (this.preferences.settings.message_sound) {
                    this.$store.dispatch('updateMessageSound', {'messagesSound': 0})
                } else {
                    this.$store.dispatch('updateMessageSound', {'messagesSound': 1})
                }
            }
        },
        mounted () {
        }
    }
</script>
