<template>
  <div :class="{ 'has-error': error }">
    <label for="max-player" class="required">{{ label }} :</label>
    <input id="max-player" v-model="nb_player" type="range" required="required" class="small-12 max-player" min="2" max="12">
    <div class="clear"></div>
  </div>
</template>

<script>
/* global $ */
const regNum = new RegExp('^([0-9]*)$', 'g')

export default {
  props: {
    translation: { type: String, default: '%nb% of players' }, // need props translation (ex: %nb% of players)
  },
  data () {
    return {
      error: false,
      nb_player: 4,
    }
  },
  computed: {
    label () {
      return this.translation.replace('%nb%', this.error ? '' : this.nb_player)
    },
  },
  watch: {
    nb_player (value) {
      this.error = !value.toString().match(regNum) || !value.length
      if (!this.error) {
        $('.hidden-maxplayer').val(this.nb_player)
      }
    },
  },
  mounted () {
    console.debug('[VUE] Mount RangeMaxPlayer')
  },
}
</script>
