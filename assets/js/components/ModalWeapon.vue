<template>
  <div v-show="weapon.modal">
    <div class="modal-bg"></div>
    <div class="modal-wrap">
      <div class="modal-container" @on:click.stop.prevent="close()">
        <div class="modal-content">
          <div id="modal-weapon" @on:click.stop.prevent="">
            <div class="center">
              <h1>{{ trans('weapon_name', {}, 'js') }}</h1>
              <div v-show="me"><strong>{{ transChoice('points_plurial', score, { nb: score }, 'js') }}</strong></div>
            </div>
            <div class="clear"></div>

            <div class="large-12 column">
              <div class="row">
                <div v-for="w in weapon.list" class="large-3 column">
                  <div :class="classWeapon(w)" class="center weapon" @click="highlight(w)">
                    <h3>{{ trans(w.name, {}, 'js') }}</h3>
                    <em>{{ trans('Price', {}, 'js') }} : {{ transChoice('points_plurial', w.price, { nb: w.price }, 'js') }}</em>
                    <div class="grid weapon-model" :style="styleModel(w)">
                      <div v-for="row in w.grid" class="clear row">
                        <span v-for="box in row" :class="{'explose hit animated': box }" class="box"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="clear"></div>
            <div class="large-12 center">
              <div class="row btn-action">
                <button class="button success small-10 large-3" :class="{disabled: !selected}" @click="select()">
                  {{ trans('Select', {}, 'js') }}
                </button>
                <button class="button primary small-10 large-3" @click="rotate()">
                  {{ trans('Rotate', {}, 'js') }}
                </button>
                <button class="button alert small-10 large-3" @click="close()">
                  {{ trans('Close', {}, 'js') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import * as types from '@js/store/game/types'
/* global $, Translator */

export default {
  data () {
    return {
      selected: null,
      trans () {
        return Translator.trans(...arguments)
      },
      transChoice () {
        return Translator.transChoice(...arguments)
      },
    }
  },
  computed: {
    ...mapState([
      'weapon', // Weapon module
      'me',
      'gameover',
    ]),
    score () {
      return this.weapon.score
    },
  },
  methods: {
    // Close the modal
    close () {
      this.$store.commit(types.MUTATION.WEAPON_MODAL, false)
      this.$store.commit(types.MUTATION.WEAPON_SELECT)
      this.selected = null
    },
    // Select weapon
    select () {
      if (this.selected) {
        this.$store.commit(types.MUTATION.WEAPON_SELECT, this.selected)
        this.$store.commit(types.MUTATION.WEAPON_MODAL, false)
        this.selected = null
      }
    },
    // Rotate all weapon matrix
    rotate () {
      this.$store.commit(types.MUTATION.WEAPON_SELECT)
      this.selected = null
      this.$store.commit(types.MUTATION.WEAPON_ROTATE)
    },
    // Highlight the weapon (on click)
    highlight (weapon) {
      if (this.score >= weapon.price && !this.gameover && this.me.life > 0) {
        this.selected = weapon
      }
    },
    // CSS class for weapon box
    classWeapon (weapon) {
      return {
        disabled: weapon.price > this.score || this.gameover || this.me.life <= 0,
        selected: this.selected && weapon.name === this.selected.name,
      }
    },
    // Calcul style of model weapon
    styleModel (weapon) {
      return {
        width: (weapon.grid[0].length * 20) + 'px',
        marginTop: (6 - weapon.grid.length) * 10 + 'px',
      }
    },
  },
  watch: {
    // Load weapons on the first call and display modal
    'weapon.modal' (open) {
      // Load weapons list
      if (open && !this.weapon.loaded) {
        this.$store.dispatch(types.ACTIONS.WEAPON_LOAD).then((list) => {
          this.$store.commit(types.MUTATION.WEAPON_SETLIST, list)
        })
      }

      // Fix scroll
      if (open) {
        $('#container').css({
          overflow: 'hidden',
          position: 'fixed',
        })
      } else {
        $('#container').removeAttr('style')
      }

      // Bind escape touch
      if (open || this.weapon.modal || this.weapon.select) {
        const self = this
        const escapeTouch = (e) => {
          if (e.which === 27) {
            if (self.$store.state.weapon.modal) {
              self.$store.commit(types.MUTATION.WEAPON_MODAL, false)
            } else {
              self.$store.commit(types.MUTATION.WEAPON_SELECT)
            }
            $(window).off('keyup', escapeTouch)
          }
        }
        $(window).on('keyup', escapeTouch)
      }
    },
    // On select : add helper on the grid
    'weapon.select' (weapon) {
      if (this.gameover || this.me.life <= 0) {
        return false
      }

      weapon = this.$store.getters.getWeapon(weapon)
      if (weapon) {
        // Get weapon grid
        const weaponBoxes = weapon.grid
        const weaponSize = [weaponBoxes.length, weaponBoxes[0].length]
        const weaponCenter = [Math.floor(weaponSize[0] / 2), Math.floor(weaponSize[1] / 2)]

        // Get list of box
        let boxes = [] // eslint-disable-line prefer-const
        for (let y = 0; y < weaponSize[0]; y++) {
          for (let x = 0; x < weaponSize[1]; x++) {
            if (weaponBoxes[y][x]) {
              boxes.push([
                x - weaponCenter[1],
                y - weaponCenter[0],
              ])
            }
          }
        }

        // Add target class in box to shoot
        $('#grid')
          .off('mouseover mouseout', 'span')
          .on('mouseover', 'span', function () {
            const el = ($(this).hasClass('box')) ? $(this) : $(this).parent('.box')
            const coord = el.attr('id').split('_').map((i) => parseInt(i))
            coord.shift()

            boxes.forEach(function (box) {
              const id = '#g_' + (box[1] + coord[0]) + '_' + (box[0] + coord[1])
              if ($(id).length === 1) {
                $(id).addClass('target')
              }
            })
          })
          .on('mouseout', 'span', function () {
            $('#grid .target').removeClass('target')
          })
      } else {
        // Unbind helper
        $('#grid .target').removeClass('target')
        $('#grid').off('mouseover mouseout', 'span')
      }
    },
  },
  mounted () {
    console.log('[VUE] Mount ModalWeapon.vue')
  },
}
</script>

<style lang="less">
  #grid span.target {
    background-color: rgba(255, 0, 0, 0.75);
  }

  #modal-weapon {
    .grid .box {
      width: 20px;
      height: 20px;
      cursor: default;
    }
    .hit {
      background-position: 120px 20px;
      background-size: 240px 40px;
    }
    .hit.animated {
      animation: explose-fire-modal 2s steps(12) infinite;
    }
    .weapon {
      cursor: pointer;
      margin-top: 20px;
      padding: 10px;
      height: 180px;
      background-color: #f2f2f2;
      border: 1px solid #d9d9d9;

      h3 {
        font-size: 1.4em;
      }

      &.selected {
        outline: #1F7E1F solid 2px;
        background-color: #b0ff9e;
      }

      &.disabled {
        border: 2px solid #F00;
        background-color: #FDE;
        cursor: default;
      }
    }
    .weapon-model {
      margin: 5px auto;
      .row {
        margin: 0;
        height: 20px;
      }
    }
  }

  @keyframes explose-fire-modal {
    0% {
      background-position: 0 20px;
    }
    100% {
      background-position: 240px 20px;
    }
  }
</style>
