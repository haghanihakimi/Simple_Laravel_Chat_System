<template>
    <div class="contact-block-remove-alert">
        <div class="alert-box">
            <div class="alert-title">
                <h2 v-html="contactActionAlert.title">&nbsp;</h2>
            </div>
            <div class="alert-body">
                <p v-html="contactActionAlert.body">&nbsp;</p>
            </div>
            <div class="alert-footer">
                <form action="/" method="post" @submit.prevent="blockContact" enctype="multipart/form-data" v-if="contactActionAlert.actions.block">
                    <button type="button" role="button" @click="cancellation">Cancel</button>
                    <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Block</button>
                </form>
                <form action="/" method="post" @submit.prevent="removeContact" enctype="multipart/form-data" v-if="contactActionAlert.actions.remove">
                    <button type="button" role="button" @click="cancellation">cancel</button>
                    <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">remove</button>
                </form>
                <form action="/" method="post" @submit.prevent="rejectRequest" enctype="multipart/form-data" v-if="contactActionAlert.actions.reject">
                    <button type="button" role="button" @click="cancellation">cancel</button>
                    <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">reject request</button>
                </form>
                <form action="/" method="post" @submit.prevent="cancelRequest" enctype="multipart/form-data" v-if="contactActionAlert.actions.cancel">
                    <button type="button" role="button" @click="cancellation">cancel</button>
                    <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">cancel request</button>
                </form>
                <form action="/" method="post" @submit.prevent="markAsSpam" enctype="multipart/form-data" v-if="contactActionAlert.actions.markSpam">
                    <button type="button" role="button" @click="cancellation">cancel</button>
                    <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Mark as Spam</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: 'ContactBlockRemoveAlert',
        data () {
            return {

            }
        },
        computed: {
            ...mapGetters([
                'contactActionAlert',
                'contacts',
                'pendingRequests',
                'pendingResponse'
            ]),
        },
        methods: {
            async blockContact () {
                if (!this.pendingResponse) {
                    await this.$store.dispatch('blockContact', this.contactActionAlert.id)
                    let i = this.contacts.map(contact => contact.uid).indexOf(this.contactActionAlert.id) // find index of your object
                    this.contacts.splice(i, 1)
                    this.cancellation()
                }
            },
            async removeContact () {
                if (!this.pendingResponse) {
                    await this.$store.dispatch('removeContact', this.contactActionAlert.id)
                    let i = this.contacts.map(contact => contact.id).indexOf(this.contactActionAlert.id) // find index of your object
                    this.contacts.splice(i, 1)
                    this.cancellation()
                }
            },
            rejectRequest () {
                if (!this.pendingResponse) {
                    this.$store.dispatch('rejectContactRequest', this.contactActionAlert.id)
                    let i = this.pendingRequests.map(pending => pending.id).indexOf(this.contactActionAlert.id) // find index of your object
                    this.pendingRequests.splice(i, 1)
                    this.cancellation()
                }
            },
            cancelRequest () {
                if (!this.pendingResponse) {
                    this.$store.dispatch('cancelContactRequest', this.contactActionAlert.id)
                    let i = this.pendingRequests.map(pending => pending.uid).indexOf(this.contactActionAlert.id) // find index of your object
                    this.pendingRequests.splice(i, 1)
                    this.cancellation()
                }
            },
            markAsSpam () {
                if (!this.pendingResponse) {
                    this.$store.dispatch('markAsSpam', this.contactActionAlert.id)
                    let i = this.pendingRequests.map(pending => pending.uid).indexOf(this.contactActionAlert.id) // find index of your object
                    this.pendingRequests.splice(i, 1)
                    this.cancellation()
                }
            },
            cancellation () {
                    let inputs = {
                        'title': '',
                        'uid': '',
                        'body': '',
                        'actions': {
                            "block": false, 
                            "remove": false, 
                            "reject": false, 
                            "cancel": false, 
                            "accept": false, 
                            "markSpam": false, 
                        },
                        'status': false
                    }
                    this.$store.dispatch('fillContactActionAlert', inputs)
            }
        },
        mounted () {
        }
    }
</script>