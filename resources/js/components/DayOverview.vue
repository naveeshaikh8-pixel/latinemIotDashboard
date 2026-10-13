<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <!-- Card Header -->
    <div class="px-5 py-3.5 flex items-center justify-between border-b border-gray-100/60">
      <div class="flex items-center gap-2.5">
        <!-- Calendar Icon -->
        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
        </svg>
        <h2 class="text-[15px] font-semibold text-gray-700 tracking-tight">Day overview</h2>
        <!-- Current Day Badge -->
        <span class="text-xs text-gray-600 font-medium bg-gray-100 px-2.5 py-0.5 rounded border border-gray-200">
          {{ currentDayDate }}
        </span>
      </div>

      <div class="flex items-center gap-3">
        <!-- Live Current Time Indicator -->
        <div class="flex items-center gap-1.5 text-xs text-gray-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full select-none">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Current Time: <strong class="text-gray-900 font-semibold">{{ currentTimeFormatted }}</strong></span>
        </div>

        <!-- Filter Dropdown Arrow / Funnel Button -->
        <button class="text-gray-300 hover:text-gray-500 transition-colors p-1" title="Filter timeline">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Timeline Grid Wrapper -->
    <div class="overflow-x-auto">
      <div class="min-w-[960px]">
        <!-- Table Header Row -->
        <div class="flex items-center border-b border-gray-200">
          <!-- Assets Column Title -->
          <div class="w-[240px] shrink-0 py-2.5 px-6 text-center border-r border-gray-200">
            <span class="text-[13px] font-medium text-gray-500">Assets ({{ assets.length }})</span>
          </div>

          <!-- Time Markers -->
          <div class="flex-1 relative h-9">
            <div
              v-for="hour in hours"
              :key="hour.time"
              class="absolute -translate-x-1/2 top-2 text-[12px] font-medium text-gray-500 select-none"
              :style="{ left: `${hour.percent}%` }"
            >
              {{ hour.time }}
            </div>

            <!-- "NOW" Current Time Marker Pill -->
            <div
              v-if="nowPercent >= 0 && nowPercent <= 100"
              class="absolute top-1.5 -translate-x-1/2 z-20 pointer-events-none"
              :style="{ left: `${nowPercent}%` }"
            >
              <span class="bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-xs whitespace-nowrap">
                NOW ({{ currentTimeFormatted }})
              </span>
            </div>
          </div>
        </div>

        <!-- Asset Rows -->
        <div
          v-for="asset in assets"
          :key="asset.id"
          class="flex items-center relative border-b border-gray-100 last:border-b-0 hover:bg-gray-50/40 transition-colors"
        >
          <!-- Left Asset Card -->
          <div class="w-[240px] shrink-0 p-4 border-r border-gray-200 bg-white">
            <div class="flex items-center gap-3">
              <!-- Vehicle Illustration -->
              <VehicleIcon :type="asset.id" />

              <!-- Vehicle Info -->
              <div class="min-w-0">
                <div class="text-[14px] font-semibold text-[#D96B27]">
                  {{ asset.id }}
                </div>
                <!-- Percentage Metrics Indicators -->
                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                  <div
                    v-for="(metric, mIdx) in asset.metrics"
                    :key="mIdx"
                    class="flex items-center gap-1"
                  >
                    <span
                      class="w-2.5 h-2.5 inline-block shrink-0 rounded-[1px]"
                      :style="{ backgroundColor: metric.color }"
                    ></span>
                    <span class="text-[11px] text-gray-500 font-normal leading-none">
                      {{ metric.value }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Timeline Gantt Track -->
          <div class="flex-1 relative h-20 overflow-hidden">
            <!-- Vertical Hour Guide Lines -->
            <div
              v-for="hour in hours"
              :key="'grid-' + hour.time"
              class="absolute top-0 bottom-0 border-l border-gray-100/90 pointer-events-none"
              :style="{ left: `${hour.percent}%` }"
            ></div>

            <!-- "NOW" Current Time Vertical Guideline -->
            <div
              v-if="nowPercent >= 0 && nowPercent <= 100"
              class="absolute top-0 bottom-0 border-l-2 border-dashed border-rose-400 z-10 pointer-events-none"
              :style="{ left: `${nowPercent}%` }"
            ></div>

            <!-- Timeline Activity Blocks -->
            <div class="absolute inset-y-0 left-0 right-0 flex items-center">
              <div
                v-for="(block, bIdx) in asset.blocks"
                :key="bIdx"
                class="absolute flex h-7 rounded-[2px] shadow-[0_2px_4px_rgba(0,0,0,0.18)] overflow-hidden cursor-pointer hover:brightness-105 transition-all"
                :style="{
                  left: `${block.leftPercent}%`,
                  width: `${block.widthPercent}%`,
                }"
              >
                <div
                  v-for="(segment, sIdx) in block.segments"
                  :key="sIdx"
                  class="h-full border-r last:border-r-0 border-black/10 transition-transform relative group"
                  :style="{
                    width: `${segment.percent}%`,
                    backgroundColor: segment.color,
                  }"
                  :title="`${asset.id} [${segment.label}]: ${segment.start} - ${segment.end}`"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import VehicleIcon from './VehicleIcon.vue';

const timelineStartMins = 10 * 60 + 30; // 630
const totalMins = 8 * 60; // 480

const currentDayDate = ref(new Date().toLocaleDateString('en-US', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' }));
const currentTimeFormatted = ref(new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
const nowPercent = ref(0);

const updateNow = () => {
  const now = new Date();
  currentTimeFormatted.value = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  const mins = now.getHours() * 60 + now.getMinutes();
  nowPercent.value = Math.max(0, Math.min(100, ((mins - timelineStartMins) / totalMins) * 100));
};

const hours = ref([
  { time: '11:00', percent: ((11 * 60 - timelineStartMins) / totalMins) * 100 },
  { time: '12:00', percent: ((12 * 60 - timelineStartMins) / totalMins) * 100 },
  { time: '13:00', percent: ((13 * 60 - timelineStartMins) / totalMins) * 100 },
  { time: '14:00', percent: ((14 * 60 - timelineStartMins) / totalMins) * 100 },
  { time: '15:00', percent: ((15 * 60 - timelineStartMins) / totalMins) * 100 },
  { time: '16:00', percent: ((16 * 60 - timelineStartMins) / totalMins) * 100 },
  { time: '17:00', percent: ((17 * 60 - timelineStartMins) / totalMins) * 100 },
  { time: '18:00', percent: ((18 * 60 - timelineStartMins) / totalMins) * 100 },
]);

const COLOR_ORANGE = '#D96B27';
const COLOR_TEAL = '#00A896';
const COLOR_OLIVE = '#9E8A38';
const COLOR_LIGHT_BLUE = '#D9E9F2';

const assets = ref([
  {
    id: 'BL-08',
    metrics: [
      { color: COLOR_LIGHT_BLUE, value: '52.2%' },
      { color: COLOR_ORANGE, value: '29.4%' },
      { color: COLOR_TEAL, value: '51.1%' },
      { color: COLOR_OLIVE, value: '0.1%' },
    ],
    blocks: [
      {
        leftPercent: 1.04,
        widthPercent: 19.38,
        segments: [
          { label: 'Idling', color: COLOR_ORANGE, percent: 63.44, start: '10:35', end: '12:08' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 21.51, start: '10:35', end: '12:08' },
          { label: 'Idling', color: COLOR_ORANGE, percent: 15.05, start: '10:35', end: '12:08' },
        ],
      },
      {
        leftPercent: 22.92,
        widthPercent: 37.5,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 100, start: '12:20', end: '15:20' },
        ],
      },
      {
        leftPercent: 61.46,
        widthPercent: 12.5,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 30, start: '15:25', end: '16:25' },
          { label: 'Idling', color: COLOR_ORANGE, percent: 33.33, start: '15:25', end: '16:25' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 36.67, start: '15:25', end: '16:25' },
        ],
      },
      {
        leftPercent: 79.17,
        widthPercent: 3.75,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 100, start: '16:50', end: '17:08' },
        ],
      },
    ],
  },
  {
    id: 'Q-47898',
    metrics: [
      { color: COLOR_LIGHT_BLUE, value: '29.8%' },
      { color: COLOR_TEAL, value: '23.9%' },
      { color: COLOR_OLIVE, value: '0.2%' },
    ],
    blocks: [
      {
        leftPercent: 1.04,
        widthPercent: 26.04,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 96, start: '10:35', end: '12:40' },
          { label: 'Auxiliary', color: COLOR_OLIVE, percent: 4, start: '10:35', end: '12:40' },
        ],
      },
      {
        leftPercent: 36.46,
        widthPercent: 36.46,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 65.71, start: '13:25', end: '16:20' },
          { label: 'Auxiliary', color: COLOR_OLIVE, percent: 2.86, start: '13:25', end: '16:20' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 28.57, start: '13:25', end: '16:20' },
          { label: 'Auxiliary', color: COLOR_OLIVE, percent: 2.86, start: '13:25', end: '16:20' },
        ],
      },
      {
        leftPercent: 89.58,
        widthPercent: 3.75,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 100, start: '17:40', end: '17:58' },
        ],
      },
    ],
  },
  {
    id: 'D-26653',
    metrics: [
      { color: COLOR_LIGHT_BLUE, value: '36.7%' },
      { color: COLOR_ORANGE, value: '19.4%' },
      { color: COLOR_TEAL, value: '30.6%' },
    ],
    blocks: [
      {
        leftPercent: 1.04,
        widthPercent: 35.21,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 11.83, start: '10:35', end: '13:24' },
          { label: 'Idling', color: COLOR_ORANGE, percent: 59.17, start: '10:35', end: '13:24' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 2.37, start: '10:35', end: '13:24' },
          { label: 'Idling', color: COLOR_ORANGE, percent: 23.67, start: '10:35', end: '13:24' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 2.96, start: '10:35', end: '13:24' },
        ],
      },
      {
        leftPercent: 38.54,
        widthPercent: 32.71,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 9.55, start: '13:35', end: '16:12' },
          { label: 'Idling', color: COLOR_ORANGE, percent: 15.92, start: '13:35', end: '16:12' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 15.92, start: '13:35', end: '16:12' },
          { label: 'Idling', color: COLOR_ORANGE, percent: 6.37, start: '13:35', end: '16:12' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 35.03, start: '13:35', end: '16:12' },
          { label: 'Idling', color: COLOR_ORANGE, percent: 10.83, start: '13:35', end: '16:12' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 6.37, start: '13:35', end: '16:12' },
        ],
      },
      {
        leftPercent: 72.92,
        widthPercent: 14.17,
        segments: [
          { label: 'Ignition On', color: COLOR_TEAL, percent: 22.06, start: '16:20', end: '17:28' },
          { label: 'Idling', color: COLOR_ORANGE, percent: 29.41, start: '16:20', end: '17:28' },
          { label: 'Ignition On', color: COLOR_TEAL, percent: 48.53, start: '16:20', end: '17:28' },
        ],
      },
    ],
  },
]);

let pollTimer = null;
let nowTimer = null;

const fetchTimeline = async () => {
  try {
    const res = await fetch('/api/dashboard/timeline');
    if (!res.ok) throw new Error('Network error');
    const json = await res.json();
    if (json.success && json.data) {
      if (json.data.formattedDate) {
        currentDayDate.value = json.data.formattedDate;
      }
      if (json.data.hours && json.data.hours.length) {
        hours.value = json.data.hours;
      }
      if (json.data.assets && json.data.assets.length) {
        assets.value = json.data.assets;
      }
    }
  } catch (err) {
    console.warn('Timeline fetch error:', err);
  }
};

onMounted(() => {
  updateNow();
  nowTimer = setInterval(updateNow, 10000);
  fetchTimeline();
  // Live auto-refresh every 3.5 seconds
  pollTimer = setInterval(fetchTimeline, 3500);
});

onBeforeUnmount(() => {
  if (pollTimer) clearInterval(pollTimer);
  if (nowTimer) clearInterval(nowTimer);
});
</script>
