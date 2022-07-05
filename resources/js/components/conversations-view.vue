<template>
    <div class="conversations__view">
        <perfect-scrollbar>
            <TransitionGroup v-if="!loadingConversations && conversations.length > 0" tag="ul" name="fade" class="conversations__list">
                <li v-for="(conversation, i) in conversations[0]" class="conversation__item" :key="i">
                    <a :href="`/messages/${conversation.username}`">
                        <div class="conversations__box">
                            <div class="item__image">
                                <img :src="`/storage/locals/${conversation.profile_picture}`" :alt="`${conversation.first_name} ${conversation.surname} profile picture`">
                            </div>
                            <div class="item__info">
                                <span>{{conversation.first_name}} {{conversation.surname}}</span>
                            </div>
                            <div class="rmv__conversation">
                                <form action="/" method="post" @submit.prevent="deleteConversation(conversation.conversation_id)" enctype="multipart/form-data">
                                    <button type="submit" name="remove_conversation">
                                        <svg viewBox="0 0 454.41 454.41">
                                            <path d="M326.72,256,468.56,114.17a50,50,0,0,0,0-70.73h0a50,50,0,0,0-70.73,0L256,185.28,114.17,43.44a50,50,0,0,0-70.73,0h0a50,50,0,0,0,0,70.73L185.28,256,43.44,397.83a50,50,0,0,0,0,70.73h0a50,50,0,0,0,70.73,0L256,326.72,397.83,468.56a50,50,0,0,0,70.73,0h0a50,50,0,0,0,0-70.73Z" transform="translate(-28.8 -28.8)"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </a>
                </li>
            </TransitionGroup>
            <div v-if="loadingConversations" class="loader">
                <svg viewBox="0 0 676 676">
                    <circle cx="338" cy="338" r="308"/>
                </svg>
            </div>
            <h2 v-if="loadingConversations || conversations.length <= 0" class="empty__conversations">No Conversation Found</h2>
        </perfect-scrollbar>
    </div>
</template>
<script>
    import { mapGetters } from 'vuex'

    export default {
        name: "ConversationsView",
        data () {
            return {

            }
        },
        computed: {
            ...mapGetters([
                'conversations',
                'loadingConversations'
            ])
        },
        methods: {
            async getConversations () {
                await this.$store.dispatch('getConversation')
            },
            async deleteConversation (conversation) {
                await this.$store.dispatch('deleteConversation', {conversation_id: conversation})
            }
        },
        created () {
            this.getConversations()
        }
    }
</script>