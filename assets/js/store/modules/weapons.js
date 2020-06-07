/**
 * Weapon module vuex store
 */
import ajax from '@js/libs/ajax'
import Routing from '@js/Routing'
import * as types from '@js/store/game/types'

export default {
  state: {
    modal: false,
    loaded: false,
    enabled: false,
    list: [],
    score: 0,
    select: null, // Selected weapon
    rotate: 0,
  },
  mutations: {
    /**
     * On first load
     */
    [types.MUTATION.LOAD] (state, obj) {
      const me = obj.players.find((player) => player.me)
      if (me) {
        state.score = me.score
        state.enabled = obj.options.weapon
      }

      // Disable weapon
      if (obj.finished) {
        state.enabled = false
      }
    },
    /**
     * Set weapon score
     */
    [types.MUTATION.WEAPON_SET_SCORE] (state, score) {
      state.score = score
    },
    /**
     * Toggle modal
     */
    [types.MUTATION.WEAPON_MODAL] (state, status) {
      state.modal = (typeof status !== 'undefined') ? status : !state.modal
    },
    /**
     * Select weapon
     */
    [types.MUTATION.WEAPON_SELECT] (state, weapon) {
      state.select = (weapon) ? weapon.name : null
    },
    /**
     * Set weapons list
     */
    [types.MUTATION.WEAPON_SETLIST] (state, weapons) {
      state.list = Object.values(weapons).sort((w1, w2) => {
        return w1.price - w2.price
      })
      state.loaded = (state.list.length > 0)
    },
    /**
     * Rotate weapon matrix
     */
    [types.MUTATION.WEAPON_ROTATE] (state) {
      let rotate = state.rotate + 1
      if (rotate > 3) {
        rotate = rotate % 4
      }
      state.rotate = rotate

      state.list.forEach((weapon, iW) => {
        // Can't rotate this weapon => next
        if (!weapon.rotate) {
          return true
        }

        // Create new grid
        let grid = weapon.grid // eslint-disable-line prefer-const
        let newGrid = [] // eslint-disable-line prefer-const
        newGrid.length = grid[0].length
        for (let i = 0; i < newGrid.length; i++) {
          newGrid[i] = []
          newGrid[i].length = grid.length
        }

        // Rotate
        for (let i = 0; i < grid.length; i++) {
          for (let j = 0; j < grid[i].length; j++) {
            newGrid[j][grid.length - i - 1] = grid[i][j]
          }
        }

        // Apply
        weapon.grid = newGrid
      })
    },
  },
  actions: {
    /**
     * Load weapons list
     */
    [types.ACTIONS.WEAPON_LOAD] (context) {
      const url = Routing.generate('weapons.list')
      console.log('[STORE] Load weapons')
      return ajax.get(url).then((response) => {
        if (response.status === 200 && response.data) {
          return Promise.resolve(response.data)
        }
        return Promise.resolve([])
      })
    },
    /**
     * Before shoot : add weapon infos
     */
    [types.ACTIONS.BEFORE_SHOOT] (context, obj) {
      return new Promise((resolve, reject) => {
        if (context.state.select) {
          Object.assign(obj, {
            weapon: context.state.select,
            rotate: context.state.rotate,
          })
          context.commit(types.MUTATION.WEAPON_SELECT)
        }
        resolve(obj)
      })
    },
    /**
     * After each rocket
     */
    [types.ACTIONS.AFTER_ROCKET] (context, box) {
      // Update points
      if (context.rootState.me) {
        const position = context.rootState.me.position
        if (box.score && box.score.hasOwnProperty(position)) {
          context.commit(types.MUTATION.WEAPON_SET_SCORE, box.score[position])
        }
      }
    },
  },
  getters: {
    // Get weapon
    getWeapon: (state) => (name) => {
      const weapon = state.list.filter((weapon) => weapon.name === name)

      return (weapon.length) ? weapon[0] : null
    },
  },
  strict: process.env.NODE_ENV !== 'production',
}
