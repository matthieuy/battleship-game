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
    /**
     * Set life
     */
    [types.MUTATION.SCORE_SET_LIFE] (state, life) {
      state.life = life
    },
  },
  actions: {
    /**
     * After each rocket
     */
    [types.ACTIONS.AFTER_ROCKET] (context, box) {
      // Update life
      if (context.rootState.me) {
        const position = context.rootState.me.position
        if (box.life && box.life.hasOwnProperty(position)) {
          context.commit(types.MUTATION.SCORE_SET_LIFE, box.life[position])
        }
      }
    },
  },
  getters: {},
  strict: process.env.NODE_ENV !== 'production',
}
