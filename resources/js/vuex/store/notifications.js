import axios from "axios"

export default {
    state: {
        notifications: []
    },
    getters: {
        notifications: state => {
            return state.notifications
        }
    },
    mutations: {
        createNotifications (state, notification) 
        {
            state.notifications.unshift(notification)
        },
        fetchNotifications (state, notifications)
        {
            return state.notifications = notifications
        },
        markAllAsRead (state, notifications)
        {
            return state.notifications = notifications
        },
        deleteNotification (state, notification)
        {
            let index = state.notifications.findIndex(item.id === notification.id)
            state.notifications.splice(index, 1)
        }
    },
    actions: {
        fetchNotifications ({commit}) 
        {
            axios.get('/getNotifications')
                .then(response => {
                    commit('fetchNotifications', response.data)
                })
                .catch(err => {
                    console.log(err)
                })
        }
    }
  }