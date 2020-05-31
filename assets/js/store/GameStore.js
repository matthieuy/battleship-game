/**
 * Vuex store for running page
 */

// Imports
import Vue from 'vue'
import Vuex from 'vuex'
import mutations from '@js/store/game/mutations'
import actions from '@js/store/game/actions'
import getters from '@js/store/game/getters'
/* global Translator */

// Init
Vue.use(Vuex)

// State
const state = {
  userId: null,
  slug: '',
  grid: [],
  players: [],
  me: null,
  size: null,
  boxSize: 20,
  tour: [],
  options: {},
  gameover: false,
  status: Translator.trans('loading', {}, 'js'),
}

// Export store
export default new Vuex.Store({
  state,
  mutations,
  getters,
  actions,
  modules: {},
  strict: process.env.NODE_ENV !== 'production',
})
