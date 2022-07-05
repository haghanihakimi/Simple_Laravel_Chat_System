import axios from "axios"

export default {
    state: {
        messageData: [],
        contact: [],
        loading: false,
    },
    getters: {
        messageData: state => {
            return state.messageData
        },
        contact: state => {
            return state.contact
        },
        loading: state => {
            return state.loading
        },
    },
    mutations: {
        fetchContactInfo (state, info) {
            return state.contact = info
        },
        fetchMessages (state, data) {
            return state.messageData.unshift(data)
        },
        pushNewMessage (state, message) {
            return state.messageData.push(message)
        },
        toggleLoading (state, status) {
            return state.loading = status
        },
    },
    actions: {
        async fetchMessages ({commit}, username) 
        {
            commit('toggleLoading', true)
            
            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(`/api/messages/fetch/data/messages/${username}`)
                    .then(response => {
                        commit('fetchContactInfo', response.data.user)
                        commit('userMessageableCheck', response.data.interacts.message)
                        commit('contactBlockableCheck', response.data.interacts.block)
                        commit('userAddableCheck', response.data.interacts.add)
                        commit('requestCancellableCheck', response.data.interacts.cancel)
                        commit('contactRemovableCheck', response.data.interacts.remove)
                        commit('selectUserTarget', response.data.destination)
                        for (var i = 0;i < response.data.messages.length;i++) {
                            commit('fetchMessages', response.data.messages[i])
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('toggleLoading', false)
                    })
            })
        },
        sendMessage ({dispatch}, data) {
            axios.get('/sanctum/csrf-cookie').then(() => {

                axios.post(`/api/messages/send/new/${data.user}`, data, '')
                    .then(res => {
                        dispatch('createMessage', res.data.user.username)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                    })
            })
        },
        createMessage ({commit}, target) 
        {
            axios.get(`/api/messages/fetch/data/messages/recent/${target}`)
                .then(response => {
                    commit('fetchContactInfo', response.data.user)
                    commit('userMessageableCheck', response.data.messageable)
                    commit('contactBlockableCheck', response.data.blockStatus)
                    commit('userAddableCheck', response.data.addable)
                    commit('selectUserTarget', response.data.destination)
                    commit('requestCancellableCheck', response.data.cancellableReq)
                    commit('contactRemovableCheck', response.data.remove)

                    commit('pushNewMessage', response.data.messages)
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(() => {
                })
        }
    }
  }