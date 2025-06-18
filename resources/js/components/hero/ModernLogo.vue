<template>
  <div class="relative w-32 h-32 flex items-center justify-center mb-8">
    <!-- Outer ring -->
    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-400/30 via-purple-400/30 to-blue-600/30 backdrop-blur-sm border border-white/40 logo-outer-ring" />

    <!-- Middle ring -->
    <div class="absolute inset-2 rounded-full bg-gradient-to-br from-purple-400/20 via-blue-400/20 to-purple-600/20 backdrop-blur-sm border border-white/20 logo-middle-ring" />

    <!-- Inner circle -->
    <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 shadow-2xl flex items-center justify-center logo-inner-circle">
      <span class="text-3xl logo-emoji">
        {{ codingEmojis[currentEmoji] }}
      </span>
    </div>

    <!-- Floating particles -->
    <div
      v-for="(particle, i) in particles"
      :key="i"
      class="absolute w-1.5 h-1.5 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full logo-particle"
      :style="{...particle.style, animationDelay: (i * 0.15) + 's'}"
    />

    <!-- Additional sparkle effect -->
     <div
      v-for="(sparkle, i) in sparkles"
      :key="`sparkle-${i}`"
      class="absolute w-0.5 h-0.5 bg-white rounded-full logo-sparkle"
      :style="{...sparkle.style, animationDelay: sparkle.delay + 's'}"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

const currentEmoji = ref(0);
const codingEmojis = ["💻", "⌨️", "🖥️", "👨‍💻", "📱", "🔧", "⚙️", "🚀"];
let intervalId = null;

onMounted(() => {
  intervalId = setInterval(() => {
    currentEmoji.value = (currentEmoji.value + 1) % codingEmojis.length;
  }, 3000);
});

onUnmounted(() => {
  clearInterval(intervalId);
});

const particles = computed(() => Array.from({ length: 12 }).map((_, i) => ({
  style: {
    left: `${50 + 40 * Math.cos((i * Math.PI * 2) / 12)}%`,
    top: `${50 + 40 * Math.sin((i * Math.PI * 2) / 12)}%`,
    '--particle-x': `${Math.cos((i * Math.PI * 2) / 12) * 10}px`,
    '--particle-y': `${Math.sin((i * Math.PI * 2) / 12) * 10}px`,
  },
  x: Math.cos((i * Math.PI * 2) / 12) * 10,
  y: Math.sin((i * Math.PI * 2) / 12) * 10,
})));

const sparkles = computed(() => Array.from({ length: 6 }).map(() => ({
  style: {
    left: `${30 + Math.random() * 40}%`,
    top: `${30 + Math.random() * 40}%`,
  },
  delay: Math.random() * 2,
})));
</script>

<style scoped>
/* Logo Animations */
@keyframes rotate-clockwise {
  from { transform: rotate(0deg) scale(1); }
  50% { transform: rotate(180deg) scale(1.05); }
  to { transform: rotate(360deg) scale(1); }
}

@keyframes rotate-counter-clockwise {
  from { transform: rotate(0deg) scale(1); }
  50% { transform: rotate(-180deg) scale(1.03); }
  to { transform: rotate(-360deg) scale(1); }
}

@keyframes float-bounce {
  0%, 100% { transform: translateY(-3px) scale(1); }
  50% { transform: translateY(3px) scale(1.05); }
}

@keyframes emoji-wiggle {
  0%, 100% { transform: scale(1) rotate(0deg); }
  25% { transform: scale(1.1) rotate(2deg); }
  75% { transform: scale(1.1) rotate(-2deg); }
}

@keyframes particle-float {
  0%, 100% { 
    opacity: 0; 
    transform: scale(0.3) translate(0, 0); 
  }
  50% { 
    opacity: 1; 
    transform: scale(2) translate(var(--particle-x), var(--particle-y)); 
  }
}

@keyframes sparkle-twinkle {
  0%, 100% { 
    opacity: 0; 
    transform: scale(0); 
  }
  50% { 
    opacity: 1; 
    transform: scale(3); 
  }
}

/* Apply animations */
.logo-outer-ring {
  animation: rotate-clockwise 15s linear infinite;
}

.logo-middle-ring {
  animation: rotate-counter-clockwise 25s linear infinite;
}

.logo-inner-circle {
  animation: float-bounce 2.5s ease-in-out infinite;
  transition: transform 0.3s ease;
}

.logo-inner-circle:hover {
  transform: scale(1.1) rotate(5deg) !important;
}

.logo-emoji {
  animation: emoji-wiggle 3s ease-in-out infinite 0.5s;
}

.logo-particle {
  animation: particle-float 2.5s ease-in-out infinite;
}

.logo-sparkle {
  animation: sparkle-twinkle 1.5s ease-in-out infinite;
}
</style>
