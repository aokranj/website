export default function throttle(callback, delay) {
  var timeout
  ,last_run = 0
  return function() {
    if (timeout) {
      return
    }
    var elapsed = (+new Date()) - last_run
    ,context = this
    ,args = arguments
    ,run_callback = function() {
      last_run = +new Date()
      timeout = false
      callback.apply(context, args)
    }
    
    if (elapsed >= delay) {
      run_callback()
    }
    else {
      timeout = setTimeout(run_callback, delay)
    }
  }
}
