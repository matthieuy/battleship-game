/**
 * List of mutations and actions for waiting store
 */

// Mutations
export const MUTATION = {
  SET_USERID: 'SET_USERID', // Set the current user id
  SET_LOADED: 'SET_LOADED', // Set if game is loaded
  SET_SLUG: 'SET_SLUG', // Set slug
  SET_GAMEINFO: 'SET_GAMEINFO', // Set game infos
}

// Actions
export const ACTION = {
  LOAD_INFO: 'LOAD_INFO', // Load game's infos
  CHANGE_SIZE: 'CHANGE_SIZE', // Change grid size
  CHANGE_MAXPLAYER: 'CHANGE_MAXPLAYER', // Change max player
  CHANGE_OPTIONS: 'CHANGE_OPTIONS', // Change game options
  CHANGE_COLOR: 'CHANGE_COLOR', // Change player color
  CHANGE_TEAM: 'CHANGE_TEAM', // Change team
  JOIN_LEAVE: 'JOIN_LEAVE', // Join/Leave a game
  REMOVE_PLAYER: 'REMOVE_PLAYER',
}
