/**
 * Actions for game
 */

import ajax from '@js/libs/ajax'
import Routing from '@js/Routing'
import * as types from './types'

export default {
  /**
   * Load the game
   */
  [types.ACTIONS.LOAD_GAME] (context) {
    console.log('[STORE] Load game')
    const url = Routing.generate('match.load', { slug: context.state.slug })

    return ajax.get(url).then((response) => {
      if (response.status === 200) {
        return Promise.resolve(response.data)
      }
      return Promise.reject(new Error('Can\'t load game'))
    }).catch(() => {
      return Promise.reject(new Error('Can\'t load game'))
    })
  },
  /**
   * Shoot
   */
  [types.ACTIONS.SHOOT] (context, obj) {
    console.log('[STORE] Shoot', obj)
    const url = Routing.generate('match.shoot', { slug: context.state.slug })
    return ajax.postCall(url, obj, 'Can\'t shoot').then((obj) => {

    })
  },
}
