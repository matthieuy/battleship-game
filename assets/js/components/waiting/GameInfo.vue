<template>
  <table class="small-12 extendable">
    <thead class="cursor">
      <tr>
        <th colspan="2">
          {{ trans('Informations', {}, 'js') }}
          <div class="fa caret"></div>
        </th>
      </tr>
    </thead>
    <tbody v-show="loaded">
      <tr>
        <td>{{ trans('Name of the game', {}, 'js') }}</td>
        <td>{{ game.name }}</td>
      </tr>
      <tr>
        <td>{{ trans('gridsize', {}, 'js') }}</td>
        <td>
          <span v-if="!isCreator">{{ game.size }} x {{ game.size }}</span>
          <select v-if="isCreator" v-model="size">
            <option value="15">15 x 15 ({{ trans('nb_players', {nb: 2}, 'js') }})</option>
            <option value="20">20 x 20 ({{ trans('nb_players', {nb: 3}, 'js') }})</option>
            <option value="25">25 x 25 ({{ trans('nb_players', {nb: '4/5'}, 'js') }})</option>
            <option value="30">30 x 30 ({{ trans('nb_players', {nb: '6/7'}, 'js') }})</option>
            <option value="35">35 x 35 ({{ trans('nb_players', {nb: 8}, 'js') }})</option>
            <option value="40">40 x 40 ({{ trans('nb_players', {nb: 9}, 'js') }})</option>
            <option value="45">45 x 45 ({{ trans('nb_players', {nb: 10}, 'js') }})</option>
            <option value="50">50 x 50 ({{ trans('XXL', {}, 'js') }})</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>{{ trans('create_date', {}, 'js') }}</td>
        <td>{{ date }}</td>
      </tr>
      <tr>
        <td>{{ trans('Creator', {}, 'js') }}</td>
        <td>{{ creatorName }}</td>
      </tr>
      <tr>
        <td>{{ trans('Players', {}, 'js') }}</td>
        <td>
          {{ players.length }} /
          <span v-if="isCreator"><input v-model="maxPlayer" type="number" min="2" max="12" /></span>
          <span v-if="!isCreator">{{ game.maxPlayer }}</span>
        </td>
      </tr>
      <tr>
        <td>{{ trans('Status', {}, 'js') }}</td>
        <td>{{ status }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import { mapState } from 'vuex'
import * as types from '@js/store/waiting/types'
/* global Translator */

export default {
  props: {
    date: { type: String, default: 'now' }, // Create date
  },
  data () {
    return {
      trans () {
        return Translator.trans(...arguments)
      },
    }
  },
  computed: {
    ...mapState([
      'game',
      'slug',
      'players',
      'isCreator',
      'creatorName',
      'loaded',
    ]),
    // Status
    status () {
      return (this.players.length < this.game.maxPlayer) ? Translator.trans('waiting_player', {}, 'js') : Translator.trans('game_full', {}, 'js')
    },
    // Get or set size
    size: {
      get () {
        return this.game.size
      },
      set (size) {
        if (this.isCreator) {
          size = Math.min(50, Math.max(15, size))
          this.$store.dispatch(types.ACTION.CHANGE_SIZE, size)
        }
      },
    },
    // Get or set max players
    maxPlayer: {
      get () {
        return this.game.maxPlayer
      },
      set (maxPlayer) {
        if (this.isCreator) {
          maxPlayer = Math.min(12, Math.max(2, maxPlayer))
          this.$store.dispatch(types.ACTION.CHANGE_MAXPLAYER, maxPlayer)
        }
      },
    },
  },
  mounted () {
    console.log('[VUE] Mount GameInfo.vue')
    if (!this.loaded) {
      const slug = document.getElementById('slug').value
      this.$store.dispatch(types.ACTION.LOAD_INFO, slug)
    }
  },
}
</script>
