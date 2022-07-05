import axios from "axios"

export default {
    state: {
        peopleSearch: [],
        loadingPeople: false,
        peopleSearchLastPage: 1
    },
    getters: {
        peopleSearch: state => {
            return state.peopleSearch
        },
        loadingPeople: state => {
            return state.loadingPeople
        },
        peopleSearchLastPage: state => {
            return state.peopleSearchLastPage
        }
    },
    mutations: {
        fetchPeopleSearch (state, people)
        {
            return state.peopleSearch = people
        },
        toggleLoading(state, status) {
            return state.loadingPeople = status
        },
        togglePeopleSearchLastPage (state, lastPage) {
            return state.peopleSearchLastPage = lastPage
        }
    },
    actions: {
        fetchPeopleSearch ({commit}, inputs) 
        {
            //commit('toggleLoading', true)
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(`/api/peopleSearch`, {params: {keywords: inputs}})
                .then(response => {
                    if (response.data.code === 200) {
                        commit('fetchPeopleSearch', response.data.people)
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally (() => {
                    //commit('toggleLoading', false)
                })
            })
        }
    }
  }