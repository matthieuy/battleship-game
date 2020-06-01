<template>
  <div :title="trans('weapon_name')" :class="{disabled: !weapon.enabled}" class="bubble" @click="toggleModal()">
    <i class="gi gi-crossed-sabres" :class="{selected: weapon.select}"></i>
    <img id="weapon-bubble" src="img/null.png" width="25" height="25">
  </div>
</template>

<script>
import { mapState } from 'vuex'
import * as types from '@js/store/game/types'
import Favico from '@npm/favico.js/favico'
/* global Translator */

let BubbleWeapon = null // eslint-disable-line no-unused-vars

export default {
  data () {
    return {
      trans () {
        return Translator.trans(...arguments)
      },
    }
  },
  computed: {
    ...mapState([
      'weapon', // Weapon module
    ]),
  },
  methods: {
    toggleModal () {
      if (this.weapon.enabled) {
        this.$store.commit(types.MUTATION.WEAPON_MODAL)
      }
    },
  },
  watch: {
    // Update btn when score change
    'weapon.score' (score) {
      if (this.weapon.enabled) {
        BubbleWeapon.badge(score)
      }
    },
  },
  mounted () {
    console.log('[VUE] Mount BtnWeapon.vue')
    BubbleWeapon = new Favico({
      elementId: 'weapon-bubble',
      animation: 'slide',
    })
  },
}
</script>
