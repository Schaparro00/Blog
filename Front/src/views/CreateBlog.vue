<template>
  <div class="create-blog">
    <h1>Crear Nueva Publicación</h1>

    <form @submit.prevent="submitBlog" class="blog-form">
      <div class="form-group">
        <label for="title">Título de la Publicación</label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
        />
      </div>

      <div class="form-group">
        <label for="content">Contenido</label>
        <textarea
          id="content"
          v-model="form.content"
          required
        ></textarea>
      </div>

      <div class="form-group">
        <label for="image">Imagen (opcional)</label>
        <input
          id="image"
          type="file"
          accept="image/*"
          @change="handleImageChange"
        />
      </div>

      <button type="submit" :disabled="loading">Crear Publicación</button>
    </form>

    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { useBlogStore } from '../stores/blogStore'
import { useRouter } from 'vue-router'

const blogStore = useBlogStore()
const { createPost, loading, error } = blogStore
const router = useRouter()

const form = reactive({
  title: '',
  content: '',
  image: undefined as File | undefined
})

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.image = target.files[0]
  } else {
    form.image = undefined
  }
}

const submitBlog = async () => {
  if (!form.title.trim() || !form.content.trim()) return

  try {
    await createPost(form)
    router.push('/')
  } catch (err) {
    console.error('Error creating post:', err)
  }
}
</script>

<style scoped>
.create-blog {
  max-width: 600px;
  margin: 0 auto;
}

.blog-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input,
.form-group textarea {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

button {
  padding: 0.75rem 1.5rem;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

button:hover {
  background-color: #218838;
}

button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.error {
  color: red;
  margin-top: 1rem;
}
</style>