/**
 * Vuex store for waiting page
 */

// Imports
import Vue from 'vue'
import Vuex from 'vuex'
import mutations from './waiting/mutations'
import actions from './waiting/actions'

// Init
Vue.use(Vuex)

// State
const state = {
  players: [],
  game: {},
  joined: false,
  userId: null,
  me: null, // Player
  isCreator: false,
  creatorName: '',
  loaded: false,
  slug: '',
}

// Export store
export default new Vuex.Store({
  state,
  mutations,
  getters: {},
  actions,
  modules: {},
  strict: process.env.NODE_ENV !== 'production',
})
