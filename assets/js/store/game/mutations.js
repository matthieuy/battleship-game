/**
 * Mutations for game
 */

import * as types from './types'

export default {
  /**
   * Set the current user id
   */
  [types.MUTATION.SET_USERID] (state, userId) {
    console.log('[STORE] Set userid', state.userId)
    state.userId = (userId !== 0) ? parseInt(userId) : null
  },
  /**
   * Set the current slug
   */
  [types.MUTATION.SET_SLUG] (state, slug) {
    console.log('[STORE] Set slug', slug)
    state.slug = slug
  },
  /**
   * Set status text
   */
  [types.MUTATION.SET_STATUS] (state, txt) {
    state.status = txt
  },
  /**
   * Load game
   */
  [types.MUTATION.LOAD] (state, obj) {
    console.log('[STORE] Load ', obj)

    // Current player
    state.me = obj.players.find((player) => player.me)

    // Create complete empty grid
    let grid = new Array(obj.size) // eslint-disable-line prefer-const
    for (let y = 0; y < obj.size; y++) {
      grid[y] = new Array(obj.size)
      for (let x = 0; x < obj.size; x++) {
        grid[y][x] = { x: x, y: y, img: 0 }
      }
    }

    // Put box in grid
    obj.grid.forEach((box) => {
      grid[box.y][box.x] = box
    })

    // Update state
    state.size = obj.size
    state.boxSize = obj.boxSize
    state.tour = obj.tour
    state.options = obj.options
    state.gameover = obj.finished
    state.players = obj.players
    state.grid = grid
  },
}
