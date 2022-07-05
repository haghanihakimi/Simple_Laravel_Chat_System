<template>
    <div class="messages-screen">
        <div class="ms-top-heading">
            <div class="heading-username-box">
                <span v-if="!loading">
                    {{`${contact.first_name} ${contact.surname}`}}
                    <span v-if="contact.privacy" class="locked" :title="`If you are not ${contact.first_name}'s friend, you cannot send them message.`">
                        <svg viewBox="0 0 382.78 502.66">
                            <path d="M241.88,507.66c-9.33-1.47-18.8-2.36-28-4.51C149.32,488.05,106,447.89,80.09,388,69,362.3,64,335.21,64.24,307.24c.41-51.53,42.81-94.56,94.33-94.89q93.69-.6,187.39,0c55.25.39,100.44,46.14,101.05,101.33,1.05,94.77-68.63,176.93-162.65,191.79-5.31.83-10.65,1.46-16,2.19ZM270.35,357c25-10.44,32.65-33.45,28.25-51.94a44.19,44.19,0,0,0-85.86-.39c-4.49,17.83,2.41,41.63,28.16,52.33v5.24q0,35.33,0,70.65c0,5.85,2.1,10.57,7.11,13.66a13.67,13.67,0,0,0,14.46.43c5.38-2.92,7.89-7.61,7.89-13.67C270.35,408,270.35,382.63,270.35,357Z" transform="translate(-64.24 -5)"/>
                            <path d="M388.13,191c-19.22-7.11-38.59-9.06-58.89-8,0-2.41,0-4.31,0-6.21-.28-16.66.67-33.46-1.12-50-3.86-35.42-36-62.65-72.18-62.75-36.52-.09-68.33,27.09-73,62.61a161.6,161.6,0,0,0-.91,19.07c-.14,12.09,0,24.19,0,37.11-19.79-.26-39.54-.73-59.15,6.73,1.63-31-3-61.2,6-90.73C146.56,40.64,203.31,1.1,263.71,5.31,326,9.64,376.64,55.34,386.23,116a156.59,156.59,0,0,1,1.82,22C388.31,155.42,388.13,172.86,388.13,191Z" transform="translate(-64.24 -5)"/>
                            <path d="M255.75,330a14.7,14.7,0,1,1,14.58-14.7A14.86,14.86,0,0,1,255.75,330Z" transform="translate(-64.24 -5)"/>
                        </svg>
                    </span>
                </span>
                <div v-if="loading" class="loader">
                    <svg viewBox="0 0 676 676">
                        <circle cx="338" cy="338" r="308"/>
                    </svg>
                </div>
            </div>
            <div class="heading-toolsToggle">
                <button @click.stop="toggleMoreOptions" v-if="!loading">
                    <svg viewBox="0 0 502.74 110.01">
                        <path d="M507.33,264.41c-2.48,9.12-5.15,18.17-11.37,25.64-15.24,18.3-34.57,25.6-57.65,19.37-22.56-6.09-36.16-21.42-40-44.61-4.8-28.83,15.1-57.14,43.27-62.18,30.63-5.47,59,13.55,64.8,43.37a19.31,19.31,0,0,0,1,2.7Z" transform="translate(-4.59 -201.59)"/>
                        <path d="M4.59,256.49a55,55,0,1,1,109.91.37c-.12,30.13-25,54.7-55.21,54.65A54.86,54.86,0,0,1,4.59,256.49Z" transform="translate(-4.59 -201.59)"/>
                        <path d="M252.65,311.51C222.18,311.45,198,286.79,198,256c.08-30.13,24.88-54.5,55.33-54.36,30.05.15,54.72,25.15,54.56,55.32S282.89,311.57,252.65,311.51Z" transform="translate(-4.59 -201.59)"/>
                    </svg>
                </button>
                <div class="heading-moreOptions" :class="[moreOptions ? 'activeMoreOptions' : 'inActiveMoreOptions']">
                    <div class="moreOptions" @click.stop="moreOptions = true">
                        <form action="/" method="POST" v-if="userAddable" @submit.prevent="addContact" enctype="multipart/form-data">
                            <button type="submit" role="button">Add Contact</button>
                        </form>
                        <form action="/" method="POST" v-if="contactRemovable" @submit.prevent="removeContact" enctype="multipart/form-data">
                            <button type="submit" role="button">Remove Contact</button>
                        </form>
                        <form action="/" method="POST" v-if="requestCancellable" @submit.prevent="cancelRequest" enctype="multipart/form-data">
                            <button type="submit" role="button">Cancel Request</button>
                        </form>
                        <form action="/" method="POST" v-if="contactBlockable" @submit.prevent="blockUser" enctype="multipart/form-data">
                            <button>Block</button>
                        </form>
                        <form action="/" method="POST" v-if="!contactBlockable" @submit.prevent="unblcokUser" enctype="multipart/form-data">
                            <button>Unblock</button>
                        </form>
                    </div>
                </div>
                <div v-if="loading" class="loader">
                    <svg viewBox="0 0 676 676">
                        <circle cx="338" cy="338" r="308"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="ms-body-content" v-if="!loading">
            <perfect-scrollbar v-if="messageData.length > 0">
                <div class="chat-bubble-container" v-for="(message, i) in messageData" :key="i" :class="[message.sent ? 'sender' : 'receiver']">
                    <div class="chat-bubble-box">
                        <div class="chat-bubble-contents">
                            {{message.message}}
                        </div>
                        <span class="chat-timestamps">{{message.timestamp}}</span>
                    </div>
                </div>

                <!-- Break Here -->
            </perfect-scrollbar>
            <div class="msg-textbox-container">
                <form action="/" method="post" v-if="!contact.privacy" @submit.prevent="sendMessage($event)" enctype="multipart/form-data">
                    <div contenteditable="true" ref="messages" @keypress="sendMessage($event)" @keyup="modelizingInput" class="messageBox"></div>
                    <button type="submit" @click="sendMessage($event)" role="button">
                        <svg viewBox="0 0 489.77 489.65">
                            <path d="M11.11,491.26c2.26-1.94,4.31-4.2,6.79-5.79q63.83-40.75,127.76-81.32c5.07-3.21,9.09-7,9.85-13.29a16.27,16.27,0,0,0-31.33-7.94c-2.48,6.54-4.49,13.25-6.75,19.88-1.93,5.63-5.47,7.84-9.87,6.24-4.08-1.49-5.44-5.37-3.68-10.76,2.08-6.35,4.13-12.72,6.37-19,4.85-13.59,16.21-21.43,30.29-21.07a30.62,30.62,0,0,1,28.13,21.47A30.2,30.2,0,0,1,156,414.38c-16.61,11-33.5,21.5-50.36,32.08a9.88,9.88,0,0,0-4.85,7.11c-3.18,16.86,9.43,32.87,26.67,32.8,25.09-.1,44.68-11,58.85-31.66q38.19-55.79,76.37-111.59c2.25-3.29,4.46-6.6,6.74-9.86,3.35-4.82,7.56-6.06,11.23-3.38s3.95,6.59.69,11.37q-22.1,32.36-44.26,64.67c-12.86,18.79-25.77,37.56-38.56,56.4-14.49,21.34-34.27,34.17-59.91,37.75a12.49,12.49,0,0,0-2.23.76H122.07a23.43,23.43,0,0,0-2.68-.9c-12.21-2.6-21.75-9.23-27.6-20.14-3.24-6-4.8-13-7.25-19.86l-63.86,40.9H11.11Z" transform="translate(-11.11 -11.17)"/>
                            <path d="M349.71,241.42c-9.31,13.59-18.4,26.84-27.48,40.1-6.93,10.12-13.8,20.28-20.78,30.37-4.81,7-10.73,6.55-14.53-1q-20.54-41-40.91-82c-1.58-3.21-3.44-4.22-6.93-4.2-31.56.13-63.12.08-94.68.06-1.9,0-4.27.44-5.6-.49-2-1.42-4.18-3.63-4.78-5.9-.9-3.43,1.52-5.85,4.55-7.56q23.52-13.23,47-26.56L486.6,14a37.78,37.78,0,0,1,4.22-2.23,7,7,0,0,1,9.94,5.66,29.35,29.35,0,0,1,.1,4.3V290.48c0,.8,0,1.6,0,2.39-.18,7.05-4.65,10.15-11.18,7.46-14.26-5.88-28.46-11.94-42.68-17.93ZM457.81,58l-.6-.67c-1.23.94-2.49,1.85-3.69,2.82L313.66,173.79c-6.56,5.32-13.06,10.72-19.72,15.9a7,7,0,0,1-10.2-1.1c-2.37-3.06-1.94-6.88,1.14-9.86.8-.77,1.7-1.44,2.56-2.14q64-52,128-104c1-.78,1.85-1.66,2.77-2.5l-.46-.62L170.39,209.24l.34,1.12h5.11c17.37,0,34.79-.77,52.1.26,12.62.74,23.1-1.85,31.59-11.34a9.62,9.62,0,0,1,1.47-1.22c3.72-2.67,7.69-2.46,10.31.54,2.82,3.22,2.43,7.26-1.23,10.51a98,98,0,0,1-9.28,7.52c-3,2-3.16,3.85-1.54,7,11.33,22.28,22.44,44.67,33.63,67,.69,1.38,1.48,2.71,2.54,4.64Zm28.37,225.33V41.94L358,229.34Z" transform="translate(-11.11 -11.17)"/>
                        </svg>
                    </button>
                </form>
                <h4 class="private-alert" v-if="contact.privacy">{{`${contact.first_name} ${contact.surname}'s account is private. Only ${(contact.gender === 'female') ? 'her' : 'his'} contacts message ${(contact.gender === 'female') ? 'her' : 'him'}.`}}</h4>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: 'MessagesScreen',
        props: ['userName', 'userId'],
        data () {
            return {
                moreOptions: false,
                data: {
                    user: '',
                    message: ''
                }
            }
        },
        computed: {
            ...mapGetters([
                'contact',
                'messageData',
                'userMessageable',
                'contactBlockable',
                'userAddable',
                'requestCancellable',
                'contactRemovable',
                'userTarget',
                'loading',
            ])
        },
        methods: {
            async fetchMessages () {
                await this.$store.dispatch('fetchMessages', this.userName)
            },
            sendMessage ($event) {
                if (this.data.message.length > 0) {
                    if (!$event.shiftKey && $event.key === "Enter") {
                        this.data = {
                            "user": this.userTarget,
                            "message": this.data.message
                        }
                        this.$store.dispatch('sendMessage', this.data)
                        this.data = {
                            user: '',
                            message: ''
                        }
                        this.$refs.messages.innerHTML = ''
                    }
                }
            },
            modelizingInput () {
                this.data.message = this.$refs.messages.innerHTML
            },
            async addContact () {
                if (!this.loading) {
                    await this.$store.dispatch('sendContactRequest', this.userTarget)
                }
            },
            removeContact () {
                if (!this.loading) {
                    let inputs = {
                        'title': 'Remove Contact',
                        'id': this.userTarget,
                        'body': !this.contact.privacy ? `If you remove ${this.contact.first_name} from your contacts list, you need to send them another request and they should accept it to have them in your contacts list again!` : `<strong>${this.contact.first_name} ${this.contact.surname}</strong> has changed their account to <strong>private</strong>. By removing them from your contacts list you won't be able to send them any more messages!`,
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
            cancelRequest () {
                if (!this.loading) {
                    let inputs = {
                        'title': 'Cancel Request',
                        'id': this.userTarget,
                        'body': `Are you sure you want to cancel your request?`,
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
            blockUser () {
                if (!this.loading) {
                    let inputs = {
                        'title': `Block ${this.contact.first_name} ${this.contact.surname}`,
                        'id': this.userTarget,
                        'body': `You're about blocking <strong>${this.contact.first_name} ${this.contact.surname}</strong>. By blcking <strong>${this.contact.first_name}</strong>, they cannot send you a message or any more contact requests.`,
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
            async unblcokUser () {
                if (!this.loading) {
                    await this.$store.dispatch('unBlockContact', this.userTarget)
                }
            },
            toggleMoreOptions () {
                if (!this.loading) {
                    this.moreOptions = !this.moreOptions
                }
            },
            async messageListener () {
                await Echo.private(`individualMessageChannel.${this.userId}`)
                    .listen('IndividualMessagesEvent', (response) => {
                        this.$store.dispatch('createMessage', this.userName)
                    })
            },
        },
        watch: {
            'data': {
                handler: 'modelizingInput',
                deep: true
            }
        },
        mounted () {
            this.fetchMessages()
            this.messageListener()
            document.addEventListener('click', () => {
                this.moreOptions = false
            })
        }
    }
</script>
