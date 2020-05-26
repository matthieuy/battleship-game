/***********
 * Tooltip *
 ***********/
/* global $ */
const moveTooltip = function (e) {
  const height = $('div.tooltip').outerHeight()
  $('div.tooltip').css({
    top: (e.pageY - height - 8),
    left: (e.pageX),
  })
}

const openTooltip = function (e) {
  $('div.tooltip').remove()
  $('<div class="tooltip">' + $(this).attr('data-tip') + '</div>').appendTo('body')
  moveTooltip(e)
}

const hideTooltip = function () {
  $('div.tooltip').remove()
}

$(document)
  .on('mouseenter', '.opentip', openTooltip)
  .on('mousemove', '.opentip', moveTooltip)
  .on('mouseleave', '.opentip', hideTooltip)
