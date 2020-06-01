<template>
  <div :title="name" class="bubble">
    <i class="gi gi-heart-organ"></i>
    <img id="life-bubble" src="img/null.png" width="25" height="25" />
  </div>
</template>

<script>
import { mapState } from 'vuex'
import Favico from '@npm/favico.js/favico'
/* global Translator */

let BubbleLife = null // eslint-disable-line no-unused-vars

export default {
  data () {
    return {
      name: Translator.trans('Score', {}, 'js'),
    }
  },
  computed: {
    ...mapState([
      'score', // Score module
    ]),
  },
  watch: {
    // Update bubble when life change
    'score.life' (life) {
      const color = (life < 10) ? '#FF0000' : (life > 20) ? '#008200' : '#B7B700'
      BubbleLife.badge(life, { bgColor: color })
    },
  },
  mounted () {
    console.log('[VUE] Mount BtnScore.vue')
    BubbleLife = new Favico({
      elementId: 'life-bubble',
      animation: 'slide',
    })
  },
}
</script>
