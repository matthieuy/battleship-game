/**
 * Waiting page before
 */

// Imports
import Vue from 'vue'
import store from './store/WaitingStore'
import * as types from './store/waiting/types'

// Components
import GameInfo from './components/waiting/GameInfo'
import GameOptions from './components/waiting/GameOptions'

// Theme
import '../css/_table.less'
import '../css/_tooltip.less'
import '../css/_modal.less'
import '../css/waiting.less'

// JS Lib
require('./libs/table')

// Store init
store.commit(types.MUTATION.SET_USERID, document.getElementById('user-id').value)

// App vue
new Vue({
  el: '#vue',
  store,
  components: {
    GameInfo,
    GameOptions,
  },
})
