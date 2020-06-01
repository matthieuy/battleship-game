/**
 * Running game entrypoint
 */

// Imports
import Vue from 'vue'
import Mercure from '@js/Mercure'
import store from './store/GameStore'
import * as types from './store/game/types'

// Components
import Grid from '@js/components/Grid'
import BtnScore from '@js/components/BtnScore'
import BtnWeapon from '@js/components/BtnWeapon'
import ModalWeapon from '@js/components/ModalWeapon'

// Theme
import '@css/_tooltip.less'
import '@css/_modal.less'
import '@css/game.less'

// JS lib
import $ from 'jquery'
require('./libs/tooltips')

// Store init
store.commit(types.MUTATION.SET_USERID, document.getElementById('user-id').value)

// App vue
/* eslint-disable no-new */
new Vue({
  el: '#vue',
  store,
  components: {
    Grid,
    ModalWeapon,
  },
})

// Top button
/* eslint-disable no-new */
new Vue({
  el: '#btn-top',
  store,
  components: {
    BtnScore,
    BtnWeapon,
  },
})

// Document ready
$(() => {
  // Load game
  store.commit(types.MUTATION.SET_SLUG, document.getElementById('slug').value)
  store.dispatch(types.ACTIONS.LOAD_GAME).then((game) => {
    store.commit(types.MUTATION.LOAD, game)
  })

  // Mercure
  Mercure.connect()
})
