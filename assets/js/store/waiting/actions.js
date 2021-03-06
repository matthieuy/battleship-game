/**
 * Actions for waiting store
 */

import ajax from '@js/libs/ajax'
import * as types from './types'
import Routing from '@js/Routing'

export default {
  /**
   * Load game's infos
   */
  [types.ACTION.LOAD_INFO] (context, slug) {
    context.commit(types.MUTATION.SET_SLUG, slug)
    const url = Routing.generate('match.ajax.infos', { slug: slug })
    console.log('[STORE] Load infos', url)
    return ajax.get(url).then((response) => {
      if (response.status === 200 && response.data.id) {
        if (response.data.hasOwnProperty('players')) {
          context.commit(types.MUTATION.SET_PLAYERS, response.data.players)
        }
        context.commit(types.MUTATION.SET_GAMEINFO, response.data)
        return Promise.resolve(response.data)
      }
      return Promise.reject(new Error('Can\'t load game'))
    }).catch(() => {
      return Promise.reject(new Error('Can\'t load game'))
    })
  },

  /**
   * Change grid size
   */
  [types.ACTION.CHANGE_SIZE] (context, size) {
    console.log('[STORE] Change size', size)

    // Request
    const url = Routing.generate('match.ajax.edit.infos', { slug: context.state.slug })
    return ajax.postCall(url, {
      options: 'size',
      value: size,
    }, 'Can\'t change size !')
  },

  /**
   * Change max player
   */
  [types.ACTION.CHANGE_MAXPLAYER] (context, maxPlayer) {
    console.log('[STORE] Change maxPlayer', maxPlayer)

    // Request
    const url = Routing.generate('match.ajax.edit.infos', { slug: context.state.slug })
    return ajax.postCall(url, {
      options: 'maxplayers',
      value: maxPlayer,
    }, 'Can\'t change max player')
  },

  /**
   * Change option
   */
  [types.ACTION.CHANGE_OPTIONS] (context, obj) {
    console.log('[STORE] Change options', obj)

    // Request
    const url = Routing.generate('match.ajax.options', { slug: context.state.slug })
    return ajax.postCall(url, obj, 'Can\'t change max player')
  },

  /**
   * Change color
   */
  [types.ACTION.CHANGE_COLOR] (context, obj) {
    console.log('[STORE] Change color', obj)

    // Request
    const url = Routing.generate('match.ajax.color', { slug: context.state.slug })
    return ajax.postCall(url, obj, 'Can\'t change color')
  },

  /**
   * Change team
   */
  [types.ACTION.CHANGE_TEAM] (context, obj) {
    console.log('[STORE] Change team', obj)

    // Request
    const url = Routing.generate('match.ajax.team', { slug: context.state.slug })
    return ajax.postCall(url, obj, 'Can\'t change team')
  },

  /**
   * Join/Leave game
   */
  [types.ACTION.JOIN_LEAVE] (context, join) {
    console.log('[STORE] Join/Leave game', join)

    // Request
    const url = Routing.generate('match.ajax.join', { slug: context.state.slug })
    return ajax.postCall(url, { join: join }, 'Can\'t join/leave game')
  },

  /**
   * Remove a player
   */
  [types.ACTION.REMOVE_PLAYER] (context, playerId) {
    console.log('[STORE] Remove a player', playerId)

    // Request
    const url = Routing.generate('match.ajax.join', { slug: context.state.slug })
    return ajax.postCall(url, {
      join: false,
      playerId: playerId,
    }, 'Can\'t join/leave game')
  },

  /**
   * Add a AI
   */
  [types.ACTION.ADD_AI] (context) {
    console.log('[STORE] Add a AI')
    const url = Routing.generate('match.ajax.join', { slug: context.state.slug })
    return ajax.postCall(url, {
      join: true,
      ai: true,
    }, 'Can\'t add AI')
  },

  /**
   * Update order of game
   */
  [types.ACTION.UPDATE_ORDER] (context, obj) {
    console.log('[STORE] Change order', obj)
    const url = Routing.generate('match.ajax.order', { slug: context.state.slug })
    return ajax.postCall(url, obj, 'Can\'t change order')
  },

  /**
   * Delete the game
   */
  [types.ACTION.DELETE_GAME] (context) {
    console.log('[STORE] Delete game')
    const url = Routing.generate('match.delete', { slug: context.state.slug })
    return ajax.postCall(url, {}, 'Can\'t delete').then((obj) => {
      if (obj.success) {
        window.location.replace(Routing.generate('homepage'))
      }
    })
  },

  [types.ACTION.RUN] (context) {
    console.log('[STORE] Run')
    const url = Routing.generate('match.run', { slug: context.state.slug })
    return ajax.postCall(url, {}, 'Can\'t run')
  },
}
