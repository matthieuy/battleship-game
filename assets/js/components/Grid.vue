<template>
  <div>
    <div id="status">{{ status }}</div>
    <div id="grid-container">
      <div id="grid" class="grid" unselectable="on" contextmenu="grid-menu">
        <div v-for="row in grid" class="grid-line">
          <span
            v-for="box in row"
            :id="'g_' + box.y + '_' + box.x"
            :class="cssBox(box)"
            :data-tip="tooltip(box)"
            class="box"
            @click="click(box)"
          >
            <span v-if="box.explose" class="explose hit animated"></span>
          </span>
        </div>
      </div>
    </div>
    <div v-for="player in players" :id="'rocket'+player.position" class="rocket">&nbsp;</div>
  </div>
</template>

<script>
// Imports
import async from 'async'
import velocity from 'velocity-animate'
import { mapState, mapGetters } from 'vuex'
import * as types from '@js/store/game/types'
import Mercure from '@js/Mercure'
/* global $, Translator */

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
      'grid',
      'players',
      'status',
      'me',
      'tour',
      'gameover',
      'boxSize',
    ]),
    ...mapGetters([
      'playerById',
    ]),
  },
  methods: {
    // Receive data from mercure
    receive (obj) {
      console.log('[GRID] Receive', obj)
      const self = this
      if (obj.img) {
        async.mapSeries(
          obj.img,
          (img, next) => { // Animate for all img
            // Box target
            const $box = $('#g_' + img.y + '_' + img.x)
            $box.getTop = function () {
              return this.offset().top + document.querySelector('#container').scrollTop
            }
            $box.getLeft = function () {
              return this.offset().left + document.querySelector('#container').scrollLeft
            }

            // Status
            const shooter = self.playerById(img.shoot)
            if (typeof img.player !== 'undefined' && img.player === img.shoot) { // Penalty
              self.$store.commit(types.MUTATION.SET_STATUS, Translator.trans('penalty_on', { name: shooter.name }, 'js'))
            } else {
              self.$store.commit(types.MUTATION.SET_STATUS, Translator.trans('shoot_of', { name: shooter.name }, 'js'))
            }

            // Rocket
            velocity(document.getElementById('rocket' + img.shoot), {
              top: $box.getTop() - (self.boxSize / 2),
              left: $box.getLeft() + (self.boxSize / 4),
            }, {
              duration: 5 * ($box.getTop() + 20),
              easing: 'linear',
              begin (rocket) {
                // Start position of the rocket
                $(rocket).css({
                  top: -20,
                  left: $box.getLeft() + (self.boxSize / 4),
                })
              },
              complete (rocket) {
                $(rocket).css('top', '-40px')

                // Update grid
                $box.addClass('animated')
                self.$store.commit(types.MUTATION.AFTER_ROCKET, img)
                self.$store.dispatch(types.ACTIONS.AFTER_ROCKET, img)

                // Next img
                next()
              },
            })
          },
          () => { // End of animate
            self.$store.commit(types.MUTATION.AFTER_ANIMATE, obj)
          },
        )
      } else {
        this.$store.commit(types.MUTATION.AFTER_ANIMATE, obj)
      }
    },
    // CSS for box
    cssBox (box) {
      let css = { // eslint-disable-line prefer-const
        explose: box.img < 0,
        miss: box.img === -2,
        dead: box.dead,
        hit: box.img === -1,
        opentip: (typeof box.player !== 'undefined' || typeof box.shoot !== 'undefined'),
        animated: box.img === -1,
        boat: (this.me !== null && box.team === this.me.team) || box.dead || (this.gameover && typeof box.player !== 'undefined'),
      }
      css['img' + box.img] = box.img > 0
      if (typeof box.player !== 'undefined') {
        css['player' + box.player] = true
      }
      // Replace animate on grid
      if ($('#g_' + box.y + '_' + box.x).hasClass('animated')) {
        css.animated = true
      }

      return css
    },
    // Text in tooltip
    tooltip (box) {
      let tooltip = [] // eslint-disable-line prefer-const

      // Victim or same team
      if (box.player !== null) {
        if (this.me && this.me.position === box.player) {
          tooltip.push(Translator.trans('your_boat', {}, 'js'))
        } else {
          const victim = this.playerById(box.player)
          if (victim) {
            tooltip.push(Translator.trans('boat_of', { name: victim.name }, 'js'))
          }
        }
      }

      // Shooter
      if (box.shoot !== null) {
        if (this.me && this.me.position === box.shoot) {
          tooltip.push(Translator.trans('your_shot', {}, 'js'))
        } else {
          const shooter = this.playerById(box.shoot)
          if (shooter) {
            tooltip.push(Translator.trans('shoot_of', { name: shooter.name }, 'js'))
          }
        }
      }
      return tooltip.length ? tooltip.join('<br>') : null
    },
    // click on a box
    click (box) {
      if (!window.isMobile()) {
        this.shoot(box)
      }
    },
    // Do a shoot
    shoot (box) {
      if (this.gameover || !this.me || (this.me && this.me.life <= 0) || (this.tour.indexOf(this.me.position + '') < 0)) {
        return false
      }

      // Data to send
      const data = {
        x: box.x,
        y: box.y,
      }

      // Event to edit data to send
      this.$store.dispatch(types.ACTIONS.BEFORE_SHOOT, data).then((data) => {
        this.$store.dispatch(types.ACTIONS.SHOOT, data)
      })
    },
  },
  watch: {
    // Tour => update status
    tour (tour) {
      let players = [] // eslint-disable-line prefer-const
      tour.forEach((playerId) => {
        players.push(this.playerById(playerId).name)
      })
      if (this.gameover) {
        this.$store.commit(types.MUTATION.SET_STATUS, Translator.trans('winner_list', { list: players.join(', ') }, 'js'))
      } else {
        this.$store.commit(types.MUTATION.SET_STATUS, Translator.trans('waiting_list', { list: players.join(', ') }, 'js'))
      }
    },
    // Game is over => relaod grid
    gameover (gameover) {
      if (gameover) {
        this.$store.dispatch(types.ACTIONS.LOAD_GAME).then((game) => {
          this.$store.commit(types.MUTATION.LOAD, game)
        })
      }
    },
  },
  mounted () {
    console.log('[VUE] Mount Grid.vue')

    // Subscribe mercure
    const slug = document.getElementById('slug').value
    Mercure.subscribeTopic('match.display', { slug: slug }, (obj) => {
      this.receive(obj)
    })

    // Disable select
    $(document)
      .bind('selectstart', () => false)
      .bind('ondragstart ondrop', () => false)
  },
}
</script>
