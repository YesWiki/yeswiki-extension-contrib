/* global peaks */

document.addEventListener('DOMContentLoaded', () => {
  const audioContext = new AudioContext()
  const primary = getComputedStyle(document.documentElement).getPropertyValue('--yw-primary').trim()

  document.querySelectorAll('.yw-sonogramme').forEach((container) => {
    peaks.init({
      highlightOffset: 0,
      overviewWaveformColor: container.dataset.color || primary || '#0c5d6a',
      containers: { overview: container.querySelector('.yw-sonogramme__overview') },
      mediaElement: container.nextElementSibling,
      webAudio: { audioContext }
    }, (error) => {
      if (error) console.error(error)
    })
  })
})
