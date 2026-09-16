import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import AddEmployee from '../components/AddEmployee.vue'
import EmployeeList from '../components/EmployeeList.vue'
import Stats from '../components/Stats.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/ajout',
    name: 'AddEmployee',
    component: AddEmployee
  },
  {
    path: '/liste',
    name: 'EmployeeList',
    component: EmployeeList
  },
  {
    path: '/bilan',
    name: 'Stats',
    component: Stats
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router