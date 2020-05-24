/**
 * Mutations for waiting vuex store
 */

import * as types from './types'

export default {
  // Set the current user id
  [types.MUTATION.SET_USERID] (state, userId) {
    state.userId = (userId !== 0) ? parseInt(userId) : null
    console.log('[STORE] Set userid', state.userId)
  },
  // Set if the game is loaded
  [types.MUTATION.SET_LOADED] (state, loaded) {
    state.loaded = loaded
  },
  // Set game's slug
  [types.MUTATION.SET_SLUG] (state, slug) {
    state.slug = slug
  },
  // Set game info after first load
  [types.MUTATION.SET_GAMEINFO] (state, infos) {
    console.log('[STORE] Set game infos', infos)

    // Players
    if (infos.hasOwnProperty('players')) {
      state.me = null
      state.players = infos.players
      infos.players.forEach((player) => {
        if (player.userId === state.userId) {
          state.me = player
          return false
        }
      })
      state.joined = (state.me !== null)
      delete infos.players
    }

    // Creator
    if (infos.hasOwnProperty('creator')) {
      state.isCreator = (infos.creator.id === state.userId)
      state.creatorName = infos.creator.username
      delete infos.creator
    }

    // Other infos
    state.game = infos
    state.loaded = true
  },
}
