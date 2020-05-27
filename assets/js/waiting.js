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
import PlayersList from './components/waiting/PlayersList'
import BtnJoin from './components/waiting/BtnJoin'
import BtnAi from './components/waiting/BtnAi'
import BtnDelete from './components/waiting/BtnDelete'

// Theme
import '../css/_table.less'
import '../css/_tooltip.less'
import '../css/_modal.less'
import '../css/waiting.less'

// JS Lib
require('./libs/table')
require('./libs/tooltips')
require('@npm/jquery-ui/ui/core')
require('@npm/jquery-ui/ui/widget')
require('@npm/jquery-ui/ui/widgets/mouse')
require('@npm/jquery-ui/ui/widgets/sortable')

// Store init
store.commit(types.MUTATION.SET_USERID, document.getElementById('user-id').value)

// App vue
new Vue({ // eslint-disable-line no-new
  el: '#vue',
  store,
  components: {
    GameInfo,
    GameOptions,
    PlayersList,
    BtnJoin,
    BtnAi,
    BtnDelete,
  },
})
