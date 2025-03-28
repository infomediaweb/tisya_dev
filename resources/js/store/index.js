import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate'

const state = {
    user: null,
    currentPaginationNumber: null,
    homeCurrentTab: null
}

const store = createStore({
    state,
    getters:{
        user:(state) => {
            return state.user;
        },

        currentPaginationNumber: (state) => {
            return state.currentPaginationNumber;
        },

        homeCurrentTab:(state) => {
            return state.homeCurrentTab;
        },
    },
    mutations: {
        user: (state, user) => {
            state.user = user;
        },

        currentPaginationNumber: (state, number) => {
            state.currentPaginationNumber = number
        },

        homeCurrentTab: (state, active) => {
            state.homeCurrentTab = active;
        },

    },
    actions: {
        user: (context, user) => {
            context.commit('user', user);
        },

        currentPaginationNumber: (context, number) => {
            context.commit('currentPaginationNumber', number);
        },

        homeCurrentTab: (context, homeCurrentTab) => {
            context.commit('homeCurrentTab', homeCurrentTab);
        },
    },
     plugins: [
        createPersistedState()
    ],
})


export default store;
