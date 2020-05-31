/**
 * Actions for game
 */

import axios from 'axios'
import Routing from '@js/Routing'
import * as types from './types'

export default {
  [types.ACTIONS.LOAD_GAME] (context) {
    console.log('[STORE] Load game')
    const url = Routing.generate('match.load', { slug: context.state.slug })

    return axios.get(url).then((response) => {
      if (response.status === 200) {
        return Promise.resolve(response.data)
      }
      return Promise.reject(new Error('Can\'t load game'))
    }).catch(() => {
      return Promise.reject(new Error('Can\'t load game'))
    })
  },
}
