import PhotoSwipeLightbox from './vendor/photoswipe/photoswipe-lightbox.esm.min.js'
import PhotoSwipeDynamicCaption from './vendor/photoswipe/photoswipe-dynamic-caption-plugin.esm.min.js'

window._bazarDynamicComponents = window._bazarDynamicComponents || {}
window._bazarDynamicComponents.BazarGallery = {
  props: {
    width: { type: Number, default: 600 },
    height: { type: Number, default: 400 },
    token: { type: String, default: '' }
  },
  methods: {
    original(entry) {
      return `${wiki.baseUrl.replace(/\?$/, '').replace(/\/$/, '')}/files/${entry.visual}`
    },
    checkImageSize(event) {
      const image = event.target
      const h = image.naturalHeight
      const w = image.naturalWidth
      if (h > w) {
        image.parentElement.dataset.pswpWidth = Math.round((1080 / h) * w)
      } else {
        image.parentElement.dataset.pswpHeight = Math.round((1920 / w) * h)
      }
    }
  },
  mounted() {
    const lightbox = new PhotoSwipeLightbox({
      gallery: '.photoswipe-gallery',
      children: 'a',
      pswpModule: () => import('./vendor/photoswipe/photoswipe.esm.min.js')
    })
    new PhotoSwipeDynamicCaption(lightbox, { type: 'auto', captionContent: '.pswp-caption-content' })
    lightbox.init()
  },
  computed: {
    entries() {
      return this.$root.entriesToDisplay.filter((entry) => entry.visual)
    }
  },
  template: `
<div class="photoswipe-gallery">
  <a v-for="entry in entries"
     :key="entry.tag"
     class="bazar-entry"
     data-pswp-width="1920"
     data-pswp-height="1080"
     data-cropped="true"
     target="_blank"
     :title="entry.title"
     :href="original(entry)">
    <img loading="lazy"
         :alt="entry.title"
         class="pswp-image"
         :src="$root.urlImage(entry, 'visual', width, height, 'fit')"
         @error="$root.urlImageResizedOnError(entry, 'visual', width, height, 'fit', token, $event)"
         @load="checkImageSize" />
    <span class="pswp-caption-content">
      <h2 v-html="entry.title"></h2>
      <span v-if="entry.subtitle" v-html="entry.subtitle"></span>
    </span>
  </a>
  <div class="spacer"></div>
</div>
`
}
