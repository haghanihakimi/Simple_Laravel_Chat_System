export default {
  state: {
    leftActivePanels: false,
    rightActivePanels: false,
    preferencesShow: false,
    notificationsShow: false,
    searchShow: false,
    messagesListShow: false,
    conversationsListShow: false,
    contactsShow: false,
    profilePreview: false,
    profileView: false,
  },
  getters: {
    preferencesShow: state => {
      return state.preferencesShow
    },
    notificationsShow: state => {
      return state.notificationsShow
    },
    searchShow: state => {
      return state.searchShow
    },
    messagesListShow: state => {
      return state.messagesListShow
    },
    contactsShow: state => {
      return state.contactsShow
    },
    profilePreview: state => {
      return state.profilePreview
    },
    rightActivePanels: state => {
      return state.rightActivePanels
    },
    leftActivePanels: state => {
      return state.leftActivePanels
    },
    profileView: state => {
      return state.profileView
    },
    conversationsListShow: state => {
      return state.conversationsListShow
    }
  },
  mutations: {
    toggleProfileView (state, status) {
      return state.profileView = status
    },
    togglePreferences (state, status) {
      return state.preferencesShow = status
    },
    toggleNotificationsList (state, status) {
      return state.notificationsShow = status
    },
    toggleSearchBox (state, status){
      return state.searchShow = status
    },
    toggleMessagesList (state, status) {
      return state.messagesListShow = status
    }, 
    toggleContactShow (state, status){
      return state.contactsShow = status
    },
    toggleProfilePreview (state, status) {
      return state.profilePreview = status
    },
    toggleLeftPanel (state, status) {
      return state.leftActivePanels = status
    },
    toggleRightPanel (state, status) {
      return state.rightActivePanels = status
    },
    toggleConversationsListShow (state, status) {
      return state.conversationsListShow = status
    }
  },
  actions: {
    toggleProfileView ({commit}, selfStatus, status) {
      commit('toggleProfileView', selfStatus)
      commit('toggleLeftPanel', selfStatus)

      commit('toggleConversationsListShow', status)
      commit('toggleRightPanel', status)
      commit('togglePreferences', status)
      commit('toggleNotificationsList', status)
      commit('toggleSearchBox', status)
      commit('toggleMessagesList', status)
      commit('toggleContactShow', status)
      commit('toggleProfilePreview', status)
    },
    toggleSearchBox ({commit}, selfStatus, status) {
      commit('toggleSearchBox', selfStatus)
      commit('toggleLeftPanel', selfStatus)

      commit('toggleConversationsListShow', status)
      commit('toggleProfileView', status)
      commit('toggleRightPanel', status)
      commit('togglePreferences', status)
      commit('toggleNotificationsList', status)
      commit('toggleMessagesList', status)
      commit('toggleContactShow', status)
      commit('toggleProfilePreview', status)
    },
    toggleContactShow ({commit}, selfStatus, status) {
      commit('toggleContactShow', selfStatus)
      commit('toggleLeftPanel', selfStatus)

      commit('toggleConversationsListShow', status)
      commit('toggleProfileView', status)
      commit('toggleRightPanel', status)
      commit('togglePreferences', status)
      commit('toggleNotificationsList', status)
      commit('toggleMessagesList', status)
      commit('toggleSearchBox', status)
      commit('toggleProfilePreview', status)
    },
    togglePreferences ({commit}, selfStatus, status) {
      commit('togglePreferences', selfStatus)
      commit('toggleLeftPanel', selfStatus)

      commit('toggleConversationsListShow', status)
      commit('toggleProfileView', status)
      commit('toggleRightPanel', status)
      commit('toggleNotificationsList', status)
      commit('toggleMessagesList', status)
      commit('toggleContactShow', status)
      commit('toggleSearchBox', status)
      commit('toggleProfilePreview', status)
    },
    toggleProfilePreview ({commit}, selfStatus, status) {
      commit('toggleProfilePreview', selfStatus)
      commit('toggleRightPanel', selfStatus)

      commit('toggleConversationsListShow', status)
      commit('toggleProfileView', status)
      commit('toggleNotificationsList', status)
      commit('toggleMessagesList', status)
      commit('toggleContactShow', status)
      commit('toggleSearchBox', status)
      commit('togglePreferences', status)
      commit('toggleLeftPanel', status)
    },
    toggleConversationsListShow ({commit}, selfStatus, status) {
      commit('toggleConversationsListShow', selfStatus)
      
      commit('toggleContactShow', status)
      commit('toggleLeftPanel', selfStatus)
      commit('toggleProfileView', status)
      commit('toggleRightPanel', status)
      commit('togglePreferences', status)
      commit('toggleNotificationsList', status)
      commit('toggleMessagesList', status)
      commit('toggleSearchBox', status)
      commit('toggleProfilePreview', status)
    }
  },
  methods: {
    deactivatePanels () {
      this.$store.state.generals.leftActivePanels = false
      this.$store.state.generals.rightActivePanels = false
      this.$store.state.generals.preferencesShow = false
      this.$store.state.generals.profilePreview = false
      this.$store.state.generals.searchShow = false
      this.$store.state.generals.conversationsListShow = false
    }
  },
  mounted () {
    window.addEventListener('click', () => {
      this.deactivatePanels()
      Array.prototype.max = () => {
          return Math.max.apply(null, this)
      }
      Array.prototype.min = () => {
          return Math.min.apply(null, this)
      }
      var items = document.querySelectorAll('.pricing-vertical-cols')
      var maximum = []
      for (var i = 0;i < items.length;i++) {
          maximum.push(parseInt(items[i].offsetHeight))
      }
      for (var i = 0;i < items.length;i++) {
          items[i].style.height = maximum.max() + 'px'
      }
    })
  }
}