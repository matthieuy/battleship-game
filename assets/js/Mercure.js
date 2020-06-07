import Routing from '@js/Routing'
/* global mercureURL */

/**
 * Mercure component
 */
class Mercure {
  /**
   * Construtor
   */
  constructor () {
    this.init = false
    this._subscribe = {}
    this._event = null
  }

  /**
   * Subscribe to topic
   * @param route {string} Route name
   * @param routeParams {Object} Route parameters
   * @param cb {function<MessageEvent>} Callback
   * @returns {Mercure}
   */
  subscribeTopic (route, routeParams, cb) {
    const topic = Routing.generate(route, routeParams, true)
    if (typeof this._subscribe[topic] === 'undefined') {
      console.log('[MERCURE] Subscribe topic', topic)
      this._subscribe[topic] = []
    }
    this._subscribe[topic].push(cb)

    return this
  }

  /**
   * Initialize the connexion
   * @returns {Mercure}
   */
  connect () {
    if (this.init) {
      throw Error('Mercure is already init')
    }
    if (!Object.size(this._subscribe)) {
      throw Error('[MERCURE] Any topic')
    }

    // Create mercure url
    const url = new URL(mercureURL)
    for (const topic in this._subscribe) {
      url.searchParams.append('topic', topic)
    }

    // Init
    this.init = true
    this._event = new EventSource(url)
    this._event.onmessage = (response) => {
      this._onReceive(response)
    }
    console.log('[MERCURE] Connect')

    return this
  }

  /**
   * When receive data from mercure
   * @param response {MessageEvent}
   * @returns {Promise<never>}
   * @private
   */
  _onReceive (response) {
    if (response.data && response.data !== '') {
      let data = null
      let topic = null
      try {
        data = JSON.parse(response.data)
        topic = data.topic
        console.log('[MERCURE] Receive', data.content, topic)
      } catch (e) {
        console.error('JSON Error', response.data, e)
        return Promise.reject(new Error('JSON Error'))
      }

      // Call all callback subscribe
      if (data && topic && typeof this._subscribe[topic] !== 'undefined') {
        // Redirect
        if (data.content.redirect) {
          return window.location.replace(data.content.redirect)
        }

        // Callback
        const listCallback = this._subscribe[topic]
        for (let i = 0; i < listCallback.length; i++) {
          listCallback[i](data.content)
        }
      }
    }
  }
}

export default new Mercure()
