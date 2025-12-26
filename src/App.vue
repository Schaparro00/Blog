<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <header>
    <nav>
      <RouterLink to="/" class="nav-link">Inicio</RouterLink>
      <RouterLink to="/create" class="nav-link">Crear Publicación</RouterLink>
    </nav>
  </header>

  <main>
    <router-view />
  </main>
</template>

<style scoped>
header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1.5rem 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

nav {
  display: flex;
  gap: 2rem;
  justify-content: center;
  align-items: center;
}

.nav-link {
  text-decoration: none;
  color: white;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  font-size: 1rem;
}

.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.nav-link.router-link-active {
  background-color: white;
  color: #667eea;
  border-color: white;
}

main {
  padding: 2rem;
  max-width: 1000px;
  margin: 0 auto;
}
</style>
