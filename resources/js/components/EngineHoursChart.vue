<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex flex-col justify-between">
    <!-- Header -->
    <div class="flex items-center gap-2 mb-2">
      <!-- Engine Icon -->
      <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="currentColor">
        <path d="M7 4V2H17V4H20V7H22V15H20V18H17V20H15V22H9V20H7V18H4V15H2V7H4V4H7ZM6 8V14H8V16H16V14H18V8H16V6H8V8H6Z" />
      </svg>
      <h3 class="text-[15px] font-semibold text-gray-700">Engine hours</h3>
    </div>

    <!-- Chart Container -->
    <div class="relative w-full h-[220px]">
      <canvas ref="canvasRef"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {
  Chart as ChartJS,
  BarController,
  BarElement,
  LinearScale,
  CategoryScale,
  Tooltip,
} from 'chart.js';

ChartJS.register(BarController, BarElement, LinearScale, CategoryScale, Tooltip);

const canvasRef = ref(null);
let chartInstance = null;
let pollTimer = null;

const initOrUpdateChart = (labels, ignitionHours, idlingHours) => {
  if (!canvasRef.value) return;

  if (chartInstance) {
    chartInstance.data.labels = labels;
    chartInstance.data.datasets[0].data = ignitionHours;
    chartInstance.data.datasets[1].data = idlingHours;
    chartInstance.update();
    return;
  }

  const ctx = canvasRef.value.getContext('2d');

  chartInstance = new ChartJS(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Ignition',
          data: ignitionHours,
          backgroundColor: '#0D8A4C',
          stack: 'stack1',
          barPercentage: 0.65,
          categoryPercentage: 0.9,
          borderRadius: { topLeft: 0, topRight: 0, bottomLeft: 2, bottomRight: 2 },
        },
        {
          label: 'Idling',
          data: idlingHours,
          backgroundColor: '#E07D1E',
          stack: 'stack1',
          barPercentage: 0.65,
          categoryPercentage: 0.9,
          borderRadius: { topLeft: 3, topRight: 3, bottomLeft: 0, bottomRight: 0 },
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
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
            label: (context) => ` ${context.dataset.label}: ${context.parsed.y} hrs`,
          },
        },
      },
      scales: {
        x: {
          stacked: true,
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
          stacked: true,
          beginAtZero: true,
          suggestedMax: 40,
          grid: {
            color: '#E2E8F0',
            borderDash: [4, 4],
            drawBorder: false,
          },
          ticks: {
            color: '#718096',
            font: { size: 11 },
            callback: (val) => `${val}h`,
          },
        },
      },
    },
  });
};

const fetchData = async () => {
  try {
    const res = await fetch('/api/dashboard/engine-hours');
    if (!res.ok) throw new Error('Network error');
    const json = await res.json();
    if (json.success && json.data) {
      initOrUpdateChart(json.data.labels, json.data.ignitionHours, json.data.idlingHours);
    }
  } catch (err) {
    console.warn('Engine hours fetch error:', err);
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
