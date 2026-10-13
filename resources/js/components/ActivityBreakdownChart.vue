<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex flex-col justify-between">
    <!-- Header -->
    <div class="flex items-center gap-2 mb-2">
      <!-- Vehicle Icon -->
      <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="currentColor">
        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
      </svg>
      <h3 class="text-[15px] font-semibold text-gray-700">Activity breakdown (Hours)</h3>
    </div>

    <!-- Chart + Legend Area -->
    <div class="relative w-full h-[220px] flex items-center justify-center">
      <!-- Doughnut Chart with Center Text -->
      <div class="relative w-[190px] h-[190px] flex items-center justify-center">
        <canvas ref="canvasRef"></canvas>

        <!-- Center Text: Dynamic Hours -->
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
          <span class="text-[26px] font-bold text-gray-800 tracking-tight leading-none">{{ formattedTotalHours }}</span>
          <span class="text-[12px] text-gray-500 font-normal mt-1 leading-none">Hours</span>
        </div>
      </div>

      <!-- Legend -->
      <div class="ml-6 flex flex-col gap-3">
        <div
          v-for="item in legendItems"
          :key="item.label"
          class="flex items-center gap-2.5 text-[13px] text-gray-600 select-none"
        >
          <span
            class="w-2.5 h-2.5 rounded-full shrink-0"
            :style="{ backgroundColor: item.color }"
          ></span>
          <span class="font-normal">{{ item.label }}</span>
          <span v-if="item.percentage" class="text-xs text-gray-400 font-medium">({{ item.percentage }}%)</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import {
  Chart as ChartJS,
  DoughnutController,
  ArcElement,
  Tooltip,
} from 'chart.js';

ChartJS.register(DoughnutController, ArcElement, Tooltip);

const canvasRef = ref(null);
let chartInstance = null;
let pollTimer = null;

const totalHours = ref(1246);
const legendItems = ref([
  { label: 'Ignition Off', color: '#8B1D2C', percentage: 20 },
  { label: 'Ignition', color: '#00875A', percentage: 53.3 },
  { label: 'Idling', color: '#E67E22', percentage: 26.7 },
]);

const formattedTotalHours = computed(() => {
  return Number(totalHours.value).toLocaleString();
});

const initOrUpdateChart = (labels, series, colors) => {
  if (!canvasRef.value) return;

  if (chartInstance) {
    chartInstance.data.labels = labels;
    chartInstance.data.datasets[0].data = series;
    chartInstance.data.datasets[0].backgroundColor = colors;
    chartInstance.update();
    return;
  }

  const ctx = canvasRef.value.getContext('2d');

  chartInstance = new ChartJS(ctx, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [
        {
          data: series,
          backgroundColor: colors,
          borderColor: '#FFFFFF',
          borderWidth: 3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '72%',
      rotation: -30,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#FFFFFF',
          titleColor: '#374151',
          bodyColor: '#4B5563',
          borderColor: '#E5E7EB',
          borderWidth: 1,
          padding: 8,
          usePointStyle: true,
          callbacks: {
            label: (context) => {
              const label = context.label || '';
              const value = context.parsed || 0;
              const percent = totalHours.value > 0 ? ((value / totalHours.value) * 100).toFixed(1) : 0;
              return ` ${label}: ${value} hrs (${percent}%)`;
            },
          },
        },
      },
    },
  });
};

const fetchData = async () => {
  try {
    const res = await fetch('/api/dashboard/activity-breakdown');
    if (!res.ok) throw new Error('Network error');
    const json = await res.json();
    if (json.success && json.data) {
      totalHours.value = json.data.totalHours ?? 1246;
      if (json.data.breakdown) {
        legendItems.value = json.data.breakdown.map((item) => ({
          label: item.label,
          color: item.color,
          percentage: item.percentage,
        }));
      }
      initOrUpdateChart(json.data.labels, json.data.series, json.data.colors);
    }
  } catch (err) {
    console.warn('Activity breakdown fetch error:', err);
  }
};

onMounted(() => {
  fetchData();
  // Live auto-refresh every 3.5 seconds
  pollTimer = setInterval(fetchData, 3500);
});

onBeforeUnmount(() => {
  if (pollTimer) clearInterval(pollTimer);
  if (chartInstance) chartInstance.destroy();
});
</script>
