/**
 * Score module vuex store
 */

import * as types from '@js/store/game/types'

export default {
  state: {
    life: 0,
  },
  mutations: {
    /**
     * On first load
     */
    [types.MUTATION.LOAD] (state, obj) {
      const me = obj.players.find((player) => player.me)
      if (me) {
        state.life = me.life
      }
    },
  },
  actions: {},
  getters: {},
  strict: process.env.NODE_ENV !== 'production',
}
