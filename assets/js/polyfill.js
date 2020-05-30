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
