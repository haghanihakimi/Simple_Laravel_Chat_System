<template>
    <div class="contacts-wrapper">
        <div class="list-container">
            <a href="javascript:void(0)" class="list-holder" target="_self">
                <div class="profile-image">
                    <img :src="`/storage/locals/${picture}`" :alt="`${first_name} ${surname} profile link`">
                </div>
                <div class="list-item-names">
                    <span :title="`${first_name} ${surname}`">{{first_name}} {{surname}}</span>
                    <span style="font-size:12px; padding-top: 0; margin-top:0; opacity:0.75; font-weight:500;">Since: {{formatDate(friend_since, 'DD, MMM YY')}}</span>
                </div>
                <div class="list-item-controls-box">
                    <button type="button" role="button" @click.stop="moreOptions = !moreOptions">
                        <svg viewBox="0 0 502.74 110.01">
                            <path d="M507.33,264.41c-2.48,9.12-5.15,18.17-11.37,25.64-15.24,18.3-34.57,25.6-57.65,19.37-22.56-6.09-36.16-21.42-40-44.61-4.8-28.83,15.1-57.14,43.27-62.18,30.63-5.47,59,13.55,64.8,43.37a19.31,19.31,0,0,0,1,2.7Z" transform="translate(-4.59 -201.59)"/>
                            <path d="M4.59,256.49a55,55,0,1,1,109.91.37c-.12,30.13-25,54.7-55.21,54.65A54.86,54.86,0,0,1,4.59,256.49Z" transform="translate(-4.59 -201.59)"/>
                            <path d="M252.65,311.51C222.18,311.45,198,286.79,198,256c.08-30.13,24.88-54.5,55.33-54.36,30.05.15,54.72,25.15,54.56,55.32S282.89,311.57,252.65,311.51Z" transform="translate(-4.59 -201.59)"/>
                        </svg>
                    </button>
                    <div class="more-options" v-if="moreOptions">
                        <a :href="`/messages/${username}`" v-if="userMessageable" target="_self">Messages</a>
                        <form action="/" method="post" v-if="contactRemovable" enctype="multipart/form-data">
                            <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Friendship</button>
                        </form>
                        <form action="/" method="post" @submit.prevent="removeContact" v-if="contactRemovable" enctype="multipart/form-data">
                            <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Remove Contact</button>
                        </form>
                        <form action="/" method="post" @submit.prevent="blockContact" v-if="contactBlockable" enctype="multipart/form-data">
                            <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Block Contact</button>
                        </form>
                        <form action="/" method="post" @submit.prevent="acceptRequest" v-if="requestAcceptable" enctype="multipart/form-data">
                            <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Accept Request</button>
                        </form>
                        <form action="/" method="post" @submit.prevent="rejectRequest" v-if="requestRejectable" enctype="multipart/form-data">
                            <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Reject Request</button>
                        </form>
                        <form action="/" method="post" @submit.prevent="cancelRequest" v-if="requestCancellable" enctype="multipart/form-data">
                            <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Cancel Request</button>
                        </form>
                        <form action="/" method="post" @submit.prevent="markAsSpam" v-if="markContactSpam" enctype="multipart/form-data">
                            <button type="submit" role="button" :disabled="pendingResponse" :style="{opacity: [pendingResponse ? '0.5' : '1'],cursor: [pendingResponse ? 'default' : 'pointer']}">Mark as Spam</button>
                        </form>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import moment from 'moment'

    export default {
        name: 'ContactRequestsList',
        props: [
            'first_name', 
            'surname', 
            'picture', 
            'id', 
            'username',
            'friend_since',
            'userMessageable',
            'requestRejectable',
            'requestCancellable',
            'requestAcceptable',
            'contactBlockable',
            'contactRemovable',
            'markContactSpam',
        ],
        data () {
            return {
                moreOptions: false,
                pages: 1
            }
        },
        computed: {
            ...mapGetters([
                'contacts',
                'pendingRequests',
                'contactMoreOptions',
                'pendingResponse',
            ]),
        },
        methods: {
            blockContact () {
                if (!this.pendingResponse) {
                    let inputs = {
                        'title': 'Block Cotact',
                        'id': this.id,
                        'body': `You want to block, <strong>${this.first_name} ${this.surname}</strong>. If you press <strong>Block</strong> button, you will also remove ${this.first_name} from your contact list.`,
                        'actions': {
                            "block": true, 
                            "remove": false, 
                            "reject": false, 
                            "cancel": false, 
                            "accept": false, 
                            "markSpam": false, 
                        },
                        'status': true
                    }
                    this.$store.dispatch('fillContactActionAlert', inputs)
                    this.moreOptions = false
                }
            },
            removeContact () {
                if (!this.pendingResponse) {
                    let inputs = {
                        'title': 'Remove Cotact',
                        'id': this.id,
                        'body': `You want to remove <strong>${this.first_name} ${this.surname}</strong> from your contacts list. To add ${this.first_name} again you must send another contact request to them.`,
                        'actions': {
                            "block": false, 
                            "remove": true, 
                            "reject": false, 
                            "cancel": false, 
                            "accept": false, 
                            "markSpam": false, 
                        },
                        'status': true
                    }
                    this.$store.dispatch('fillContactActionAlert', inputs)
                    this.moreOptions = false
                }
            },
            rejectRequest () {
                if (!this.pendingResponse) {
                    let inputs = {
                        'title': 'Reject Request',
                        'id': this.id,
                        'body': `If you do not want to receive any more requests from ${this.first_name} you can mark this request as <strong>spam</spam> before you reject it.`,
                        'actions': {
                            "block": false, 
                            "remove": false, 
                            "reject": true, 
                            "cancel": false, 
                            "accept": false, 
                            "markSpam": false, 
                        },
                        'status': true
                    }
                    this.$store.dispatch('fillContactActionAlert', inputs)
                    this.moreOptions = false
                }
            },
            acceptRequest () {
                if (!this.pendingResponse) {
                    this.$store.dispatch('acceptContactRequest', this.id)
                    let i = this.pendingRequests.map(pending => pending.id).indexOf(this.id) // find index of your object
                    this.pendingRequests.splice(i, 1)
                    this.moreOptions = false
                }
            },
            cancelRequest () {
                if (!this.pendingResponse) {
                    let inputs = {
                        'title': 'Cancel Request',
                        'id': this.id,
                        'body': `${this.first_name} already received your request! You can wait longer to receive a response from ${this.first_name}. By cancelling request you minimize risk of getting <strong>marked as spam</strong>!`,
                        'actions': {
                            "block": false, 
                            "remove": false, 
                            "reject": false, 
                            "cancel": true, 
                            "accept": false, 
                            "markSpam": false, 
                        },
                        'status': true
                    }
                    this.$store.dispatch('fillContactActionAlert', inputs)
                    this.moreOptions = false
                }
            },
            markAsSpam () {
                if (!this.pendingResponse) {
                    let inputs = {
                        'title': 'Cancel Request',
                        'id': this.id,
                        'body': `If you mark ${this.first_name} as spam, they can send no more contact requests. However, they can send you message despite you marked them as spam! By accepting or rejecting this request <strong>mark as spam</strong> option will no longer be available.`,
                        'actions': {
                            "block": false, 
                            "remove": false, 
                            "reject": false, 
                            "cancel": false, 
                            "accept": false, 
                            "markSpam": true, 
                        },
                        'status': true
                    }
                    this.$store.dispatch('fillContactActionAlert', inputs)
                    this.moreOptions = false
                }
            },
            formatDate (date, format) {
                return moment(date).format(format)
            },
        },
        watch: {
        },
        mounted () {
            document.addEventListener('click', () => {
                this.moreOptions = false
            })
        }
    }
</script>
