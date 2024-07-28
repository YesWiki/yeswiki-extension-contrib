import PhotoSwipeLightbox from '../node_modules/photoswipe/dist/photoswipe-lightbox.esm.js';
import PhotoSwipeDynamicCaption from '../node_modules/photoswipe-dynamic-caption-plugin/dist/photoswipe-dynamic-caption-plugin.esm.min.js';

Vue.component('BazarGallery', {
  methods: {
    checkImageSize(event) {
      const h = event.originalTarget.naturalHeight
      const w = event.originalTarget.naturalWidth
      if (h > w) {
        const newWidth = 1080 / h * w
        event.originalTarget.parentElement.dataset.pswpWidth = newWidth
      } else {
        const newHeight = 1920 / w * h
        event.originalTarget.parentElement.dataset.pswpHeight = newHeight
      }
    }
  },
  mounted() {
    const lightbox = new PhotoSwipeLightbox({
      gallery: '.photoswipe-gallery',
      children: 'a',
      pswpModule: () => import('../node_modules/photoswipe/dist/photoswipe.esm.js')
    });
    const captionPlugin = new PhotoSwipeDynamicCaption(lightbox, {
      // Plugins options, for example:
      type: 'auto',
      captionContent: '.pswp-caption-content'
    });
    lightbox.init();
  },
  computed: {
    entries() {
      return this.$root.entriesToDisplay.filter((entry) => entry.visual)
    }
  },
  template: `
<div class="photoswipe-gallery">
  <a v-for="entry in entries"
     :key="entry.id_fiche"
     class="bazar-entry"
     data-pswp-width="1920" 
     data-pswp-height="1080"
     data-cropped="true" 
     target="_blank"
     :title="entry.bf_titre"
     :href="'?api/images/resize/' + entry.visual+'&w=1920&h=1080&fit=max'"
  >
      <img   
        loading="lazy"
        :alt="entry.bf_titre"
        @load="(event) => checkImageSize(event)"
        class="pswp-image" 
        :src="'?api/images/resize/'+entry.visual+'&w='+$root.params.imgWidth+'&h='+$root.params.imgHeight+'&fit=max'"
      />
      <span class="pswp-caption-content">
        <h2 v-html="entry.bf_titre"></h2>
        <span v-if="entry.subtitle" v-html="entry.subtitle"></span>
      </span>
  </a>
  <div class="spacer"></div>
</div>
`
})