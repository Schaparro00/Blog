<template>
  <div class="create-blog">
    <h1>Crear Nueva Publicación</h1>

    <form @submit.prevent="submitBlog" class="blog-form">
      <div class="form-group">
        <label for="name">Nombre de la Publicación</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
        />
      </div>

      <div class="form-group">
        <label for="description">Descripción</label>
        <textarea
          id="description"
          v-model="form.description"
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
import { ref, reactive } from 'vue'
import { useBlogStore } from '../stores/blogStore'
import { useRouter } from 'vue-router'

const blogStore = useBlogStore()
const { addBlog, loading, error } = blogStore
const router = useRouter()

const form = reactive({
  name: '',
  description: '',
  image: null as File | null
})

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.image = target.files[0]
  }
}

const submitBlog = async () => {
  if (!form.name.trim() || !form.description.trim()) return

  try {
    const blogData = new FormData()
    blogData.append('name', form.name)
    blogData.append('description', form.description)
    if (form.image) {
      blogData.append('image', form.image)
    }

    await addBlog(blogData)
    router.push('/')
  } catch (err) {
    console.error('Error creating blog:', err)
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