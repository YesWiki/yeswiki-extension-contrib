#!/usr/bin/env bash

# Extract from node_modules the files the extension ships, so that a production
# server never needs node. The extracted files are ignored by git.

cd "$(dirname "$0")/.." || exit 1

# Copy a JS file while stripping sourceMappingURL comments
copy_js() { sed '/^[[:space:]]*\/\/#[[:space:]]*sourceMappingURL=/d' "$1" > "$2"; }
# Copy a CSS file while stripping sourceMappingURL comments
copy_css() { sed '/^[[:space:]]*\/\*#[[:space:]]*sourceMappingURL=/d' "$1" > "$2"; }

mkdir -p javascripts/vendor/peaks.js
copy_js node_modules/peaks.js/peaks.js javascripts/vendor/peaks.js/peaks.js

mkdir -p javascripts/vendor/photoswipe
copy_js node_modules/photoswipe/dist/photoswipe.esm.min.js javascripts/vendor/photoswipe/photoswipe.esm.min.js
copy_js node_modules/photoswipe/dist/photoswipe-lightbox.esm.min.js javascripts/vendor/photoswipe/photoswipe-lightbox.esm.min.js
copy_js node_modules/photoswipe-dynamic-caption-plugin/dist/photoswipe-dynamic-caption-plugin.esm.min.js javascripts/vendor/photoswipe/photoswipe-dynamic-caption-plugin.esm.min.js
copy_css node_modules/photoswipe/dist/photoswipe.css javascripts/vendor/photoswipe/photoswipe.css
copy_css node_modules/photoswipe-dynamic-caption-plugin/photoswipe-dynamic-caption-plugin.css javascripts/vendor/photoswipe/photoswipe-dynamic-caption-plugin.css
