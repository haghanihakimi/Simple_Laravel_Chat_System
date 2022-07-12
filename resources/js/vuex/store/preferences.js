import axios from "axios"

export default {
    state: {
        preferences: [],
        saving: false
    },
    getters: {
        preferences: state => {
            return state.preferences
        },
        saving: state => {
            return state.saving
        }
    },
    mutations: {
        fetchPreferences (state, preferences)
        {
            return state.preferences = preferences
        },
        toggleSavings (state, status) {
            return state.saving = status
        }
    },
    actions: {
        async fetchPreferences ({commit, dispatch}) 
        {            
            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get('/api/preferences/collect')
                    .then(response => {
                        dispatch('updateErrors', {"code": 200, "title": "Data Collected", "message": response.data.message, "button": 'dismiss', 'popup': (response.data.code !== 200) ? true : false},{root:true});
                        commit('fetchPreferences', response.data.settings)
                    })
                    .catch(err => {
                        dispatch('updateErrors', {"code": 500, "title": "Data Collection Failure!", "message": err, "button": 'dismiss', 'popup': true},{root:true});
                    })
            })
        },
        async updateDarkMode ({commit, dispatch}, preference)
        {
            commit('toggleSavings', true)
            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post('/api/preferences/update/dark/mode', preference, '')
                .then(response => {
                    dispatch('updateErrors', {"code": response.data.code, "title": response.data.title, "message": response.data.message, "button": 'dismiss', 'popup': (response.data.code !== 200) ? true : false},{root:true});
                    if (response.data.code === 200) {
                        commit('fetchPreferences', response.data)
                    }
                })
                .catch(err => {
                    dispatch('updateErrors', {"code": 500, "title": "Dark Mode Failure!", "message": err, "button": 'dismiss', 'popup': true},{root:true});
                })
                .finally(() => {
                    commit('toggleSavings', false)
                }) 
            })       
        }
    }
  }