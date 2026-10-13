import { createApp } from 'vue';
import Dashboard from './components/Dashboard.vue';
import Simulator from './components/Simulator.vue';

const isSimulator = window.location.pathname.startsWith('/iot');
const app = createApp(isSimulator ? Simulator : Dashboard);
app.mount('#app');
