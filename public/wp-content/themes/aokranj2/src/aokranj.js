/*!
 * AO Kranj scripts
 *
 * Author Bojan Hribernik
 * Copyright AO Kranj
 */

import 'jquery'
import 'popper.js'
import 'bootstrap'

import './aokranj.scss'

import throttle from './js/utils/throttle'

$(function() {

  // enable tooltips
  $('[data-toggle="tooltip"]').tooltip()

  // admin bar toggler (key h)
  if ($('body.logged-in').length) {
    $(document).on('keydown', function(e) {
      if (e.keyCode === 72 && e.shiftKey) {
        $('#wpadminbar').toggle()
        $('body').toggleClass('admin-bar')
      }
    })
  }

  // toggle navbar
  $('.navbar-toggler').on('click', function() {
    $('.navbar-toggler').toggleClass('collapsed')
    $('.aokranj-header').toggleClass('expanded')
    if ($('.aokranj-header').is('.expanded')) {
      $('body').css({ height: '100vh', overflow: 'hidden' })
    } else {
      $('body').css({ height: 'auto', overflowX: 'hidden', overflowY: 'auto' })
    }
  })

  // show/hide go to top button
  $(window).on('scroll', throttle(function(){
    var scroll = $(window).scrollTop()
    var height = $(window).height()
    $('.go-top')[scroll > height ? 'addClass' : 'removeClass']('show')
  }, 100))

  // click on go to top button
  $('a.go-top').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 500)
    return false
  })

})
