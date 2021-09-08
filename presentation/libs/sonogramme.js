/* global peaks */

document.addEventListener('DOMContentLoaded', () => {
  const audioContext = new AudioContext();
  const $ = (selector) => Array.from(document.querySelectorAll(selector))

  function onLoad (err, peaks) {
    err && console.error(err)
  }

  $('.yw-sonogramme').forEach((container) => {
    peaks.init({
      highlightOffset: 0,
      overviewWaveformColor: container.dataset.color || 'var(--primary-color)',
      //
      containers: {
        overview: container.querySelector('.overview-container')
      },
      mediaElement: container.nextElementSibling,
      webAudio: {
        audioContext
      }
    }, onLoad)
  })
})
