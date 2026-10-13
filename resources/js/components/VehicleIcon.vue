<template>
  <div class="inline-flex items-center justify-center shrink-0">
    <!-- Backhoe Loader (BL-08) -->
    <svg
      v-if="type === 'BL-08'"
      class="w-12 h-9 drop-shadow-[0_2px_3px_rgba(0,0,0,0.2)]"
      viewBox="0 0 70 48"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <!-- Rear backhoe arm & bucket -->
      <path d="M52 24 L59 15 L66 18 L64 26 L58 22" fill="#4B5563" stroke="#374151" stroke-width="1" />
      <path d="M64 24 L69 28 L64 31 Z" fill="#D97706" />
      
      <!-- Vehicle main yellow body / chassis -->
      <path d="M22 28 L48 24 L52 34 L26 38 Z" fill="#F59E0B" />
      <path d="M22 28 L30 18 L44 16 L48 24 Z" fill="#D97706" />
      
      <!-- Cabin & glass -->
      <path d="M30 18 L34 10 L42 10 L44 16 Z" fill="#1F2937" />
      <path d="M32 17 L35 12 L40 12 L41 16 Z" fill="#93C5FD" opacity="0.8" />
      <rect x="36" y="8" width="6" height="3" rx="1" fill="#4B5563" />
      
      <!-- Engine hood & exhaust -->
      <path d="M22 28 L18 30 L26 38 L30 35 Z" fill="#B45309" />
      <line x1="26" y1="20" x2="26" y2="14" stroke="#374151" stroke-width="1.5" stroke-linecap="round" />
      
      <!-- Front Loader Arm & Bucket -->
      <path d="M20 29 L11 31 L8 36 L16 35 Z" fill="#6B7280" />
      <path d="M5 28 L11 30 L10 40 L3 37 Z" fill="#D97706" stroke="#B45309" stroke-width="0.8" />
      
      <!-- Rear large wheel -->
      <ellipse cx="44" cy="34" rx="7.5" ry="9" fill="#1F2937" />
      <ellipse cx="44" cy="34" rx="4.5" ry="5.5" fill="#4B5563" />
      <ellipse cx="44" cy="34" rx="2.5" ry="3" fill="#9CA3AF" />
      
      <!-- Front smaller wheel -->
      <ellipse cx="23" cy="37" rx="5" ry="6" fill="#1F2937" />
      <ellipse cx="23" cy="37" rx="3" ry="3.8" fill="#4B5563" />
      <ellipse cx="23" cy="37" rx="1.5" ry="2" fill="#9CA3AF" />
    </svg>

    <!-- Compact Loader / Bulldozer (Q-47898) -->
    <svg
      v-else-if="type === 'Q-47898'"
      class="w-12 h-9 drop-shadow-[0_2px_3px_rgba(0,0,0,0.2)]"
      viewBox="0 0 70 48"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <!-- Rear counterweight -->
      <path d="M46 22 L53 25 L51 34 L43 32 Z" fill="#D97706" />
      
      <!-- Main yellow body -->
      <path d="M25 26 L46 22 L45 35 L24 37 Z" fill="#F59E0B" />
      <path d="M25 26 L32 17 L42 16 L46 22 Z" fill="#EA580C" />
      
      <!-- Cabin -->
      <path d="M31 17 L34 11 L41 11 L43 16 Z" fill="#1F2937" />
      <path d="M33 16 L35 12 L39 12 L41 16 Z" fill="#BAE6FD" opacity="0.85" />
      
      <!-- Front Loader Arm -->
      <path d="M24 28 L14 30 L11 36 L19 34 Z" fill="#4B5563" />
      <circle cx="21" cy="29" r="1.5" fill="#D1D5DB" />
      
      <!-- Big Front Bucket -->
      <path d="M4 27 L13 29 L12 41 L2 38 Z" fill="#D97706" stroke="#92400E" stroke-width="0.8" />
      <path d="M2 38 L12 41 L10 43 L1 40 Z" fill="#78350F" />
      
      <!-- Rear Wheel -->
      <ellipse cx="44" cy="34" rx="6.5" ry="8" fill="#1F2937" />
      <ellipse cx="44" cy="34" rx="4" ry="5" fill="#4B5563" />
      <ellipse cx="44" cy="34" rx="2" ry="2.5" fill="#F59E0B" />
      
      <!-- Front Wheel -->
      <ellipse cx="26" cy="36" rx="6.5" ry="8" fill="#1F2937" />
      <ellipse cx="26" cy="36" rx="4" ry="5" fill="#4B5563" />
      <ellipse cx="26" cy="36" rx="2" ry="2.5" fill="#F59E0B" />
    </svg>

    <!-- Wheel Loader (D-26653) -->
    <svg
      v-else
      class="w-12 h-9 drop-shadow-[0_2px_3px_rgba(0,0,0,0.2)]"
      viewBox="0 0 70 48"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <!-- Rear Engine Housing -->
      <path d="M44 24 L52 26 L50 34 L42 33 Z" fill="#B45309" />
      <rect x="46" y="21" width="3" height="4" fill="#374151" rx="0.5" />
      
      <!-- Main Cabin & Yellow Frame -->
      <path d="M24 27 L44 23 L43 35 L23 37 Z" fill="#F59E0B" />
      <path d="M25 27 L31 16 L40 15 L43 23 Z" fill="#D97706" />
      
      <!-- Cabin Windows -->
      <path d="M30 16 L33 11 L39 11 L41 15 Z" fill="#111827" />
      <path d="M32 15 L34 12 L38 12 L39 15 Z" fill="#93C5FD" opacity="0.9" />
      
      <!-- Heavy Loader Boom Hydraulic Lift -->
      <path d="M23 29 L13 31 L10 36 L18 35 Z" fill="#4B5563" stroke="#374151" stroke-width="0.8" />
      <line x1="20" y1="26" x2="14" y2="33" stroke="#9CA3AF" stroke-width="1.8" stroke-linecap="round" />
      
      <!-- Heavy Bucket Scoop -->
      <path d="M4 27 L12 29 L11 41 L2 38 Z" fill="#F59E0B" stroke="#B45309" stroke-width="0.8" />
      <polygon points="2,38 5,42 12,41" fill="#78350F" />
      
      <!-- Rear Massive Wheel -->
      <ellipse cx="43" cy="34" rx="7" ry="8.5" fill="#1F2937" />
      <ellipse cx="43" cy="34" rx="4" ry="5.2" fill="#374151" />
      <ellipse cx="43" cy="34" rx="2" ry="2.8" fill="#FBBF24" />
      
      <!-- Front Massive Wheel -->
      <ellipse cx="25" cy="36" rx="7" ry="8.5" fill="#1F2937" />
      <ellipse cx="25" cy="36" rx="4" ry="5.2" fill="#374151" />
      <ellipse cx="25" cy="36" rx="2" ry="2.8" fill="#FBBF24" />
    </svg>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'BL-08',
  },
});
</script>

