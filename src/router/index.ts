import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import CreateBlog from '../views/CreateBlog.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/create',
    name: 'CreateBlog',
    component: CreateBlog
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router