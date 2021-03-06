/**
 * Sidebar module
 */
import $ from 'jquery'
import slidebars from 'slidebars'

export default {
  sidebar: null,
  init: function () {
    // Init Slidebar
    this.sidebar = new slidebars({ // eslint-disable-line new-cap
      speed: 1000,
      siteClose: true,
      disableOver: false,
      hideControlClasses: false,
    })
    this.sidebar.init()

    // Event listener
    const self = this
    $('#btn-menu').click(function (e) {
      e.stopPropagation()
      self.sidebar.toggle('sidebar')
    })
    $('#container, .sb-close').click(function () {
      self.sidebar.close('sidebar')
    })

    // Submenu
    const $submenu = $('.sidebar-menu .has-submenu')
    if ($submenu.length) {
      $submenu.click(function () {
        if ($(this).hasClass('disabled')) {
          return false
        }

        $(this).toggleClass('active')
        const ul = $(this).find('ul')
        if ($(ul).length > 0) {
          if ($(this).hasClass('active')) {
            $(ul).slideDown(600)
          } else {
            $(ul).slideUp(400)
          }
        }
        return false
      })
    }
  },
}
