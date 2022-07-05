import axios from "axios"

export default {
    state: {
        user: [],
        deletionAlert: false,
        pendingFetch: false,
        qrCodeSvg: null,
        twoFaRecoveryCodes: null,
        SecurityPasswordConfirm: false,
        userInputs: {
            email: '',
            email_confirmation: '',
            password: ''
        },
        emailChangeResponse: '',
        SecuritySettingsErrors: [],
        passwordRequestResponse: '',
        securitySettingsSaveReady: false,
    },
    getters: {
        user: state => {
            return state.user
        },
        SecurityPasswordConfirm: state => {
            return state.SecurityPasswordConfirm
        },
        userInputs: state => {
            return state.userInputs
        },
        pendingFetch: state => {
            return state.pendingFetch
        },
        emailChangeResponse: state => {
            return state.emailChangeResponse
        }, 
        SecuritySettingsErrors: state => {
            return state.SecuritySettingsErrors
        },
        passwordRequestResponse: state => {
            return state.passwordRequestResponse
        },
        deletionAlert: state => {
            return state.deletionAlert
        },
        securitySettingsSaveReady: state => {
            return state.securitySettingsSaveReady
        },
        qrCodeSvg: state => {
            return state.qrCodeSvg
        },
        twoFaRecoveryCodes: state => {
            return state.twoFaRecoveryCodes
        }
    },
    mutations: {
        fetchUser (state, user) {
            return state.user = user
        },
        userInputs (state, inputs) {
            return state.userInputs = inputs
        },
        togglePendingFetch (state, status) {
            return state.pendingFetch = status
        },
        fillErrors (state, fillables) {
            return state.emailChangeResponse = fillables
        },
        fillPasswordRequestResponse (state, fillables) {
            return state.passwordRequestResponse = fillables
        },
        toggleDeletionAlert (state, status) {
            return state.deletionAlert = status
        },
        securitySettingsSaveReady (state, status) {
            return state.securitySettingsSaveReady = status
        },
        setQrCodeSvg (state, svg) {
            return state.qrCodeSvg = svg
        },
        fillRecoverCodes (state, codes) {
            return state.twoFaRecoveryCodes = codes
        },
        checkSecurityPasswordConfirm (state, status) {
            return state.SecurityPasswordConfirm = status
        },
        fillSecuritySettingsErrors (state, errors) {
            return state.SecuritySettingsErrors = errors
        }
    },
    actions: {
        async fetchUser ({commit}){
            commit('togglePendingFetch', true)
            
            await axios.get('/api/user/settings/security/profile')
                .then(response => {
                    commit('fetchUser', response.data.user)
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(() => {
                    commit('togglePendingFetch', false)
                })
        },
        async saveSecuritySettings ({commit}, inputs){
            commit('togglePendingFetch', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post('/api/user/update/security/settings/changes', inputs)
                    .then(response => {
                        commit('fillSecuritySettingsErrors', response.data)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        commit('togglePendingFetch', false)
                    })
            })
        },
        async passwordResetRequest ({commit}) {
            commit('togglePendingFetch', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post('/api/auth/forgot-password')
                    .then(response => {
                        if (response.data.code === 200) {
                            commit('fillPasswordRequestResponse', response.data.message)
                        } else {
                            commit('fillPasswordRequestResponse', response.data.message)
                        }
                    })
                    .catch (error => {
                        console.log(error)
                    })
                    .finally(() => {
                        commit('togglePendingFetch', false)
                    })
            })
        },
        async deleteAccount ({commit}) {
            commit('togglePendingFetch', true)

            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post('/api/users/accounts/current/delele/perm')
                    .then(response => {
                        if (response.data.code === 200) {
                            window.location.href = '/login'
                        } else {
                            commit('fillPasswordRequestResponse', response.data.message)
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    })
                    .finally(() => {
                        commit('togglePendingFetch', false)
                    })
            })
            .catch (error => {
                console.log(error)
            })
            .finally(() => {
                commit('togglePendingFetch', false)
            })
        },

        //Two Factor Authentication section starts
        async twoFaPassConfirm ({commit, dispatch}, userPassword) {
            commit('togglePendingFetch', true)
            await axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post('/api/user/check/chk-confirm-password', userPassword).then(response => {
                    if (response.data.confirmed) {
                        dispatch('checkSecurityPasswordConfirm', response.data.confirmed)
                        commit('securitySettingsSaveReady', false)
                    }
                }).catch(errors => {
                    console.log(errors)
                }).finally(() => {
                    commit('togglePendingFetch', false)
                })
            }).catch(errors => {
                console.log(errors)
            })
        },
        async enableTwoFa ({commit, dispatch, state}) {
            commit('togglePendingFetch', true)

            await dispatch('confirmPasswordStatus').then(() => {
                if (state.SecurityPasswordConfirm) {
                    axios.get('/sanctum/csrf-cookie').then(() => {
                        axios.post('/api/user/enable/act-two-factor-auth').then(response => {
                            commit('setQrCodeSvg', response.data)

                            state.user.is_twofa = true
                            console.log("Two Factor Authentication is activate now!")
                        }).catch (errors => {
                            console.log(errors)
                        }).finally(() => {
                            commit('togglePendingFetch', false)
                        })
                    }).catch(errors => {
                        console.log(errors)
                    })
                } else {
                    commit('securitySettingsSaveReady', true)
                }
            })
        },
        async disableTwoFa ({commit, dispatch, state}) {
            commit('togglePendingFetch', true)

            await dispatch('confirmPasswordStatus').then(() => {
                if (state.SecurityPasswordConfirm) {
                    axios.get('/sanctum/csrf-cookie').then(() => {
                        axios.post('/api/user/disable/deact-two-factor-auth').then(response => {
                            if (response.data.status) {
                                state.user.is_twofa = false
                                console.log("Two Factor Authentication is deactivated")
                            }
                        }).catch (error => {
                            console.log(error)
                        }).finally(() => {
                            commit('togglePendingFetch', false)
                        })
                    }).catch(errors => {
                        console.log(errors)
                    })
                } else {
                    commit('securitySettingsSaveReady', true)
                }
            })
        },
        async twoFaQrCode ({commit, state, dispatch}) {
            commit('togglePendingFetch', true)

            await dispatch('confirmPasswordStatus').then(() => {
                if (state.SecurityPasswordConfirm) {
                    axios.get('/api/user/twofa/twofa-qr-code').then(response => {
                        commit('setQrCodeSvg', response.data)
                    }).catch(errors => {
                        console.log(errors)
                    }).finally(() => {
                        commit('togglePendingFetch', false)
                    })
                } else {
                    commit('securitySettingsSaveReady', true)
                }
            })
        },
        async confirmPasswordStatus ({commit, dispatch}) {
            commit('togglePendingFetch', true)

            await axios.get('/api/user/check/chk-password-status').then(response => {
                dispatch('checkSecurityPasswordConfirm', response.data.confirmed)
            }).catch(error => {
                console.log(error)
            }).finally(() => {
                commit('togglePendingFetch', false)
            })
        },
        async viewRecoveryCodes ({commit, dispatch, state}) {
            commit('togglePendingFetch', true)

            await dispatch('confirmPasswordStatus').then(() => {
                if (state.SecurityPasswordConfirm) {
                    axios.get('/api/user/twofa/twofa-recovery-codes').then(response => {
                        commit('fillRecoverCodes', response.data)
                    }).catch(errors => {
                        console.log(errors)
                    }).finally(() => {
                        commit('togglePendingFetch', false)
                    })
                } else {
                    commit('securitySettingsSaveReady', true)
                }
            })
        },
        //Two Factor Authentication section ends

        toggleDeletionAlert ({commit}, status) {
            commit('toggleDeletionAlert', status)
        },
        securitySettingsSaveReady ({commit}, status) {
            commit('securitySettingsSaveReady', status)
        },
        userInputs ({commit}, inputs) {
            commit('userInputs', inputs)
        },
        checkSecurityPasswordConfirm ({commit}, status) {
            commit('checkSecurityPasswordConfirm', status)
        }
    }
  }