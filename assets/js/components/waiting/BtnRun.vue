<template>
  <span
    v-if="isCreator"
    :data-tip="tip"
    :class="{disabled: isDisabled}"
    class="button success round small-12 large-2 opentip"
    @click="run"
  >
    <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
  </span>
</template>

<script>
import { mapState } from 'vuex'
import Flash from '@js/Flash'
import * as types from '@js/store/waiting/types'
/* global Translator */

export default {
  data () {
    return {
      loading: false,
      name: Translator.trans('btn_run', {}, 'js'),
      tip: `<strong>${Translator.trans('btn_run', {}, 'js')} :</strong>${Translator.trans('btn_run_tip', {}, 'js')}`,
    }
  },
  computed: {
    ...mapState([
      'game',
      'players',
      'isCreator',
      'loaded',
    ]),
    isDisabled () {
      return this.players.length < 2 || this.players.length > this.game.maxPlayer || this.loading || !this.loaded
    },
  },
  methods: {
    run () {
      if (this.isDisabled) {
        return false
      }

      // Count team
      // let teamList = {} // eslint-disable-line prefer-const
      // this.players.forEach((player) => {
      //   teamList[player.team] = 1
      // })
      // const nbTeam = Object.size(teamList)
      //
      // // Check team
      // if (nbTeam <= 1) {
      //   return Flash.error('error_run_team')
      // }
      // for (let i = 1; i <= nbTeam; i++) {
      //   if (typeof teamList[i] === 'undefined') {
      //     return Flash.error(Translator.trans('error_run_emptyteam', { team: i }, 'js'))
      //   }
      // }

      // Human player
      const humanPlayers = this.players.filter((player) => !player.ai)
      if (!humanPlayers.length) {
        return Flash.error('error_run_ai')
      }

      // Confirm ?
      if (!confirm(Translator.trans('confirm_run'))) {
        return false
      }

      // Run
      this.loading = true
      this.$store.dispatch(types.ACTION.RUN).then(() => {
        this.loading = false
      })
    },
  },
  mounted () {
    console.log('[VUE] Mount BtnRun.vue')
  },
}
</script>
