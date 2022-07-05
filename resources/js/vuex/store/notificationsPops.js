import axios from "axios"

export default {
    state: {
        notificationspop: ['First Notification', 'Second Notification', 'Final Notification']
    },
    getters: {
        notificationspop: state => {
            return state.notificationspop
        }
    },
    mutations: {
        fetchNotificationsPop (state, notification)
        {
            return state.notificationspop = notification
        },
        markAsRead (state, notification)
        {
            return state.notificationspop = notification
        }
    },
    actions: {
        fetchnotificationspop ({commit}) 
        {
            axios.get('/notifications/popups')
                .then(response => {
                    commit('fetchNotificationsPop', response.data)
                })
                .catch(err => {
                    console.log(err)
                })
        },
        markAsRead({commit}, notification){
            const meta = document.querySelector('head meta[name="csrf-token"]')
            const headers = {headers: {"X-CSRF-TOKEN": meta.getAttribute('content')}}

            axios.post(`/mark/notification/seen/${notification}`, null, headers)
                .then((response) => {
                    commit('markAsRead', response.data)
                })
                .catch ((err) => {
                    console.log(err)
                })
                .finally(() => {
                    console.log('finally we got there!')
                })
        }
    }
  }