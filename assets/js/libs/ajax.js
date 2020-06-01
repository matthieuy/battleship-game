import axios from 'axios'
import Flash from '@js/Flash'

export default {
  ...axios,
  /**
   * Do a AJAX POST call
   * @private
   * @param url
   * @param params
   * @param errorMsg
   * @returns {Promise}
   */
  postCall (url, params, errorMsg) {
    return axios.post(url, new URLSearchParams(params)).then((response) => {
      console.log(response.status, response.data, response.data.hasOwnProperty('error'))
      if (response.status === 200) {
        if (response.data.success) {
          return Promise.resolve(response.data)
        } else if (response.data.hasOwnProperty('error')) {
          errorMsg = response.data.error
        }

        return Promise.reject(new Error(errorMsg))
      }
      return Promise.reject(new Error(errorMsg))
    }).catch(() => {
      Flash.error(errorMsg)
    })
  },
}
