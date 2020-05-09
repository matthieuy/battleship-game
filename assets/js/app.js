/**
 * Global script and CSS
 */


// Theme
import '../css/_reset.less';
import '../css/_grid.less';
import '@npm/font-awesome/less/font-awesome.less';
import '@npm/typeface-lobster/index.css';
import '../game-icons/game-icons-font.less';
import '../css/_form.less';
import '../css/_flash.less';
import '../css/_buttons.less';
import '../css/topbar.less';
import '../css/sidebar.less';
import '../css/theme.less';


import $ from 'jquery'
window.Translator = require('bazinga-translator')

// Flash message
import Flash from './Flash'
import Sidebar from './Sidebar'

// Document ready
$(() => {
  // Sidebar
  Sidebar.init()
})
