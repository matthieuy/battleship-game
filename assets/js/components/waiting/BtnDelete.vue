<template>
  <span
    v-if="isCreator"
    :data-tip="tip"
    :class="btnClass"
    class="button alert round small-12 large-2 opentip"
    @click="deleteGame"
  >
    <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
  </span>
</template>

<script>
import { mapState } from 'vuex'
import * as types from '@js/store/waiting/types'
// import Routing from '@js/Routing'
/* global Translator */

export default {
  data () {
    return {
      loading: false,
      name: Translator.trans('btn_delete', {}, 'js'),
      tip: `<strong>${Translator.trans('btn_delete', {}, 'js')} :</strong> ${Translator.trans('btn_delete_tip', {}, 'js')}`,
    }
  },
  computed: {
    ...mapState([
      'loaded',
      'isCreator',
    ]),
    // CSS class on button
    btnClass () {
      return {
        disabled: this.loading || !this.loaded,
      }
    },
  },
  methods: {
    deleteGame () {
      const confirmMsg = Translator.trans('btn_delete_confirm', {}, 'js')
      if (this.isCreator && confirm(confirmMsg)) {
        this.$store.dispatch(types.ACTION.DELETE_GAME)
      }
    },

  },
  mounted () {
    console.log('[VUE] Mount BtnDelete.vue')
  },
}
</script>
