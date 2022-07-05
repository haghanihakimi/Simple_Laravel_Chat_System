import axios from "axios"

export default {
    state: {
        contacts: [],
        pendings: 0,
        pendingRequests: [],
        contactActionAlert: {'status': false},
        notificationPop: '',
        pendingResponse: false,
    },
    getters: {
        contacts: state => {
            return state.contacts
        },
        pendingResponse: state => {
            return state.pendingResponse
        },
        notificationPop: state => {
            return state.notificationPop
        },
        pendingRequests: state => {
            return state.pendingRequests
        },
        pendings: state => {
            return state.pendings
        },
        contactActionAlert: state => {
            return state.contactActionAlert
        }
    },
    mutations: {
        fetchContactRequests (state, inputs)
        {
            return state.contacts.push(...inputs)
        },
        sendContactRequest (state, contactRequest) 
        {
            state.contactRequests = contactRequest
        },
        fetchPendingRequests (state, pendings) {
            return state.pendingRequests = pendings
        },
        prepareNotificationPop (state, content) {
            return state.notificationPop = content
        },
        togglePendingResponse (state, status) {
            return state.pendingResponse = status
        },
        fillContactActionAlert (state, inputs){
            return state.contactActionAlert = inputs
        }
    },
    actions: {
        async fetchContacts ({commit}){
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(`/api/prepare/list/contacts`)
                .then(async response => {
                    await commit('fetchContactRequests', response.data.contacts)
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(() => {
                    commit('togglePendingResponse', false)
                })
            })
        },
        async receivedPendingContactRequests ({commit}) 
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get('/api/fetch/contact/received/pending/requests')
                .then(async response => {
                    await commit('fetchPendingRequests', response.data.pendings)
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(() => {
                    commit('togglePendingResponse', false)
                })
            })
        },
        async sentPendingContactRequests ({commit}) 
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get('/api/fetch/contact/sent/pending/requests')
                .then(async response => {
                    await commit('fetchPendingRequests', response.data.pendings)
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(() => {
                    commit('togglePendingResponse', false)
                })
            })
        },
        async sendContactRequest ({commit, dispatch}, contactRequest)
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/request/${contactRequest}`, '', '')
                    .then(response => {
                        dispatch('userAddableAction', response.data.interact.add)
                        dispatch('requestCancellableAction', response.data.interact.cancel)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('togglePendingResponse', false)
                    })
            })   
        },
        async cancelContactRequest ({commit, dispatch}, contactRequest)   
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/cancelation/${contactRequest}`, '', '')
                    .then(response => {
                        dispatch('userAddableAction', response.data.interact.add)
                        dispatch('requestCancellableAction', response.data.interact.cancel)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('togglePendingResponse', false)
                    }) 
            })  
        },
        async rejectContactRequest ({commit, dispatch}, contactRequest)
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/reject/${contactRequest}`, '', '')
                    .then(response => {
                        dispatch('userAddableAction', response.data.interact.add)
                        dispatch('requestRejectableAction', response.data.interact.reject)
                        dispatch('requestAcceptableAction', response.data.interact.accept)
                        dispatch('userMessageableAction', response.data.interact.message)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('togglePendingResponse', false)
                    })
            })  
        },
        async acceptContactRequest ({commit, dispatch}, contactRequest)
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/approve/${contactRequest}`, '', '')
                    .then(response => {
                        dispatch('userAddableAction', response.data.interact.add)
                        dispatch('requestRejectableAction', response.data.interact.reject)
                        dispatch('requestAcceptableAction', response.data.interact.accept)
                        dispatch('userMessageableAction', response.data.interact.message)
                        dispatch('contactRemovableAction', response.data.interact.remove)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('togglePendingResponse', false)
                    })
            })
        },
        async removeContact ({commit, dispatch}, contactRequest)
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/remove/${contactRequest}`, '', '')
                    .then(response => {
                        dispatch('userAddableAction', response.data.interact.add)
                        dispatch('userMessageableAction', response.data.interact.message)
                        dispatch('contactRemovableAction', response.data.interact.remove)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('togglePendingResponse', false)
                    })
            }) 
        },
        async blockContact ({commit, dispatch}, contactRequest)
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/block/${contactRequest}`, '', '')
                .then(response => {    
                    dispatch('userAddableAction', response.data.userAddable)
                    dispatch('userMessageableAction', response.data.userMessageable)
    
                    dispatch('markContactSpamAction', response.data.interact.spam)
                    dispatch('requestRejectableAction', response.data.interact.reject)
                    dispatch('requestCancellableAction', response.data.interact.cancel)
                    dispatch('requestAcceptableAction', response.data.interact.accept)
                    dispatch('contactRemovableAction', response.data.interact.remove)
                    dispatch('contactBocklableAction', response.data.interact.block)
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(() => {
                    commit('togglePendingResponse', false)
                }) 
            })  
        },
        async unBlockContact ({commit, dispatch}, contactRequest)
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/unblock/${contactRequest}`, '', '')
                .then(response => {
                    dispatch('userAddableAction', response.data.interact.add)
                    dispatch('userMessageableAction', response.data.interact.message)
                    dispatch('contactBocklableAction', response.data.interact.block)
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(() => {
                    commit('togglePendingResponse', false)
                })
            })
        },
        async markAsSpam ({commit, dispatch}, contactRequest)
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/mark/spam/${contactRequest}`, '', '')
                    .then(response => {
                        dispatch('userAddableAction', response.data.interact.add)
                        dispatch('requestRejectableAction', response.data.interact.reject)
                        dispatch('requestAcceptableAction', response.data.interact.accept)
                        dispatch('unMarkContactSpamAction', response.data.interact.hasSpammed)
                        dispatch('markContactSpamAction', response.data.interact.spam)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('togglePendingResponse', false)
                    })
            }) 
        },
        async unMarkAsSpam ({commit, dispatch}, contactRequest)
        {
            commit('togglePendingResponse', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post(`/api/send/contact/unmark/spam/${contactRequest}`, '', '')
                    .then(response => {
                        dispatch('userAddableAction', response.data.interact.add)
                        dispatch('unMarkContactSpamAction', response.data.interact.hasSpammed)
                        dispatch('markContactSpamAction', response.data.interact.spam)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('togglePendingResponse', false)
                    })
            })  
        },
        markPendingRequests ({commit}, index) {
            commit('markViewedPendingRequests', index)
        },
        prepareNotificationPop ({commit}, content) {
            commit('prepareNotificationPop', content)
        },
        fillContactActionAlert ({commit}, inputs) {
            commit('fillContactActionAlert', inputs)
        }
    }
  }