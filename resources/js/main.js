import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import tooltip from './tooltip';

const app = createApp(App)

axios.defaults.withCredentials = true;
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

app.directive('tooltip', tooltip);
app.use(router)
app.mount('#app');
