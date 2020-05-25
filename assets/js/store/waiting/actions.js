/**
 * Actions for waiting store
 */

import axios from 'axios'
import * as types from './types'
import Routing from '@js/Routing'
import Flash from '@js/Flash'

export default {
  /**
   * Load game's infos
   */
  [types.ACTION.LOAD_INFO] (context, slug) {
    context.commit(types.MUTATION.SET_SLUG, slug)
    let url = Routing.generate('match.ajax.infos', {slug: slug})
    console.log('[STORE] Load infos', url)
    return axios.get(url).then((response) => {
      if (response.status === 200 && response.data.id) {
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
    let url = Routing.generate('match.ajax.edit.infos', { slug: context.state.slug })
    return ajaxPostCall(url, {
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
    let url = Routing.generate('match.ajax.edit.infos', { slug: context.state.slug })
    return ajaxPostCall(url, {
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
    let url = Routing.generate('match.ajax.options', { slug: context.state.slug })
    return ajaxPostCall(url, obj, 'Can\'t change max player')
  },

  /**
   * Change color
   */
  [types.ACTION.CHANGE_COLOR] (context, obj) {
    console.log('[STORE] Change color', obj)

    // Request
    let url = Routing.generate('match.ajax.color', { slug: context.state.slug })
    return ajaxPostCall(url, obj, 'Can\'t change color')
  },

  /**
   * Change team
   */
  [types.ACTION.CHANGE_TEAM] (context, obj) {
    console.log('[STORE] Change team', obj)

    // Request
    let url = Routing.generate('match.ajax.team', { slug: context.state.slug })
    return ajaxPostCall(url, obj, 'Can\'t change team')
  },

  /**
   * Join/Leave game
   */
  [types.ACTION.JOIN_LEAVE] (context, join) {
    console.log('[STORE] Join/Leave game', join)

    // Request
    let url = Routing.generate('match.ajax.join', { slug: context.state.slug })
    return ajaxPostCall(url, { join: join }, 'Can\'t join/leave game')
  },

  /**
   * Remove a player
   */
  [types.ACTION.REMOVE_PLAYER] (context, playerId) {
    console.log('[STORE] Remove a player', playerId)

    // Request
    let url = Routing.generate('match.ajax.join', { slug: context.state.slug })
    return ajaxPostCall(url, {
      join: false,
      playerId: playerId,
    }, 'Can\'t join/leave game')
  },

  /**
   * Add a AI
   */
  [types.ACTION.ADD_AI] (context) {
    console.log('[STORE] Add a AI')
    let url = Routing.generate('match.ajax.join', { slug: context.state.slug })
    return ajaxPostCall(url, {
      join: true,
      ai: true,
    }, 'Can\'t add AI')
  },

  /**
   * Update order of game
   */
  [types.ACTION.UPDATE_ORDER] (context, obj) {
    console.log('[STORE] Change order', obj)
    let url = Routing.generate('match.ajax.order', { slug: context.state.slug })
    return ajaxPostCall(url, obj, 'Can\'t change order')
  },
}


/**
 * Do a AJAX POST call
 * @private
 * @param url
 * @param params
 * @param errorMsg
 * @returns {Promise}
 */
function ajaxPostCall (url, params, errorMsg) {
  return axios.post(url, new URLSearchParams(params)).then((response) => {
    console.log(response.status, response.data, response.data.hasOwnProperty('error'))
    if (response.status === 200) {
      if (response.data.success) {
        return Promise.resolve(response.data)
      } else if (response.data.hasOwnProperty('error')) {
        errorMsg = response.data.error
      }

      return Promise.reject(new Error(errorMsg))
    }
    return Promise.reject(new Error(errorMsg))
  }).catch(() => {
    Flash.error(errorMsg)
  })
}
