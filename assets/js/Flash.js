/* global Translator, $ */

const Flash = function () {
  $('.flash-container').on('click', '.close', function () {
    const $flash = $(this).parents('.flash-msg')
    $flash.slideUp(300, () => {
      $flash.remove()
    })
  })

  /**
   * Add a error message
   * @param msg
   * @returns {boolean}
   */
  this.error = (msg) => {
    addMessage('error', msg)
    console.error(Translator.trans(msg, {}, 'js'))
    return false
  }

  /**
   * Add a message
   * @param {String} type error|info|success
   * @param {String} msg  The message
   * @returns {boolean} False
   * @private
   */
  const addMessage = (type, msg) => {
    if (msg === '') {
      return false
    }
    const flash = $('<div class="flash-msg ' + type + '" style="display: none;"><div class="close">&times;</div>' + Translator.trans(msg, {}, 'js') + '</div>').appendTo('.flash-container')
    $('#container').animate({ scrollTop: $('.flash-container').offset().top - 5 }, 2000)
    $(flash).show('slow', function () {
      setTimeout(function () {
        $(flash).hide('slow', function () {
          $(this).remove()
        })
      }, 10000)
    })

    return false
  }

  return this
}

module.exports = new Flash()
