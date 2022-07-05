<template>
    <div class="counters-holder">
        <div v-if="counter > 0" class="counters-holder_active counters-holder">
            <span v-if="counter <= 9" style="font-size:10px !important;">
                0{{ counter }}
            </span>
            <span v-else-if="counter >= 20" style="font-size:10px !important;">
                +20
            </span>
            <span v-else style="font-size:10px !important;">
                {{ counter }}
            </span>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import ReceivedContactRequestContext from './tinyComponents/ReceivedContactRequestContext.vue'

    export default {
        name: 'ContactRequestsCounter',
        props: ['userId'],
        data () {
            return {
                counter: 0,
            }
        },
        computed: {
            ...mapGetters([
            ]),
        },
        methods: {
            async catchContactRequests () {
                await Echo.private(`newContactRequest.${this.userId}`)
                    .listen('ContactRequests', (response) => {
                        this.$toast.info({
                            component: ReceivedContactRequestContext,
                            props:  {
                                contents: response.contents
                            },
                            //icon: MyIconComponent,
                        })
                        this.$store.dispatch('userAddableAction', response.interacts.add)
                        this.$store.dispatch('requestRejectableAction', response.interacts.reject)
                        this.$store.dispatch('requestAcceptableAction', response.interacts.accept)
                        this.$store.dispatch('markContactSpamAction', response.interacts.spam)
                    })
            },
            async requestCancellationListener () {
                await Echo.private(`ContactRequestCancellation.${this.userId}`)
                    .listen('CancelContactRequestEvent', (response) => {
                        this.$store.dispatch('userAddableAction', response.interacts.add)
                        this.$store.dispatch('requestRejectableAction', response.interacts.reject)
                        this.$store.dispatch('requestAcceptableAction', response.interacts.accept)
                        this.$store.dispatch('markContactSpamAction', response.interacts.spam)
                    })
            },
            async requestRejectListener () {
                await Echo.private(`ContactRequestReject.${this.userId}`)
                    .listen('RejectContactRequestEvent', (response) => {
                        this.$store.dispatch('userAddableAction', response.interacts.add)
                        this.$store.dispatch('userMessageableAction', response.interacts.message)
                        this.$store.dispatch('requestCancellableAction', response.interacts.cancel)
                    })
            },
            async requestAcceptanceListener () {
                await Echo.private(`ContactRequestAcceptance.${this.userId}`)
                    .listen('AcceptContactRequestEvent', (response) => {
                        this.$store.dispatch('userAddableAction', response.interacts.add)
                        this.$store.dispatch('userMessageableAction', response.interacts.message)
                        this.$store.dispatch('requestCancellableAction', response.interacts.cancel)
                        this.$store.dispatch('contactRemovableAction', response.interacts.remove)
                        this.$toast.info({
                            component: contactNotificationsBody,
                            props:  {
                                contents: response.contents
                            },
                            //icon: MyIconComponent,
                        })
                    })
            },
            async receivedContactRequestCounter () {
                await Echo.private(`ReceivedContactRequestCounter.${this.userId}`)
                    .listen('CountPendingRequestsEvent', (response) => {
                        this.counter = response.pendings
                    })
            }
        },
        mounted () {
            this.catchContactRequests()
            this.requestCancellationListener()
            this.requestRejectListener()
            this.requestAcceptanceListener()
            this.receivedContactRequestCounter()
        }
    }
</script>
