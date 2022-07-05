<template>
    <div class="contact-requests-list-container">
        <div class="contacts-list">
            <contact-request-conf-tab 
                :tabs="['Contacts', 'Pending Requests','Sent Requests']"
                :selected="selected" @selected="setSelected">
                <contact-request-tabs :isSelected="selected === 'Contacts'">
                    <div class="wrapper" v-if="contacts.length > 0 && !pendingResponse">
                        <contacts-row 
                            v-for="(contact, i) in contacts" :key="i" 
                            :first_name="contact.first_name"
                            :surname="contact.surname"
                            :picture="contact.profile_image"
                            :id="contact.uid"
                            :username="contact.username"
                            :friend_since="contact.friend_since"
                            :userMessageable="contact.userMessageable"
                            :requestRejectable="contact.requestRejectable"
                            :requestAcceptable="contact.requestAcceptable"
                            :contactBlockable="contact.userBlockable"
                            :requestCancellable="contact.requestCancellable"
                            :contactRemovable="contact.userRemovable"
                            :markContactSpam="contact.userSpamMarkable">
                        </contacts-row>
                    </div>
                    <span class="contacts-loader-waiter" v-else-if="pendingResponse">Loading contacts</span>
                    <h2 class="unavailability" v-else-if="!pendingResponse && contacts.length <= 0">No contacts</h2>
                </contact-request-tabs>
                <contact-request-tabs :isSelected="selected === 'Pending Requests'">
                    <div class="wrapper" v-if="pendingRequests.length > 0 && !pendingResponse">
                        <contacts-row 
                            v-for="(pending, i) in pendingRequests" :key="i" 
                            :first_name="pending.first_name"
                            :surname="pending.surname"
                            :picture="pending.profile_image"
                            :id="pending.uid"
                            :username="pending.username"
                            :userMessageable="pending.userMessageable"
                            :requestRejectable="pending.requestRejectable"
                            :requestAcceptable="pending.requestAcceptable"
                            :contactBlockable="pending.userBlockable"
                            :requestCancellable="pending.requestCancellable"
                            :contactRemovable="pending.userRemovable"
                            :markContactSpam="pending.userSpamMarkable">
                        </contacts-row>
                    </div>
                    <span class="contacts-loader-waiter" v-else-if="pendingResponse">Loading Pending Requests</span>
                    <h2 class="unavailability" v-else-if="!pendingResponse && pendingRequests.length <= 0">No pending requests</h2>
                </contact-request-tabs>
                <contact-request-tabs :isSelected="selected === 'Sent Requests'">
                    <div class="wrapper" v-if="pendingRequests.length > 0 && !pendingResponse">
                        <contacts-row 
                            v-for="(pending, i) in pendingRequests" :key="i" 
                            :first_name="pending.first_name"
                            :surname="pending.surname"
                            :picture="pending.profile_image"
                            :id="pending.uid"
                            :username="pending.username"
                            :userMessageable="pending.userMessageable"
                            :requestRejectable="pending.requestRejectable"
                            :requestAcceptable="pending.requestAcceptable"
                            :contactBlockable="pending.userBlockable"
                            :requestCancellable="pending.requestCancellable"
                            :contactRemovable="pending.userRemovable"
                            :markContactSpam="pending.userSpamMarkable">
                        </contacts-row>
                    </div>
                    <span class="contacts-loader-waiter" v-else-if="pendingResponse">Loading Pending Requests</span>
                    <h2 class="unavailability" v-else-if="!pendingResponse && pendingRequests.length <= 0">No pending requests</h2>
                </contact-request-tabs>
            </contact-request-conf-tab>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import moment from 'moment'
    import ContactsRow from './tinyComponents/ContactsRow.vue'
    import ContactRequestConfTab from './tabs/contact-request-conf-tab.vue'
    import ContactRequestTabs from './tabs/contact-request-tabs.vue'

    export default {
        name: 'ContactsList',
        data () {
            return {
                selected: "Contacts",
                moreOptionsToggle: false,
            }
        },
        components: {
            ContactsRow,
            ContactRequestConfTab,
            ContactRequestTabs,
        },
        computed: {
            ...mapGetters([
                'contacts',
                'pendingRequests',
                'pendingResponse'
            ]),
        },
        methods: {
            async collectContacts () {
                await this.$store.dispatch('fetchContacts')
            },
            async receivedPendingRequests () {
                await this.$store.dispatch('receivedPendingContactRequests')
            },
            async sentPendingRequests () {
                await this.$store.dispatch('sentPendingContactRequests')
            },
            async setSelected (tab){
                this.selected = tab
                switch (this.selected) {
                    case 'Contacts':
                        await this.collectContacts()
                        break
                    case 'Pending Requests':
                        await this.receivedPendingRequests()
                        break
                    case 'Sent Requests':
                        await this.sentPendingRequests()
                        break
                    default:
                        alert('Invalid tab has selected!')
                        break
                }
            },
        },
        watch: {
        },
        mounted () {
            this.setSelected('Contacts')
        }
    }
</script>
