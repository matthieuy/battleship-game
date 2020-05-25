<template>
    <span @click="join" :data-tip="tip" :class="btnClass" class="button round small-12 large-2 opentip">
        <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
    </span>
</template>
<script>
    import { mapState } from 'vuex'
    import * as types from '@js/store/waiting/types'

    export default {
      data () {
        return {
          loading: false,
          disabled: true,
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
          let actionName = (this.joined) ? 'leave' : 'join'
          return `<strong>${this.name} :</strong>${Translator.trans('btn_' + actionName + '_tip')}`
        },
        // CSS class
        btnClass () {
          this.disabled = !this.loaded || (!this.joined && this.players.length >= this.game.max)
          return {
            alert: this.joined,
            disabled: this.disabled || this.loading,
          }
        },
      },
      methods: {
        // Join/Leave the game
        join () {
          if (this.disabled) {
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
      },
      mounted() {
        console.log('[VUE] Mount BtnJoin.vue')
      }
    }
</script>
