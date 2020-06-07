/**
 * List of mutations and actions for running store
 */

export const MUTATION = {
  SET_USERID: 'SET_USERID', // Set the current user id
  SET_SLUG: 'SET_SLUG', // Set the slug
  SET_STATUS: 'SET_STATUS', // Set status text
  LOAD: 'FIRST_LOAD', // Load the game
  SCORE_SET_LIFE: 'SCORE_SET_LIFE', // Set life
  WEAPON_MODAL: 'WEAPON_MODAL', // Toggle weapon modal
  WEAPON_SELECT: 'WEAPON_SELECT', // Select a weapon
  WEAPON_SETLIST: 'WEAPON_SETLIST', // Set weapon list
  WEAPON_ROTATE: 'WEAPON_ROTATE', // Rotate all weapons
  WEAPON_SET_SCORE: 'WEAPON_SET_SCORE', // Set weapon score
  AFTER_ANIMATE: 'AFTER_ANIMATE', // After all animate finished
  AFTER_ROCKET: 'AFTER_ROCKET', // After each rocket
}
export const ACTIONS = {
  LOAD_GAME: 'LOAD_GAME', // Load the game
  WEAPON_LOAD: 'WEAPON_LOAD', // Load weapons
  BEFORE_SHOOT: 'BEFORE_SHOOT', // Before shoot
  SHOOT: 'SHOOT', // Do fire
  AFTER_ROCKET: 'AFTER_ROCKET', // After each rocket
}
