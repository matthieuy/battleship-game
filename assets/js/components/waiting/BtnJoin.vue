<template>
  <span :data-tip="tip" :class="btnClass" class="button round small-12 large-2 opentip" @click="join">
    <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
  </span>
</template>

<script>
import { mapState } from 'vuex'
import * as types from '@js/store/waiting/types'
/* global Translator */

export default {
  data () {
    return {
      loading: false,
    }
  },
  computed: {
    ...mapState([
      'loaded',
      'joined',
      'game',
      'players',
    ]),
    // Content of the button
    name () {
      return (this.joined) ? Translator.trans('btn_leave', {}, 'js') : Translator.trans('btn_join', {}, 'js')
    },
    // Tooltip content
    tip () {
      const actionName = (this.joined) ? 'leave' : 'join'
      return `<strong>${this.name} :</strong>${Translator.trans('btn_' + actionName + '_tip')}`
    },
    // CSS class
    btnClass () {
      return {
        alert: this.joined,
        disabled: this.isDisabled() || this.loading || (!this.joined && this.players.length >= this.game.maxPlayer),
      }
    },
  },
  methods: {
    /**
     * Join/Leave the game
     */
    join () {
      if (this.isDisabled()) {
        return false
      }

      // Loading
      this.loading = true
      this.$store.commit(types.MUTATION.SET_LOADED, true)

      // Request
      this.$store.dispatch(types.ACTION.JOIN_LEAVE, !this.joined).then((obj) => {
        this.loading = false
      })
    },
    /**
     * Is button is disabled ?
     * @returns {boolean}
     */
    isDisabled () {
      return !this.loaded || (!this.joined && this.players.length >= this.game.max)
    },
  },
  mounted () {
    console.log('[VUE] Mount BtnJoin.vue')
  },
}
</script>
