export default {
    state: {
        errors: []
    },
    getters: {
        errors: state => {
            return state.errors
        }
    },
    mutations: {
        updateErrors (state, error) 
        {
            return state.errors = error
        }
    },
    actions: {
        updateErrors ({commit}, error) 
        {
            switch (error.code) {
                case 200:
                    commit('updateErrors', error)
                    break;
                case 500:
                    commit('updateErrors', error)
                    break;
                case 419:
                    commit('updateErrors', error)
                    break;
                default:
                    commit('updateErrors', {
                        "code": 520, "title": "Unknown error occurred", 
                        "message": "Sorry, an unknown error occurred during processing your request. Please try again later.",
                        "button": 'dismiss'
                    })
                    break;
            }
        },
    }
  }