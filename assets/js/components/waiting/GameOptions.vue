<template>
  <table id="table-options" class="small-12 extendable">
    <thead>
      <tr>
        <th colspan="2">
          {{ trans('Options', {}, 'js') }}
          <div class="fa caret"></div>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          {{ trans('penalty_name', {}, 'js') }}
          <span class="fa opentip" :data-tip="tips('penalty')"></span> :
        </td>
        <td>
          <span v-show="penalty" :class="{cursor: isCreator}" style="color: green;" @click="changeOption('penalty', 0)">
            <i class="fa" :class="{'fa-check-square-o': !loading.penalty, 'fa-spin fa-spinner': loading.penalty}"></i> {{ trans('enabled', {}, 'js') }}
          </span>
          <span v-show="!penalty" :class="{cursor: isCreator}" style="color: red;" @click="changeOption('penalty', 20)">
            <i class="fa" :class="{'fa-square-o': !loading.penalty, 'fa-spin fa-spinner': loading.penalty}"></i> {{ trans('disabled', {}, 'js') }}
          </span>
          <span v-show="penalty">
            <span v-if="isCreator">
              <input :value="penalty" type="number" min="1" max="72" /> hour(s)
            </span>
            <span v-else>
              <strong>{{ penalty }}</strong> hour(s)
            </span>
          </span>
        </td>
      </tr>
      <tr>
        <td>
          {{ trans('weapon_name', {}, 'js') }}
          <span class="fa opentip" :data-tip="tips('weapon')"></span> :
        </td>
        <td>
          <span v-show="isWeapon" :class="{cursor: isCreator}" style="color: green;" @click="changeOption('weapon', false)">
            <i class="fa" :class="{'fa-check-square-o': !loading.weapon, 'fa-spin fa-spinner': loading.weapon}"></i> {{ trans('enabled', {}, 'js') }}
          </span>
          <span v-show="!isWeapon" :class="{cursor: isCreator}" style="color: red;" @click="changeOption('weapon', true)">
            <i class="fa" :class="{'fa-square-o': !loading.weapon, 'fa-spin fa-spinner': loading.weapon}"></i> {{ trans('disabled', {}, 'js') }}
          </span>
        </td>
      </tr>
      <tr>
        <td>
          {{ trans('bonus_name', {}, 'js') }}
          <span class="fa opentip" :data-tip="tips('bonus')"></span> :
        </td>
        <td>
          <span v-show="isBonus" :class="{cursor: isCreator}" style="color: green;" @click="changeOption('bonus', false)">
            <i class="fa" :class="{'fa-check-square-o': !loading.bonus, 'fa-spin fa-spinner': loading.bonus}"></i> {{ trans('enabled', {}, 'js') }}
          </span>
          <span v-show="!isBonus" :class="{cursor: isCreator}" style="color: red;" @click="changeOption('bonus', true)">
            <i class="fa" :class="{'fa-square-o': !loading.bonus, 'fa-spin fa-spinner': loading.bonus}"></i> {{ trans('disabled', {}, 'js') }}
          </span>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import { mapState } from 'vuex'
import * as types from '@js/store/waiting/types'
/* global Translator */

export default {
  data () {
    return {
      loading: {
        penalty: false,
        weapon: false,
        bonus: false,
      },
      trans () {
        return Translator.trans(...arguments)
      },
      tips (type) {
        return `<strong>${Translator.trans(type + '_name', {}, 'js')} :</strong>${Translator.trans(type + '_desc', {}, 'js')}`
      },
    }
  },
  computed: {
    ...mapState([
      'game',
      'isCreator',
    ]),
    isBonus () {
      return (this.game.options) ? this.game.options.bonus : false
    },
    isWeapon () {
      return (this.game.options) ? this.game.options.weapon : false
    },
    penalty () {
      return (this.game.options) ? this.game.options.penalty : 0
    },
  },
  methods: {
    changeOption (name, value) {
      if (!this.isCreator) {
        return false
      }
      this.loading[name] = true
      this.$store.commit(types.MUTATION.SET_LOADED, false)

      this.$store.dispatch(types.ACTION.CHANGE_OPTIONS, {
        option: name,
        value: value,
      }).then(() => {
        this.loading[name] = false
      }).catch(() => {
        this.loading[name] = false
      })
    },
  },
  mounted () {
    console.log('[VUE] Mount GameOptions.vue')
  },
}
</script>
