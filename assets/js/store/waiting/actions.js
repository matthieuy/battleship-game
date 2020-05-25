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
