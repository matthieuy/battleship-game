/**
 * Get size of a object
 * @param obj
 * @returns {number}
 */
Object.size = function (obj) {
  let size = 0
  for (const key in obj) {
    if (obj.hasOwnProperty(key)) {
      size++
    }
  }
  return size
}

/**
 * Detect if device is mobile
 * @returns {boolean}
 */
window.isMobile = function () {
  return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(navigator.userAgent)
}
