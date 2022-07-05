<template>
    <div class="searchbox-container">
        <perfect-scrollbar>
            <form action="/" method="post" enctype="multipart/form-data" :class="[loadingPeople ? 'loadingPeople' : '']">
                <div>
                    <input type="text" placeholder="Search People..." :readonly="loadingPeople" v-model="input">
                </div>
            </form>
            <div class="people-listbox" :class="[loadingPeople ? 'loadingPeople' : '']">
                
                <div class="people-list" v-for="(user, i) in search" :key="i" @click="selectTarget(user.id)">
                    <div class="people-avatar-holder">
                        <img :src="`/storage/locals/${user.profile_picture}`" :alt="`${user.first_name} ${user.surname} profile`">
                    </div>
                    <div class="people-names-holder">
                        <span :title="`${user.first_name} ${user.surname}`">{{ user.first_name }} {{ user.surname }}</span>
                    </div>
                </div>

            </div>
        </perfect-scrollbar>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import moment from 'moment'

    export default {
        name: 'SearchBox',
        data () {
            return {
                input: '',
                pages: 1,
            }
        },
        computed: {
            ...mapGetters([
                'peopleSearch',
                'peopleSearchLastPage',
                'loadingPeople'
            ]),
            search () {
                return this.peopleSearch
                /*
                return this.peopleSearch.filter((output) => {
                    return output.fname.toString().toLowerCase().match(this.input.toLowerCase())
                            || output.sname.toString().toLowerCase().match(this.input.toLowerCase())
                })
                */
            }
        },
        methods: {
            async loadSearch () {
                await this.$store.dispatch('fetchPeopleSearch', this.input)
            },
            // handleScrolledToBottom (isVisible){
            //     if (!isVisible){
            //         return
            //     }
            //     if (this.pages >= this.peopleSearchLastPage) {
            //         return
            //     }
            //     this.pages++
            //     this.loadSearch()
            // },
            selectTarget (target) {
                this.$store.dispatch('setUserTarget', target)
                this.$store.dispatch('toggleProfileView', true)
            }
        },
        watch: {
            'input': {
                handler: 'loadSearch',
                deep: true
            }
        }
    }
</script>
