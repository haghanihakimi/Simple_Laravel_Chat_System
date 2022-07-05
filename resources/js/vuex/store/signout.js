import axios from "axios"

export default {
    state: {
        signResponse: false,
    },
    getters: {
        signResponse: state => {
            return state.signResponse
        }
    },
    mutations: {
        signResponse (state, sign) {
            return state.signResponse = sign
        }
    },
    actions: {
        signout ({commit, state}) {
            if (!state.signResponse) {
                commit('signResponse', true)

                axios.get('/sanctum/csrf-cookie').then(() => {
                    axios.post('/api/user/log/sign/out')
                        .then(response => {
                            if (response.data.signature === 'succeeded') {
                                window.location.href = '/login'
                            }
                        })
                        .catch(err => {
                            console.log(err)
                        })
                        .finally(() => {
                            commit('signResponse', false)
                        })
                })
            }
        }
    }
  }