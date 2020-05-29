<template>
  <span
    v-if="isCreator"
    :data-tip="tip"
    :class="btnClass"
    class="button round small-12 large-2 opentip"
    @click="addAi"
  >
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
      name: Translator.trans('btn_ai', {}, 'js'),
      tip: `<strong>${Translator.trans('btn_ai')} :</strong> ${Translator.trans('btn_ai_tip')}`,
    }
  },
  computed: {
    ...mapState([
      'loaded',
      'game',
      'players',
      'isCreator',
    ]),
    // CSS class on button
    btnClass () {
      return {
        disabled: this.isDisabled() || this.loading || !this.loaded || (this.players.length >= this.game.maxPlayer),
      }
    },
  },
  methods: {
    /**
     * Add a AI
     */
    addAi () {
      if (this.isDisabled()) {
        return false
      }

      // Loading
      this.loading = true

      // Request
      this.$store.dispatch(types.ACTION.ADD_AI).then(() => {
        this.loading = false
      })
    },
    /**
     * Check if btn is disabled
     * @returns {boolean}
     */
    isDisabled () {
      return (!this.isCreator || this.players.length >= this.game.max)
    },
  },
  mounted () {
    console.log('[VUE] Mount BtnAI.vue')
  },
}
</script>
