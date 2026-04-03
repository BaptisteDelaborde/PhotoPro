<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  images: { type: Array, required: true },
  initialIndex: { type: Number, default: 0 }
})

const emit = defineEmits(['close'])

const currentIndex = ref(props.initialIndex)
const imageContainer = ref(null)

const closeLightbox = () => emit('close')

const nextImage = () => {
  currentIndex.value = (currentIndex.value + 1) % props.images.length
}

const prevImage = () => {
  currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length
}

// Plein écran
const toggleFullScreen = async () => {
  if (!document.fullscreenElement) {
    await imageContainer.value.requestFullscreen().catch(err => {
      console.error(`Erreur d'activation du plein écran : ${err.message}`)
    })
  } else {
    await document.exitFullscreen()
  }
}

const handleKeydown = (e) => {
  if (e.key === 'Escape') closeLightbox()
  if (e.key === 'ArrowRight') nextImage()
  if (e.key === 'ArrowLeft') prevImage()
}

onMounted(() => window.addEventListener('keydown', handleKeydown))
onUnmounted(() => window.removeEventListener('keydown', handleKeydown))
</script>


<template>
  <div class="lightbox-overlay" @click.self="closeLightbox">
    <div class="controls-top">
      <button @click="toggleFullScreen" class="control-btn" title="Plein écran">⛶</button>
      <button @click="closeLightbox" class="control-btn" title="Fermer">✖</button>
    </div>

    <button class="nav-btn prev" @click.stop="prevImage" v-if="images.length > 1">◀</button>

    <div ref="imageContainer" class="image-wrapper">
      <img
          :src="images[currentIndex].url"
          :alt="images[currentIndex].title || 'Image de la galerie'"
          class="lightbox-img"
      />
      <p class="image-caption" v-if="images[currentIndex].title">
        {{ images[currentIndex].title }}
      </p>
    </div>

    <button class="nav-btn next" @click.stop="nextImage" v-if="images.length > 1">▶</button>
  </div>
</template>
<style src="../assets/css/main.css" scoped></style>
