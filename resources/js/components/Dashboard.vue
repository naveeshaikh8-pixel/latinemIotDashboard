<template>
  <main class="min-h-screen bg-[#F0F2F5] p-3 sm:p-5 lg:p-6">
    <div class="max-w-[1400px] mx-auto space-y-4 sm:space-y-5">
      <!-- Header Section: Dashboard Title & Live Sync Controls -->
      <header class="bg-white rounded-lg shadow-sm border border-gray-100 px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-[#D96B27]/10 flex items-center justify-center text-[#D96B27] shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h1 class="text-lg sm:text-xl font-bold text-gray-800 tracking-tight leading-tight">
                IoT Dashboard
              </h1>
              <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                Live Active
              </span>
            </div>
            <p class="text-xs text-gray-500">
              Real-time IoT fleet monitoring & telemetry analytics
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
          <!-- Live Telemetry Status Badge -->
          <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-200 select-none">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Live Sync (3s)</span>
          </div>

          <!-- Total Records In Database Badge -->
          <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200 select-none">
            <span class="text-blue-400">Total DB Records:</span>
            <span class="font-bold text-blue-900">{{ summary.totalRecords }}</span>
          </div>

          <!-- Total Active Vehicles Badge -->
          <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-50 text-gray-600 text-xs font-medium border border-gray-200 select-none">
            <span class="text-gray-400">Fleet:</span>
            <span class="font-semibold text-gray-800">{{ summary.activeVehiclesCount }} Online</span>
          </div>

          <!-- Live Simulator Link (Opens in new tab) -->
          <a
            href="/iot"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-teal-50 text-teal-700 hover:bg-teal-100 text-xs font-medium border border-teal-200 transition-colors shadow-2xs group"
            title="Open IoT Live Data Simulator in a new tab"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span class="font-bold">Simulator</span>
            <svg class="w-3 h-3 text-teal-500 opacity-70 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
          </a>

          <!-- Manual Refresh Button -->
          <button
            @click="fetchAll"
            class="p-1.5 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 border border-gray-200 transition-all cursor-pointer"
            title="Refresh dashboard data now"
          >
            <svg class="w-4 h-4" :class="{ 'animate-spin': isRefreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </header>

      <!-- Live Vehicles Telemetry Summary Cards (Hidden) -->
      <section v-if="showVehiclesBar" aria-label="Live Vehicles Status" class="hidden grid grid-cols-1 md:grid-cols-3 gap-3">
        <div
          v-for="v in liveVehicles"
          :key="v.vehicleId"
          class="bg-white rounded-lg shadow-sm border border-gray-100 px-4 py-3 flex items-center justify-between hover:border-gray-200 transition-colors"
        >
          <div class="flex items-center gap-2.5 min-w-0">
            <span
              class="w-2.5 h-2.5 rounded-full shrink-0"
              :class="v.ignitionStatus ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400'"
            ></span>
            <div class="truncate">
              <div class="font-bold text-sm text-[#D96B27] leading-tight flex items-center gap-1.5">
                <span>{{ v.vehicleId }}</span>
                <span v-if="v.timeAgo" class="text-[10px] font-normal text-gray-400">({{ v.timeAgo }})</span>
              </div>
              <div class="text-[11px] text-gray-400 truncate">{{ v.vehicleName }}</div>
            </div>
          </div>

          <div class="text-right shrink-0 pl-2">
            <div class="text-xs font-bold text-gray-800">
              {{ v.speed > 0 ? `${v.speed} km/h` : '0 km/h' }}
            </div>
            <div
              class="text-[10px] uppercase font-bold tracking-wider"
              :class="v.activityType === 'ignition_on' ? 'text-emerald-600' : 'text-amber-600'"
            >
              {{ v.activityType ? v.activityType.replace('_', ' ') : 'STANDBY' }}
            </div>
          </div>
        </div>
      </section>

      <!-- Top Section: 2x2 Metric Cards Grid -->
      <section aria-label="Telemetry Metrics" class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">
        <!-- Traveled Distance -->
        <DistanceChart />

        <!-- Engine Hours -->
        <EngineHoursChart />

        <!-- Activity Breakdown -->
        <ActivityBreakdownChart />

        <!-- Messages Received -->
        <MessagesChart />
      </section>

      <!-- Bottom Section: Day Overview Activity Timeline -->
      <section aria-label="Day Overview">
        <DayOverview />
      </section>

      <!-- Latest IoT Telemetry Records (Shown at the bottom) -->
      <section v-if="showLatestRecords" aria-label="Live Telemetry Stream" class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 flex items-center justify-between border-b border-gray-100 bg-gray-50/50">
          <div class="flex items-center gap-2.5">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></div>
            <h2 class="text-[14px] font-semibold text-gray-800 tracking-tight">
              Latest IoT Telemetry Records
            </h2>
            <span class="text-xs text-gray-400">
              (Live feed from <code class="text-xs font-mono bg-gray-100 px-1 py-0.5 rounded text-gray-600">vehicleTrackingData</code>)
            </span>
          </div>

          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Auto-updating every 3s</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80 text-gray-500 font-medium">
                <th class="py-2.5 px-4">Record ID</th>
                <th class="py-2.5 px-4">Vehicle</th>
                <th class="py-2.5 px-4">Activity</th>
                <th class="py-2.5 px-4">Speed</th>
                <th class="py-2.5 px-4">Distance</th>
                <th class="py-2.5 px-4">Engine Hours</th>
                <th class="py-2.5 px-4">GPS Location</th>
                <th class="py-2.5 px-4 text-right">Recorded At</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr
                v-for="(rec, idx) in latestFeed"
                :key="rec.id"
                class="hover:bg-amber-50/30 transition-colors"
                :class="idx === 0 ? 'bg-emerald-50/20' : ''"
              >
                <td class="py-2.5 px-4 font-mono font-bold text-gray-500">#{{ rec.id }}</td>
                <td class="py-2.5 px-4">
                  <div class="font-bold text-[#D96B27]">{{ rec.vehicleId }}</div>
                  <div class="text-[10px] text-gray-400">{{ rec.registrationNumber }}</div>
                </td>
                <td class="py-2.5 px-4">
                  <span
                    class="px-2 py-0.5 rounded text-[11px] font-semibold"
                    :class="rec.activityType === 'ignition_on' ? 'bg-emerald-100 text-emerald-800' : (rec.activityType === 'idling' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700')"
                  >
                    {{ rec.activityType ? rec.activityType.replace('_', ' ') : 'N/A' }}
                  </span>
                </td>
                <td class="py-2.5 px-4 font-medium">{{ rec.speed }} km/h</td>
                <td class="py-2.5 px-4 font-medium">{{ rec.travelledDistance }} km</td>
                <td class="py-2.5 px-4 font-medium">{{ rec.engineHours }} h</td>
                <td class="py-2.5 px-4 font-mono text-[11px] text-gray-500">{{ rec.latitude }}, {{ rec.longitude }}</td>
                <td class="py-2.5 px-4 text-right">
                  <div class="font-medium text-gray-800">{{ rec.timeFormatted || rec.recordedAt }}</div>
                  <div class="text-[10px] text-gray-400">{{ rec.timeAgo }}</div>
                </td>
              </tr>
              <tr v-if="!latestFeed.length">
                <td colspan="8" class="text-center py-6 text-gray-400">Loading live telemetry records...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import DayOverview from './DayOverview.vue';
import DistanceChart from './DistanceChart.vue';
import EngineHoursChart from './EngineHoursChart.vue';
import ActivityBreakdownChart from './ActivityBreakdownChart.vue';
import MessagesChart from './MessagesChart.vue';

const summary = ref({
  totalDistance: 431,
  totalEngineHours: 1246,
  totalMessages: 46554,
  totalRecords: 270,
  activeVehiclesCount: 3,
  totalVehiclesCount: 3,
  lastUpdatedTime: '',
});

const liveVehicles = ref([]);
const showVehiclesBar = ref(false); // Set to false to keep top vehicles tab hidden
const showLatestRecords = ref(true); // Shown at the bottom
const latestFeed = ref([]);
const isRefreshing = ref(false);
let pollTimer = null;

const currentDate = new Date().toLocaleDateString('en-US', {
  weekday: 'short',
  day: 'numeric',
  month: 'short',
  year: 'numeric',
});

const fetchSummary = async () => {
  try {
    const res = await fetch('/api/dashboard/summary');
    if (!res.ok) throw new Error('Network error');
    const json = await res.json();
    if (json.success && json.data) {
      summary.value = json.data;
    }
  } catch (err) {
    console.warn('Dashboard summary error:', err);
  }
};

const fetchVehicles = async () => {
  try {
    const res = await fetch('/api/vehicles');
    if (!res.ok) throw new Error('Network error');
    const json = await res.json();
    if (json.success && json.data) {
      liveVehicles.value = json.data;
    }
  } catch (err) {
    console.warn('Live vehicles error:', err);
  }
};

const fetchLatestFeed = async () => {
  if (!showLatestRecords.value) return;
  try {
    const res = await fetch('/api/dashboard/latest-feed?limit=6');
    if (!res.ok) throw new Error('Network error');
    const json = await res.json();
    if (json.success && json.data) {
      latestFeed.value = json.data;
    }
  } catch (err) {
    console.warn('Latest feed error:', err);
  }
};

const fetchAll = async () => {
  isRefreshing.value = true;
  await Promise.all([fetchSummary(), fetchVehicles(), fetchLatestFeed()]);
  setTimeout(() => {
    isRefreshing.value = false;
  }, 400);
};

onMounted(() => {
  fetchAll();
  // Auto-refresh every 3.5 seconds so live simulator updates are immediately visible!
  pollTimer = setInterval(fetchAll, 3500);
});

onBeforeUnmount(() => {
  if (pollTimer) clearInterval(pollTimer);
});
</script>
