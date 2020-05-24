/**
 * Actions for waiting store
 */

import axios from 'axios'
import * as types from './types'
import Routing from '@js/Routing'

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

    // Prepare request
    let url = Routing.generate('match.ajax.edit.infos', { slug: context.state.slug })
    const params = new URLSearchParams({
      options: 'size',
      value: size,
    })

    // Post request
    return axios.post(url, params).then((response) => {
      if (response.status === 200 && response.data.success) {
        return Promise.resolve(response.data.value)
      }
      return Promise.reject(new Error('Can\'t change size'))
    }).catch(() => {
      return Promise.reject(new Error('Can\'t change size'))
    })
  },

  /**
   * Change max player
   */
  [types.ACTION.CHANGE_MAXPLAYER] (context, maxPlayer) {
    console.log('[STORE] Change maxPlayer', maxPlayer)

    // Prepare request
    let url = Routing.generate('match.ajax.edit.infos', { slug: context.state.slug })
    const params = new URLSearchParams({
      options: 'maxplayers',
      value: maxPlayer,
    })

    // Post request
    return axios.post(url, params).then((response) => {
      if (response.status === 200 && response.data.success) {
        return Promise.resolve(response.data.value)
      }
      return Promise.reject(new Error('Can\'t change max player'))
    }).catch(() => {
      return Promise.reject(new Error('Can\'t change max player'))
    })
  },
}
