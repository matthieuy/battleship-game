<template>
    <span
        v-if="isCreator"
        @click="addAi"
        :data-tip="tip"
        :class="btnClass"
        class="button round small-12 large-2 opentip">
            <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
    </span>
</template>
<script>
    import { mapState } from 'vuex'
    import * as types from '@js/store/waiting/types'

    export default {
      data ()  {
        return {
          loading: false,
          disabled: true,
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
          this.disabled = (!this.isCreator || this.players.length >= this.game.max)
          return {
            disabled: this.disabled || this.loading || !this.loaded,
          }
        },
      },
      methods: {
        // Add a AI
        addAi () {
          if (this.disabled) {
            return false
          }

          // Loading
          this.loading = true
          this.$store.commit(types.MUTATION.SET_LOADED, false)

          // Request
          this.$store.dispatch(types.ACTION.ADD_AI)
        },
      },
      mounted() {
        console.log('[VUE] Mount BtnAI.vue')
      },
    }
</script>
