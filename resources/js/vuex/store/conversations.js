import axios from "axios"

export default {
    state: {
        conversations: [],
        loadingConversations: false,
    },
    getters: {
        conversations: state => {
            return state.conversations
        },
        loadingConversations: state => {
            return state.loadingConversations
        }
    },
    mutations: {
        getConversations (state, conversations) {
            return state.conversations.push(conversations)
        },
        toggleLoadingConversations (state, status) {
            return state.loadingConversations = status
        }
    },
    actions: {
        getConversation ({commit}) {
            commit('toggleLoadingConversations', true)

            axios.get('/api/chats/conversation/pull').then(response => {
                if (response.data.conversations.length > 0) {
                    commit('getConversations', response.data.conversations)
                }
            }).catch(errors => {
                console.log(errors)
            }).finally(() => {
                commit('toggleLoadingConversations', false)
            })
        },
        deleteConversation ({commit, state}, conversation)
        {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/chats/conversation/delete`, conversation, '')
                    .then(response => {
                        if (response.data.code === 200) {
                            let i = state.conversations[0].map(conversations => conversations.conversation_id).indexOf(conversation.conversation_id) // find index of your object
                            state.conversations[0].splice(i, 1)
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                    })
            })
        },
    }
  }