<template>
    <div class="profile-overview">
        <div class="profile-pic-box" v-if="profiles">
            <div class="avatar-box" v-if="profiles.profile_picture">
                <img :src="`/storage/locals/${profiles.profile_picture}`" :alt="`${profiles.first_name} ${profiles.surname} profile`">
                <span v-if="profiles.privacy" title="Locked Profile">
                    <svg viewBox="0 0 382.78 502.66">
                        <path fill="#0ea785" d="M241.88,507.66c-9.33-1.47-18.8-2.36-28-4.51C149.32,488.05,106,447.89,80.09,388,69,362.3,64,335.21,64.24,307.24c.41-51.53,42.81-94.56,94.33-94.89q93.69-.6,187.39,0c55.25.39,100.44,46.14,101.05,101.33,1.05,94.77-68.63,176.93-162.65,191.79-5.31.83-10.65,1.46-16,2.19ZM270.35,357c25-10.44,32.65-33.45,28.25-51.94a44.19,44.19,0,0,0-85.86-.39c-4.49,17.83,2.41,41.63,28.16,52.33v5.24q0,35.33,0,70.65c0,5.85,2.1,10.57,7.11,13.66a13.67,13.67,0,0,0,14.46.43c5.38-2.92,7.89-7.61,7.89-13.67C270.35,408,270.35,382.63,270.35,357Z" transform="translate(-64.24 -5)"/>
                        <path fill="#0ea785" d="M388.13,191c-19.22-7.11-38.59-9.06-58.89-8,0-2.41,0-4.31,0-6.21-.28-16.66.67-33.46-1.12-50-3.86-35.42-36-62.65-72.18-62.75-36.52-.09-68.33,27.09-73,62.61a161.6,161.6,0,0,0-.91,19.07c-.14,12.09,0,24.19,0,37.11-19.79-.26-39.54-.73-59.15,6.73,1.63-31-3-61.2,6-90.73C146.56,40.64,203.31,1.1,263.71,5.31,326,9.64,376.64,55.34,386.23,116a156.59,156.59,0,0,1,1.82,22C388.31,155.42,388.13,172.86,388.13,191Z" transform="translate(-64.24 -5)"/>
                        <path fill="#0ea785" d="M255.75,330a14.7,14.7,0,1,1,14.58-14.7A14.86,14.86,0,0,1,255.75,330Z" transform="translate(-64.24 -5)"/>
                    </svg>
                </span>
            </div>
        </div>
        <div class="profile-name-box" v-if="profiles">
            <h2 class="profile-name">{{ profiles.first_name }} {{ profiles.surname }}</h2>
        </div>
        <div class="profile-btnEdit-box" v-if="profiles">
            <button @click="openProfile" class="btnEdit" type="button" role="button">Edit Profile</button>
        </div>
        <div class="profile-user-descriptions" v-if="profiles">
            <strong class="profile-user-descTitle">about you</strong>
            <markdown v-if="profiles.bio" class="md-body" :content="profiles.bio"></markdown>
            <markdown v-else class="md-body" :content="`Describe who you are!`"></markdown>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import Markdown from 'markdown-it-vue/dist/markdown-it-vue-light.umd.min.js'
    import 'markdown-it-vue/dist/markdown-it-vue-light.css'

    export default {
        name: 'ProfileOverview',
        components: {
            Markdown,
        },
        computed: {
            ...mapGetters([
                'profiles',
                'updating'
            ])
        },
        methods: {
            fetchProfiles () {
                this.$store.dispatch('fetchProfiles')
            },
            openProfile () {
                this.$store.dispatch('toggleProfiles', true)
            }
        },
        mounted () {
            this.fetchProfiles()
        }
    }
</script>
