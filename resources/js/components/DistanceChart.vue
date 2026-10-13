<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex flex-col justify-between">
    <!-- Header -->
    <div class="flex items-center justify-between mb-2">
      <div class="flex items-center gap-2">
        <!-- Route / Distance Icon -->
        <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="currentColor">
          <circle cx="6" cy="18" r="3" />
          <circle cx="18" cy="6" r="3" />
          <path d="M8.7 16.5 C 14 16, 10 8, 15.3 7.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
        </svg>
        <h3 class="text-[15px] font-semibold text-gray-700">Traveled distance ({{ Math.round(totalDistance) }}km)</h3>
      </div>
      <span class="bg-[#27AE60] text-white text-[11px] font-bold px-2 py-0.5 rounded tracking-wide">
        + {{ changePercent }}%
      </span>
    </div>

    <!-- Chart Container -->
    <div class="relative w-full h-[220px]">
      <canvas ref="canvasRef"></canvas>

      <!-- Pinned Reference Tooltip from API / Screenshot -->
      <div
        v-if="tooltipData"
        class="absolute left-[10%] top-[4%] bg-white rounded-md shadow-[0_4px_12px_rgba(0,0,0,0.12)] border border-gray-100 px-3 py-2 pointer-events-none z-10 transition-opacity"
        :class="{ 'opacity-0': isHovering }"
      >
        <div class="text-[11px] text-gray-600 font-medium pb-1 mb-1 border-b border-gray-100">{{ tooltipData.date }}</div>
        <div class="flex items-center gap-1.5 text-[11px] text-gray-600 leading-tight">
          <span class="w-2 h-2 rounded-full bg-[#B85D19]"></span>
          <span>Current Period: <strong class="text-gray-900 font-bold">{{ tooltipData.currentPeriodKm }} Km</strong></span>
        </div>
        <div class="flex items-center gap-1.5 text-[11px] text-gray-600 leading-tight mt-1">
          <span class="w-2 h-2 rounded-full border border-[#D5A77E] bg-white"></span>
          <span>Previous Period: <strong class="text-gray-900 font-bold">{{ tooltipData.previousPeriodKm }} Km</strong></span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {
  Chart as ChartJS,
  LineController,
  LineElement,
  PointElement,
  LinearScale,
  CategoryScale,
  Filler,
  Tooltip,
} from 'chart.js';

ChartJS.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Filler, Tooltip);

const canvasRef = ref(null);
const isHovering = ref(false);
const totalDistance = ref(431);
const changePercent = ref(45);
const tooltipData = ref({
  date: 'Sunday, 23 March',
  currentPeriodKm: 29,
  previousPeriodKm: 6,
});

let chartInstance = null;
let pollTimer = null;

const initOrUpdateChart = (labels, currentPeriod, previousPeriod) => {
  if (!canvasRef.value) return;

  if (chartInstance) {
    chartInstance.data.labels = labels;
    chartInstance.data.datasets[0].data = currentPeriod;
    chartInstance.data.datasets[1].data = previousPeriod;
    chartInstance.update();
    return;
  }

  const ctx = canvasRef.value.getContext('2d');
  const gradient = ctx.createLinearGradient(0, 0, 0, 200);
  gradient.addColorStop(0, 'rgba(200, 104, 26, 0.55)');
  gradient.addColorStop(1, 'rgba(215, 140, 70, 0.40)');

  chartInstance = new ChartJS(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Current Period',
          data: currentPeriod,
          borderColor: '#B85D19',
          borderWidth: 2.5,
          backgroundColor: gradient,
          fill: true,
          tension: 0.35,
          pointRadius: 4.5,
          pointBackgroundColor: '#B85D19',
          pointBorderColor: '#B85D19',
          pointHoverRadius: 6,
          order: 2,
        },
        {
          label: 'Previous Period',
          data: previousPeriod,
          borderColor: '#D7AC81',
          borderWidth: 2,
          borderDash: [5, 4],
          backgroundColor: 'transparent',
          fill: false,
          tension: 0.35,
          pointStyle: 'rectRot',
          pointRadius: 4,
          pointBackgroundColor: '#FFFFFF',
          pointBorderColor: '#D7AC81',
          pointBorderWidth: 1.5,
          pointHoverRadius: 6,
          order: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          enabled: true,
          backgroundColor: '#FFFFFF',
          titleColor: '#374151',
          bodyColor: '#4B5563',
          borderColor: '#E5E7EB',
          borderWidth: 1,
          padding: 8,
          boxPadding: 4,
          usePointStyle: true,
          titleFont: { size: 11, weight: '500' },
          bodyFont: { size: 11 },
          callbacks: {
            label: (context) => ` ${context.dataset.label}: ${context.parsed.y} Km`,
          },
        },
      },
      onHover: (event, elements) => {
        isHovering.value = elements && elements.length > 0;
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            color: '#718096',
            font: { size: 11 },
            maxRotation: 45,
            minRotation: 45,
            autoSkip: false,
          },
        },
        y: {
          beginAtZero: true,
          suggestedMax: 45,
          grid: {
            color: '#E2E8F0',
            borderDash: [4, 4],
            drawBorder: false,
          },
          ticks: {
            color: '#718096',
            font: { size: 11 },
            callback: (val) => `${val}km`,
          },
        },
      },
    },
  });
};

const fetchData = async () => {
  try {
    const res = await fetch('/api/dashboard/travelled-distance');
    if (!res.ok) throw new Error('Network error');
    const json = await res.json();
    if (json.success && json.data) {
      totalDistance.value = json.data.totalDistance ?? 431;
      changePercent.value = json.data.changePercent ?? 45;
      if (json.data.tooltip) {
        tooltipData.value = json.data.tooltip;
      }
      initOrUpdateChart(json.data.labels, json.data.currentPeriod, json.data.previousPeriod);
      return;
    }
  } catch (err) {
    console.warn('Distance chart fetch error:', err);
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
