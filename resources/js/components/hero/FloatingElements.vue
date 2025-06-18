<template>
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <!-- Primary gradient orb -->
    <div class="absolute top-1/6 left-1/6 w-96 h-96 bg-gradient-to-br from-blue-400/25 via-purple-400/20 to-blue-600/25 rounded-full blur-3xl floating-orb-1" />

    <!-- Secondary gradient orb -->
    <div class="absolute bottom-1/6 right-1/6 w-80 h-80 bg-gradient-to-br from-purple-400/20 via-pink-400/15 to-blue-400/20 rounded-full blur-3xl floating-orb-2" />

    <!-- Third accent orb -->
    <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-gradient-to-br from-emerald-400/15 via-blue-400/10 to-purple-400/15 rounded-full blur-3xl floating-orb-3" />

    <!-- Animated floating particles -->
    <div
      v-for="(particle, i) in particles"
      :key="i"
      class="absolute w-3 h-3 bg-gradient-to-r from-blue-400/50 to-purple-400/50 rounded-full floating-particle"
      :style="{...particle.style, animationDelay: particle.delay + 's', animationDuration: particle.duration + 's'}"
    />

    <!-- Twinkling stars -->
    <div
      v-for="(star, i) in stars"
      :key="`star-${i}`"
      class="absolute w-1 h-1 bg-white rounded-full twinkling-star"
      :style="{...star.style, animationDelay: star.delay + 's', animationDuration: star.duration + 's'}"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';

const particles = computed(() => Array.from({ length: 15 }).map(() => ({
  style: {
    left: `${5 + Math.random() * 90}%`,
    top: `${5 + Math.random() * 90}%`,
  },
  x: [0, Math.random() * 100 - 50, 0],
  y: [0, Math.random() * 100 - 50, 0],
  duration: 6 + Math.random() * 8,
  delay: Math.random() * 5,
})));

const stars = computed(() => Array.from({ length: 25 }).map(() => ({
  style: {
    left: `${Math.random() * 100}%`,
    top: `${Math.random() * 100}%`,
  },
  duration: 3 + Math.random() * 4,
  delay: Math.random() * 6,
})));
</script>

<style scoped>
/* Floating Orb Animations */
@keyframes floating-orb-1 {
  0%, 100% { 
    transform: translate(0px, 0px) scale(1) rotate(0deg); 
  }
  25% { 
    transform: translate(80px, -40px) scale(1.3) rotate(90deg); 
  }
  50% { 
    transform: translate(-30px, 60px) scale(0.9) rotate(180deg); 
  }
  75% { 
    transform: translate(40px, 20px) scale(1.1) rotate(270deg); 
  }
}

@keyframes floating-orb-2 {
  0%, 100% { 
    transform: translate(0px, 0px) scale(1) rotate(0deg); 
  }
  25% { 
    transform: translate(-60px, 50px) scale(1.4) rotate(-90deg); 
  }
  50% { 
    transform: translate(40px, -20px) scale(0.8) rotate(-180deg); 
  }
  75% { 
    transform: translate(-20px, 30px) scale(1.2) rotate(-270deg); 
  }
}

@keyframes floating-orb-3 {
  0%, 100% { 
    transform: translate(0px, 0px) scale(0.8) rotate(0deg); 
  }
  20% { 
    transform: translate(40px, -30px) scale(1.2) rotate(72deg); 
  }
  40% { 
    transform: translate(-40px, 30px) scale(1) rotate(144deg); 
  }
  60% { 
    transform: translate(20px, 40px) scale(1.1) rotate(216deg); 
  }
  80% { 
    transform: translate(-30px, -20px) scale(0.9) rotate(288deg); 
  }
}

@keyframes floating-particle {
  0%, 100% { 
    opacity: 0; 
    transform: scale(0.3) rotate(0deg); 
  }
  50% { 
    opacity: 0.8; 
    transform: scale(1.5) rotate(180deg); 
  }
}

@keyframes twinkling-star {
  0%, 100% { 
    opacity: 0; 
    transform: scale(0.5); 
  }
  50% { 
    opacity: 1; 
    transform: scale(2); 
  }
}

/* Apply animations */
.floating-orb-1 {
  animation: floating-orb-1 20s ease-in-out infinite;
}

.floating-orb-2 {
  animation: floating-orb-2 18s ease-in-out infinite;
  animation-delay: 3s;
}

.floating-orb-3 {
  animation: floating-orb-3 25s ease-in-out infinite;
  animation-delay: 6s;
}

.floating-particle {
  animation: floating-particle 6s ease-in-out infinite;
}

.twinkling-star {
  animation: twinkling-star 3s ease-in-out infinite;
}
</style>
