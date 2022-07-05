import axios from "axios"

export default {
    state: {
        targetedUser: [],
        userAddable: true,
        userMessageable: true,
        requestRejectable: true,
        requestCancellable: true,
        requestAcceptable: true,
        contactBlockable: false,
        contactRemovable: false,
        markContactSpam: false,
        unMarkContactSpam: false,
        userTarget: '',
        loadingTargetedUser: false,
        profileMoreOptions: false,
    },
    getters: {
        targetedUser: state => {
            return state.targetedUser
        },
        userTarget: state => {
            return state.userTarget
        },
        userAddable: state => {
            return state.userAddable
        },
        userMessageable: state => {
            return state.userMessageable
        },
        requestCancellable: state => {
            return state.requestCancellable
        },
        requestRejectable: state => {
            return state.requestRejectable
        },
        requestAcceptable: state => {
            return state.requestAcceptable
        },
        contactBlockable: state => {
            return state.contactBlockable
        },
        contactRemovable: state => {
            return state.contactRemovable
        },
        markContactSpam: state => {
            return state.markContactSpam
        },
        unMarkContactSpam: state => {
            return state.unMarkContactSpam
        },
        loadingTargetedUser: state => {
            return state.loadingTargetedUser
        },
        profileMoreOptions: state => {
            return state.profileMoreOptions
        }
    },
    mutations: {
        collectUserInfo (state, info) {
            return state.targetedUser = info
        },
        selectUserTarget (state, target){
            return state.userTarget = target
        },
        userAddableCheck (state, status){
            return state.userAddable = status
        },
        userMessageableCheck (state, status) {
            return state.userMessageable = status
        },
        requestRejectableCheck (state, status) {
            return state.requestRejectable = status
        },
        requestCancellableCheck (state, status) {
            return state.requestCancellable = status
        },
        requestAcceptableCheck (state, status) {
            return state.requestAcceptable = status
        },
        contactBlockableCheck (state, status) {
            return state.contactBlockable = status
        },
        contactRemovableCheck (state, status) {
            return state.contactRemovable = status
        },
        markContactSpamCheck (state, status) {
            return state.markContactSpam = status
        },
        unMarkContactSpamCheck (state, status) {
            return state.unMarkContactSpam = status
        },
        unloadTargetUser (state){
            return state.targetedUser = []
        },
        toggleLoadingTargetedUser (state, status) {
            return state.loadingTargetedUser = status
        },
        profileMoreOptions (state, status) {
            return state.profileMoreOptions = status
        }
    },
    actions: {
        collectUserInfo ({commit, dispatch, state}) {
            commit('toggleLoadingTargetedUser', true)

            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(`/api/collect/searched/user/info/${state.userTarget}`)
                .then(res => {
                    if (res.data.code === 200) {
                        commit('collectUserInfo', res.data.user)
                        
                        dispatch('userAddableAction', res.data.userAddable)
                        dispatch('userMessageableAction', res.data.userMessageable)
                        dispatch('requestRejectableAction', res.data.requestRejectable)
                        dispatch('requestCancellableAction', res.data.requestCancellable)
                        dispatch('requestAcceptableAction', res.data.requestAcceptable)
                        dispatch('contactRemovableAction', res.data.contactRemovable)
                        dispatch('contactBocklableAction', res.data.contactBocklable)
                        dispatch('markContactSpamAction', res.data.markContactSpam)
                        dispatch('unMarkContactSpamAction', res.data.markedAsSpam)
                    }
                })
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {
                    commit('toggleLoadingTargetedUser', false)
                })
            })
        },
        unloadTargetUser ({commit}){
            commit('unloadTargetUser')
        },
        setUserTarget ({commit}, target) {
            commit('selectUserTarget', target)
        },
        userAddableAction ({commit}, data) {
            commit('userAddableCheck', data)
        },
        userMessageableAction ({commit}, data) {
            commit('userMessageableCheck', data)
        },
        requestRejectableAction ({commit}, data) {
            commit('requestRejectableCheck', data)
        },
        requestCancellableAction ({commit}, data) {
            commit('requestCancellableCheck', data)
        },
        requestAcceptableAction ({commit}, data) {
            commit('requestAcceptableCheck', data)
        },
        contactRemovableAction ({commit}, data) {
            commit('contactRemovableCheck', data)
        },
        contactBocklableAction ({commit}, data) {
            commit('contactBlockableCheck', data)
        },
        markContactSpamAction ({commit}, data) {
            commit('markContactSpamCheck', data)
        },
        unMarkContactSpamAction ({commit}, data) {
            commit('unMarkContactSpamCheck', data)
        },
        profileMoreOptions ({commit, state}) {
            let toggle = state.profileMoreOptions ? false : true
            commit('profileMoreOptions', toggle)
        }
    }
  }