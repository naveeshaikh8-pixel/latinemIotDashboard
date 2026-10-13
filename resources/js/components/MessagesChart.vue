<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex flex-col justify-between">
    <!-- Header -->
    <div class="flex items-center gap-2 mb-2">
      <!-- Message / Chat Bubble Icon -->
      <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="currentColor">
        <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM9 11C8.45 11 8 10.55 8 10C8 9.45 8.45 9 9 9C9.55 9 10 9.45 10 10C10 10.55 9.55 11 9 11ZM12 11C11.45 11 11 10.55 11 10C11 9.45 11.45 9 12 9C12.55 9 13 9.45 13 10C13 10.55 12.55 11 12 11ZM15 11C14.45 11 14 10.55 14 10C14 9.45 14.45 9 15 9C15.55 9 16 9.45 16 10C16 10.55 15.55 11 15 11Z" />
      </svg>
        <h3 class="text-[15px] font-semibold text-gray-700">Messages received <span v-if="totalMessages" class="text-xs text-gray-400 font-normal">({{ totalMessages.toLocaleString() }})</span></h3>
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
const totalMessages = ref(0);
let chartInstance = null;
let pollTimer = null;

const initOrUpdateChart = (labels, base, messages) => {
  if (!canvasRef.value) return;

  if (chartInstance) {
    chartInstance.data.labels = labels;
    chartInstance.data.datasets[0].data = base;
    chartInstance.data.datasets[1].data = messages;
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
          label: 'Base',
          data: base,
          backgroundColor: '#B58E71',
          stack: 'messagesStack',
          barPercentage: 0.65,
          categoryPercentage: 0.9,
          borderRadius: { topLeft: 0, topRight: 0, bottomLeft: 2, bottomRight: 2 },
        },
        {
          label: 'Messages',
          data: messages,
          backgroundColor: '#C8681A',
          stack: 'messagesStack',
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
          usePointStyle: true,
          callbacks: {
            title: (items) => (items[0] ? items[0].label : ''),
            label: (context) => {
              if (context.datasetIndex === 0) return null;
              const total = context.parsed.y + (context.chart.data.datasets[0].data[context.dataIndex] || 0);
              return ` Total Messages: ${total.toLocaleString()}`;
            },
          },
          filter: (tooltipItem) => tooltipItem.datasetIndex === 1,
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
          suggestedMax: 6500,
          grid: {
            color: '#E2E8F0',
            borderDash: [4, 4],
            drawBorder: false,
          },
          ticks: {
            color: '#718096',
            font: { size: 11 },
            callback: (val) => `${val >= 1000 ? (val / 1000).toFixed(1) + 'k' : val}`,
          },
        },
      },
    },
  });
};

const fetchData = async () => {
  try {
    const res = await fetch('/api/dashboard/messages-received');
    if (!res.ok) throw new Error('Network error');
    const json = await res.json();
    if (json.success && json.data) {
      if (json.data.totalMessages) {
        totalMessages.value = json.data.totalMessages;
      }
      initOrUpdateChart(json.data.labels, json.data.base, json.data.messages);
    }
  } catch (err) {
    console.warn('Messages received fetch error:', err);
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
