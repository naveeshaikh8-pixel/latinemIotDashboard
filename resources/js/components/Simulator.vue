<template>
  <main class="min-h-screen bg-[#F0F2F5] p-3 sm:p-5 lg:p-6">
    <div class="max-w-[1100px] mx-auto space-y-5">
      <!-- Top Navigation & Header -->
      <header class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-[#00A896]/10 flex items-center justify-center text-[#00A896] shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-800 tracking-tight">IoT Data Simulator</h1>
              <p class="text-xs text-gray-500">Live background telemetry stream into MySQL</p>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <a
            href="/"
            class="text-xs font-medium text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 border border-gray-200 px-3 py-2 rounded-md transition-colors flex items-center gap-1.5"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
          </a>
        </div>
      </header>

      <!-- Controls & Status Card -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 pb-6 border-b border-gray-100">
          <div>
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Simulator Status</div>
            <div class="flex items-center gap-2">
              <span
                class="w-3 h-3 rounded-full"
                :class="isRunning ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400'"
              ></span>
              <span
                class="text-lg font-bold"
                :class="isRunning ? 'text-emerald-700' : 'text-gray-600'"
              >
                {{ isRunning ? 'Running' : 'Stopped' }}
              </span>
              <span class="text-xs text-gray-400 ml-2">
                ({{ isRunning ? 'Simulating 1 record every 5s' : 'Idle' }})
              </span>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex items-center gap-3">
            <button
              @click="startSimulator"
              :disabled="isRunning"
              class="px-5 py-2.5 rounded-md font-semibold text-sm transition-all flex items-center gap-2 shadow-sm"
              :class="isRunning
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                : 'bg-[#00875A] hover:bg-[#00704A] text-white cursor-pointer active:scale-95'"
            >
              <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                <path d="M4 4l12 6-12 6V4z" />
              </svg>
              Start Simulator
            </button>

            <button
              @click="stopSimulator"
              :disabled="!isRunning"
              class="px-5 py-2.5 rounded-md font-semibold text-sm transition-all flex items-center gap-2 shadow-sm"
              :class="!isRunning
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'
                : 'bg-rose-600 hover:bg-rose-700 text-white cursor-pointer active:scale-95'"
            >
              <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 5h10v10H5V5z" clip-rule="evenodd" />
              </svg>
              Stop Simulator
            </button>
          </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
          <div class="bg-gray-50/70 border border-gray-100 rounded-lg p-4">
            <div class="text-xs font-medium text-gray-500">Total Inserted (Session)</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ totalInserted }}</div>
            <div class="text-[11px] text-gray-400 mt-0.5">Records confirmed by API</div>
          </div>

          <div class="bg-gray-50/70 border border-gray-100 rounded-lg p-4">
            <div class="text-xs font-medium text-gray-500">Last Insertion Time</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">
              {{ lastInsertionTime || '--:--:--' }}
            </div>
            <div class="text-[11px] text-gray-400 mt-0.5">Server timestamp</div>
          </div>

          <div class="bg-gray-50/70 border border-gray-100 rounded-lg p-4">
            <div class="text-xs font-medium text-gray-500">Insertion Rate</div>
            <div class="text-2xl font-bold text-[#D96B27] mt-1">1 record / 5s</div>
            <div class="text-[11px] text-gray-400 mt-0.5">Table: vehicleTrackingData</div>
          </div>
        </div>
      </div>

      <!-- Latest Insertion Result -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
          <span>Latest Insertion Result</span>
          <span v-if="isRequesting" class="text-xs font-normal text-amber-600 animate-pulse">
            • Transmitting...
          </span>
        </h3>

        <!-- Status Message Banner -->
        <div
          v-if="latestMessage"
          class="p-3.5 rounded-md text-xs font-medium mb-4 flex items-center justify-between"
          :class="isError ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200'"
        >
          <span>{{ latestMessage }}</span>
          <span class="text-gray-400 text-[11px]">{{ lastInsertionTime }}</span>
        </div>

        <div v-else class="text-xs text-gray-400 italic py-6 text-center border border-dashed border-gray-200 rounded-md">
          Simulator is idle. Click "Start Simulator" to begin streaming live telemetry.
        </div>

        <!-- Latest Inserted Records Cards -->
        <div v-if="latestRecords.length" class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
          <div
            v-for="(rec, idx) in latestRecords"
            :key="idx"
            class="border border-gray-200 rounded-md p-3.5 bg-gray-50/40 hover:bg-white transition-colors"
          >
            <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-gray-100">
              <span class="font-bold text-sm text-[#D96B27]">{{ rec.vehicleId }}</span>
              <span
                class="text-[11px] px-2 py-0.5 rounded font-medium"
                :class="rec.activityType === 'ignition_on' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
              >
                {{ rec.activityType }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
              <div>
                <span class="text-gray-400">Speed:</span>
                <strong class="ml-1 text-gray-800">{{ rec.speed }} km/h</strong>
              </div>
              <div>
                <span class="text-gray-400">Dist:</span>
                <strong class="ml-1 text-gray-800">{{ rec.travelledDistance }} km</strong>
              </div>
              <div>
                <span class="text-gray-400">Lat:</span>
                <span class="ml-1 font-mono text-[11px] text-gray-700">{{ rec.latitude }}</span>
              </div>
              <div>
                <span class="text-gray-400">Lng:</span>
                <span class="ml-1 font-mono text-[11px] text-gray-700">{{ rec.longitude }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onBeforeUnmount } from 'vue';

const isRunning = ref(false);
const totalInserted = ref(0);
const lastInsertionTime = ref('');
const latestMessage = ref('');
const latestRecords = ref([]);
const isRequesting = ref(false);
const isError = ref(false);

let timerId = null;

const performSimulation = async () => {
  // Prevent overlapping in-flight requests
  if (isRequesting.value) return;

  isRequesting.value = true;
  try {
    const res = await fetch('/api/iot/simulate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    const data = await res.json();

    if (res.ok && data.success) {
      isError.value = false;
      totalInserted.value += data.insertedCount ?? 1;
      lastInsertionTime.value = new Date().toLocaleTimeString();
      latestMessage.value = `${data.message} (${data.insertedCount} record inserted)`;
      latestRecords.value = data.records || [];
    } else {
      isError.value = true;
      latestMessage.value = data.message || 'Error executing simulation.';
    }
  } catch (err) {
    isError.value = true;
    latestMessage.value = 'Network failure: unable to reach simulation API.';
    console.error('Simulation error:', err);
  } finally {
    isRequesting.value = false;
  }
};

const startSimulator = () => {
  // Prevent multiple timers if already running
  if (isRunning.value) return;

  isRunning.value = true;
  latestMessage.value = 'Starting simulator...';

  // Call immediately, then every 5 seconds
  performSimulation();
  timerId = setInterval(performSimulation, 5000);
};

const stopSimulator = () => {
  if (timerId) {
    clearInterval(timerId);
    timerId = null;
  }
  isRunning.value = false;
  if (latestMessage.value && !isError.value) {
    latestMessage.value = `Simulator stopped. Total session records: ${totalInserted.value}`;
  }
};

onBeforeUnmount(() => {
  stopSimulator();
});
</script>

