<template>
    <table id="playerlist" class="small-12 extendable">
        <thead>
        <tr>
            <th colspan="4">
                {{ trans('Players', {}, 'js') }}
                <div class="fa caret"></div>
            </th>
        </tr>
        <tr>
            <th class="center" width="50">{{ trans('Color', {}, 'js') }}</th>
            <th width="50"></th>
            <th class="center">{{ trans('Player', {}, 'js') }}</th>
            <th class="center" width="100">{{ trans('Team', {}, 'js') }}</th>
        </tr>
        </thead>
        <tbody id="sortable-players">
            <tr v-for="player in players" class="player-line" :data-id="player.id" :key="player.id">
                <td class="opentip"
                    :class="{move: isSortable}"
                    :data-tip="(player.ai) ? trans('AI', {}, 'js') : trans('Player', {}, 'js')"
                    :style="txtColor(player.color)"
                >
                    <i class="fa" :class="[player.ai ? 'fa-gamepad' : 'fa-desktop']"></i>
                </td>
                <td>
                    <!-- @todo AVATAR !-->
                </td>
                <td>
                    <!-- @todo LINK TO PROFIL -->
                    <span class="name">{{ player.name }}</span>
                    <div class="color-div" v-show="isCreator || player.userId === userId">
                        <input type="color" class="color" :title="trans('Change color', {}, 'js')" :value="'#' + player.color" @change="changeColor($event, player)">
                    </div>
                </td>
                <td>
                    <select v-show="isCreator || player.userId === userId" :value="player.team" @change="changeTeam($event, player)">
                        <option v-for="n in 12">{{ n }}</option>
                    </select>
                    <span
                        class="delete"
                        :title="trans('Remove the player', {}, 'js')"
                        v-show="isCreator || player.userId === userId"
                        @click="removePlayer($event, player)">&times;</span>
                </td>
            </tr>
            <tr v-show="!loaded">
                <td colspan="4" class="center">{{ trans('loading', {}, 'js') }}</td>
            </tr>
            <tr v-show="players.length === 0 && loaded">
                <td colspan="4" class="center">{{ trans('no_player', {}, 'js') }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
  import {mapState} from 'vuex'
  import * as types from '@js/store/waiting/types'

  export default {
    data () {
      return {
        trans() {
          return Translator.trans(...arguments)
        },
      }
    },
    computed: {
      ...mapState([
        'players',
        'loaded',
        'isCreator',
        'userId',
      ]),
      isSortable () {
        return this.isCreator && this.players.length > 1
      },
    },
    methods: {
      // Calcul text color for player line
      txtColor(playerColor) {
        let rgb = hexToRgb(playerColor)
        let textColor = 0.213 * rgb[0] + 0.715 * rgb[1] + 0.072 * rgb[2]
        return {
          backgroundColor: '#' + playerColor,
          color: (textColor < 127.5) ? '#FFF' : '#000',
        }
      },
      // Change player color
      changeColor(e, player) {
        this.$store.commit(types.MUTATION.SET_LOADED, false)
        this.$store.dispatch(types.ACTION.CHANGE_COLOR, {
          playerId: player.id,
          color: e.target.value,
        })
      },
      // Change team
      changeTeam(e, player) {
        this.$store.commit(types.MUTATION.SET_LOADED, false)
        this.$store.dispatch(types.ACTION.CHANGE_TEAM, {
          playerId: player.id,
          team: e.target.value
        })
      },
      removePlayer (e, player) {
        e.target.innerHTML = '<i class="fa fa-spin fa-spinner"></i>'
        this.$store.commit(types.MUTATION.SET_LOADED, false)
        this.$store.dispatch(types.ACTION.REMOVE_PLAYER, player.id)
      },
    },
    watch: {
      isSortable (value) {
        if (value) {
          $('#playerlist tbody').sortable('enable')
        } else {
          $('#playerlist tbody').sortable('disable')
        }
      },
    },
    mounted() {
      console.log('[VUE] Mount PlayersList.vue')

      // Playerlist sortable
      $('#playerlist tbody').sortable({
        axis: 'y',
        forceHelperSize: true,
        forcePlaceholderSize: true,
        placeholder: 'ui-placeholder',
        scroll: false,
        cursor: 'move',
        helper: function (e, ui) {
          ui.children().each(function () {
            $(this).width($(this).width())
          })
          return ui
        },
        handle: 'td.move',
        start: function (e, ui) {
          $('.ui-placeholder').height($(ui.item).height())
        },
        update: function (e, ui) {
          return ui
        },
      })
    },
  }


  /**
   * Convert hexa color to rgb
   * @param hex
   * @returns {Array}
   */
  function hexToRgb (hex) {
    let bigint = parseInt(hex, 16)
    let r = (bigint >> 16) & 255
    let g = (bigint >> 8) & 255
    let b = bigint & 255

    return [r, g, b]
  }
</script>
