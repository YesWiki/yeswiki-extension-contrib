#!/bin/bash

# Extract files that we need from the node_modules folder
# The extracted files are integrated to the repository, so production server don't need to
# have node installed

# peaks.js
mkdir -p javascripts/vendor/peaks.js
cp -f node_modules/peaks.js/peaks.js javascripts/vendor/peaks.js/peaks.js

# Photoswipe
mkdir -p javascripts/vendor/photoswipe
cp -f node_modules/photoswipe/dist/photoswipe.esm.min.js javascripts/vendor/photoswipe/photoswipe.esm.min.js
cp -f node_modules/photoswipe-dynamic-caption-plugin/dist/photoswipe-dynamic-caption-plugin.esm.min.js javascripts/vendor/photoswipe/photoswipe-dynamic-caption-plugin.esm.min.js
cp -f node_modules/photoswipe/dist/photoswipe-lightbox.esm.min.js javascripts/vendor/photoswipe/photoswipe-lightbox.esm.min.js
cp -f node_modules/photoswipe/dist/photoswipe.css javascripts/vendor/photoswipe/photoswipe.css
cp -f node_modules/photoswipe-dynamic-caption-plugin/photoswipe-dynamic-caption-plugin.css javascripts/vendor/photoswipe/photoswipe-dynamic-caption-plugin.css
