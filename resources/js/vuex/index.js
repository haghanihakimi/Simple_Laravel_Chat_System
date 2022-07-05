import Vue from 'vue'
import VueX from 'vuex'
import generals from './store/generals'
import notifications from './store/notifications'
import messages from './store/messages'
import contacts from './store/contacts'
import preferences from './store/preferences'
import errors from './store/errors'
import notificationsPops from './store/notificationsPops'
import profile from './store/profile'
import peopleSearch from './store/peopleSearch'
import profileView from './store/profileView'
import signout from './store/signout'
import SecuritySettings from './store/SecuritySettings'
import conversations from './store/conversations'

Vue.use(VueX)

export default new VueX.Store({
  modules: {
    generals,
    notifications,
    messages,
    contacts,
    preferences,
    errors,
    notificationsPops,
    profile,
    peopleSearch,
    profileView,
    signout,
    SecuritySettings,
    conversations,
  }
})