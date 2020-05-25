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
                <span v-show="penalty" @click="changeOption('penalty', 0)" :class="{cursor: isCreator}" style="color: green;">
                    <i class="fa" :class="{'fa-check-square-o': !loading.penalty, 'fa-spin fa-spinner': loading.penalty}"></i> {{ trans('enabled', {}, 'js') }}
                </span>
                <span v-show="!penalty" @click="changeOption('penalty', 20)" :class="{cursor: isCreator}" style="color: red;">
                    <i class="fa" :class="{'fa-square-o': !loading.penalty, 'fa-spin fa-spinner': loading.penalty}"></i> {{ trans('disabled', {}, 'js') }}
                </span>
                <span v-show="penalty">
                    <span v-if="isCreator">
                        <input @change="changeOption('penalty', $event.target.value)" :value="penalty" type="number" min="1" max="72" /> hour(s)
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
                <span v-show="isWeapon" @click="changeOption('weapon', false)" :class="{cursor: isCreator}" style="color: green;">
                    <i class="fa" :class="{'fa-check-square-o': !loading.weapon, 'fa-spin fa-spinner': loading.weapon}"></i> {{ trans('enabled', {}, 'js') }}
                </span>
                <span v-show="!isWeapon" @click="changeOption('weapon', true)" :class="{cursor: isCreator}" style="color: red;">
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
                <span v-show="isBonus" @click="changeOption('bonus', false)" :class="{cursor: isCreator}" style="color: green;">
                    <i class="fa" :class="{'fa-check-square-o': !loading.bonus, 'fa-spin fa-spinner': loading.bonus}"></i> {{ trans('enabled', {}, 'js') }}
                </span>
                <span v-show="!isBonus" @click="changeOption('bonus', true)" :class="{cursor: isCreator}" style="color: red;">
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
          }
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
          this.$store.commit(types.MUTATION.SET_LOADED, true)

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
      }
    }
</script>
