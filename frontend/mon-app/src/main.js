import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Importer Bootstrap CSS et JS
import 'animate.css/animate.min.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js' // ← Cette ligne est cruciale !
import 'bootstrap-icons/font/bootstrap-icons.css'

const app = createApp(App)
app.use(router)
app.mount('#app')

