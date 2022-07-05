import axios from "axios"

export default {
    state: {
        profiles: [],
        updating: false,
        profileActive: false,
        readyToSave: false,
    },
    getters: {
        profiles: state => {
            return state.profiles
        },
        updating: state => {
            return state.updating
        },
        profileActive: state => {
            return state.profileActive
        },
        readyToSave: state => {
            return state.readyToSave
        }
    },
    mutations: {
        fetchProfiles (state, profiles)
        {
            return state.profiles = profiles
        },
        toggleSavings (state, status) {
            return state.updating = status
        },
        toggleProfiles (state, status) {
            return state.profileActive = status
        },
        toggleReadyToSave (state, status) {
            return state.readyToSave = status
        }
    },
    actions: {
        fetchProfiles ({commit, state, dispatch}) 
        {
            if (state.profiles.length <= 0) {
                axios.get('/api/profiles/collect')
                .then(async response => {
                    dispatch('updateErrors', {"code": 200, "title": "Profile Data Synced", "message": response.data.message, "button": 'dismiss', 'popup': (response.data.code !== 200) ? true : false},{root:true});
                    await commit('fetchProfiles', response.data.profiles)
                })
                .catch(err => {
                    dispatch('updateErrors', {"code": 500, "title": "Profile Sync Failure!", "message": err, "button": 'dismiss', 'popup': true},{root:true});
                })
                .finally(() => {
                })
            }
        },
        async updateProfiles ({commit, dispatch}, profile_data)
        {
            commit('toggleSavings', true)
            await axios.post('/api/profiles/update', profile_data, '')
                .then(response => {
                    commit('fetchProfiles', response.data.profiles)
                })
                .catch(err => {
                    dispatch('updateErrors', {"code": 500, "title": "Dark Mode Failure!", "message": err, "button": 'dismiss', 'popup': true},{root:true});
                })
                .finally(() => {
                    commit('toggleSavings', false)
                })      
        },
        async deleteProfiles ({commit, dispatch}, preference)
        {
            
        },
        toggleProfiles ({commit}, status) {
            commit('toggleProfiles', status)
        },
        toggleReadyToSave ({commit}, status) {
            commit('toggleReadyToSave', status)
        }
    }
  }