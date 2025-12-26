<template>
  <div class="home">
    <div class="header">
      <div class="header-content">
        <h1>📝 Mi Blog</h1>
        <div class="user-actions">
          <div class="user-info" v-if="authStore.user">
            <div class="avatar">{{ authStore.user.name.charAt(0).toUpperCase() }}</div>
            <span class="welcome">{{ authStore.user.name }}</span>
          </div>
          <button @click="logout" class="btn-logout">Cerrar Sesión</button>
        </div>
      </div>
    </div>

    <div class="container">
      <router-link to="/create" class="btn-create">
        <span class="icon">✏️</span>
        Crear Nueva Publicación
      </router-link>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Cargando publicaciones...</p>
      </div>
      
      <div v-else-if="error" class="error-message">
        <span class="icon">⚠️</span>
        {{ error }}
      </div>
      
      <div v-else-if="posts.length === 0" class="empty-state">
        <div class="empty-icon">📭</div>
        <h2>No hay publicaciones aún</h2>
        <p>Sé el primero en crear una publicación</p>
        <router-link to="/create" class="btn-empty">Crear Publicación</router-link>
      </div>
      
      <div v-else class="posts-grid">
        <article v-for="post in posts" :key="post.id" class="post-card">
          <div class="post-header">
            <div class="post-title-section">
              <h2>{{ post.title }}</h2>
              <div class="post-meta">
                <span class="author">
                  <span class="icon">👤</span>
                  {{ post.author.name }}
                </span>
                <span class="date">
                  <span class="icon">📅</span>
                  {{ formatDate(post.created_at) }}
                </span>
              </div>
            </div>
          </div>

          <div class="post-body">
            <p class="post-content">{{ post.content }}</p>
            <img 
              v-if="post.image" 
              :src="`${apiUrl}/storage/${post.image}`" 
              :alt="post.title" 
              class="post-image"
            />
          </div>

          <div class="comments-section">
            <div class="comments-header">
              <h3>💬 Comentarios ({{ post.comments.length }})</h3>
            </div>
            
            <div class="comments-list">
              <div v-if="post.comments.length === 0" class="no-comments">
                <span class="icon">💭</span>
                <p>No hay comentarios. ¡Sé el primero en comentar!</p>
              </div>
              
              <div v-else class="comments-container">
                <div v-for="comment in post.comments" :key="comment.id" class="comment">
                  <div class="comment-avatar">{{ comment.author.name.charAt(0).toUpperCase() }}</div>
                  <div class="comment-content">
                    <div class="comment-header">
                      <strong class="comment-author">{{ comment.author.name }}</strong>
                      <small class="comment-date">{{ formatDate(comment.created_at) }}</small>
                    </div>
                    <p class="comment-text">{{ comment.content }}</p>
                  </div>
                </div>
              </div>
            </div>

            <form @submit.prevent="submitComment(post.id)" class="comment-form">
              <div class="form-input-wrapper">
                <textarea
                  v-model="newComments[post.id]"
                  placeholder="Escribe tu comentario..."
                  required
                  rows="3"
                ></textarea>
                <button type="submit" :disabled="commentLoading[post.id]" class="btn-comment">
                  <span v-if="!commentLoading[post.id]">Enviar</span>
                  <span v-else>Enviando...</span>
                </button>
              </div>
            </form>
          </div>
        </article>
      </div>

      <div v-if="pagination && pagination.last_page > 1" class="pagination">
        <button
          @click="loadPage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="btn-page"
        >
          ← Anterior
        </button>

        <span class="page-info">
          Página <strong>{{ pagination.current_page }}</strong> de <strong>{{ pagination.last_page }}</strong>
        </span>

        <button
          @click="loadPage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="btn-page"
        >
          Siguiente →
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useBlogStore } from '../stores/blogStore'
import { useAuthStore } from '../stores/authStore'

const apiUrl = import.meta.env.VITE_APP_API_URL

const blogStore = useBlogStore()
const authStore = useAuthStore()
const { posts, loading, error, pagination } = storeToRefs(blogStore)
const { getPosts, addComment } = blogStore
const { logout } = authStore

const newComments = ref<Record<number, string>>({})
const commentLoading = ref<Record<number, boolean>>({})

onMounted(() => {
  getPosts()
})

const submitComment = async (postId: number) => {
  const content = newComments.value[postId]?.trim()
  if (!content) return

  commentLoading.value[postId] = true
  try {
    await addComment(postId, content)
    newComments.value[postId] = ''
  } catch (err) {
    console.error('Error adding comment:', err)
  } finally {
    commentLoading.value[postId] = false
  }
}

const loadPage = (page: number) => {
  getPosts(page)
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.home {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding-bottom: 3rem;
}

.header {
  background: rgba(255, 255, 255, 0.98);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
  backdrop-filter: blur(10px);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

h1 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.user-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
  border-radius: 50px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.1rem;
}

.welcome {
  font-weight: 600;
  color: #333;
}

.btn-logout {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
}

.btn-logout:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.btn-create {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem 2rem;
  background: white;
  color: #667eea;
  text-decoration: none;
  border-radius: 30px;
  font-weight: 600;
  font-size: 1.1rem;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  margin-bottom: 2rem;
}

.btn-create:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
}

.icon {
  font-size: 1.2rem;
}

.loading {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-message {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
  color: white;
  padding: 1.5rem 2rem;
  border-radius: 15px;
  text-align: center;
  font-weight: 600;
  box-shadow: 0 8px 25px rgba(238, 90, 111, 0.3);
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 1rem;
}

.empty-state h2 {
  color: #333;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #666;
  margin-bottom: 2rem;
}

.btn-empty {
  display: inline-block;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 25px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.posts-grid {
  display: grid;
  gap: 2rem;
}

.post-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  transition: all 0.3s ease;
}

.post-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.18);
}

.post-header {
  padding: 2rem 2rem 1rem;
  background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
}

.post-title-section h2 {
  color: #2c3e50;
  font-size: 1.8rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.post-meta {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  font-size: 0.9rem;
}

.author, .date {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: #666;
}

.post-body {
  padding: 2rem;
}

.post-content {
  color: #555;
  line-height: 1.8;
  font-size: 1.05rem;
  margin-bottom: 1.5rem;
}

.post-image {
  width: 100%;
  max-height: 500px;
  object-fit: cover;
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.comments-section {
  border-top: 2px solid #f0f0f0;
  padding: 2rem;
  background: #fafafa;
}

.comments-header h3 {
  color: #2c3e50;
  font-size: 1.3rem;
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.no-comments {
  text-align: center;
  padding: 2rem;
  color: #999;
}

.no-comments .icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 0.5rem;
}

.comments-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.comment {
  display: flex;
  gap: 1rem;
  padding: 1.25rem;
  background: white;
  border-radius: 12px;
}