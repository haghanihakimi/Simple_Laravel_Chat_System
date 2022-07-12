<template>
    <div class="preferences">
        <h2 class="panel-title">Preferences</h2>
        <div class="preferences-innerbox">
            <div class="preference-item-box">
                <div class="preference-dark-mode modesToggles" :class="{'savingDisabled': saving}">
                    <form action="/" @submit.prevent="updateDarkMode" method="post" enctype="multipart/form-data">
                        <label for="darkMode">Dark Mode</label>
                        <button type="submit" :disabled="saving" role="button"><span :class="{'toggleOn': preferences.dark_mode, 'toggleOff': !preferences.dark_mode}">&nbsp;</span></button>
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
                if (this.preferences.dark_mode) {
                    this.$store.dispatch('updateDarkMode', {'darkMode': 0})
                } else {
                    this.$store.dispatch('updateDarkMode', {'darkMode': 1})
                }
            }
        },
        mounted () {
        }
    }
</script>
