<template>
  <div class="home">
    <h1>Publicaciones</h1>

    <div v-if="loading" class="loading">Cargando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="blogs.length === 0" class="no-blogs">No hay publicaciones aún.</div>
    <div v-else>
      <div v-for="blog in blogs" :key="blog.id" class="blog-card">
        <h2 v-if="!editMode || currentEditBlog !== blog.id">{{ blog.name }}</h2>
        <input v-else v-model="editBlogs[blog.id]?.name" type="text" class="edit-input" />
        <p v-if="!editMode || currentEditBlog !== blog.id">{{ blog.description }}</p>
        <textarea v-else v-model="editBlogs[blog.id]?.description" class="edit-textarea"></textarea>
        <img v-if="blog.image" :src="blog.image" :alt="blog.name" class="blog-image" />
        <p class="date">Publicado: {{ formatDate(blog.created_at) }}</p>

        <div class="blog-actions">
          <button v-if="!editMode || currentEditBlog !== blog.id" @click="startEdit(blog)" class="btn-edit">Editar</button>
          <button v-if="editMode && currentEditBlog === blog.id" @click="saveEdit(blog.id)" class="btn-save">Guardar</button>
          <button v-if="editMode && currentEditBlog === blog.id" @click="cancelEdit" class="btn-cancel">Cancelar</button>
          <button @click="confirmDelete(blog.id)" class="btn-delete">Eliminar</button>
        </div>

        <div class="comments-section">
          <h3>Comentarios</h3>
          <div v-if="blog.comments.length === 0" class="no-comments">No hay comentarios aún.</div>
          <div v-else>
            <div v-for="comment in blog.comments" :key="comment.id" class="comment">
              <strong>{{ comment.name }}</strong>
              <p>{{ comment.content }}</p>
              <small>{{ formatDate(comment.created_at) }}</small>
            </div>
          </div>

          <form @submit.prevent="submitComment(blog.id)" class="comment-form">
            <input
              :value="newComments[blog.id]?.name"
              @input="updateCommentName(blog.id, ($event.target as HTMLInputElement).value)"
              type="text"
              placeholder="Tu nombre"
              required
            />
            <textarea
              :value="newComments[blog.id]?.content"
              @input="updateCommentContent(blog.id, ($event.target as HTMLTextAreaElement).value)"
              placeholder="Escribe un comentario..."
              required
            ></textarea>
            <button type="submit" :disabled="commentLoading[blog.id]">Comentar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useBlogStore } from '../stores/blogStore'

const blogStore = useBlogStore()
const { blogs, loading, error } = storeToRefs(blogStore)
const { getBlogs, addComment, deleteBlog, updateBlog } = blogStore

const newComments = ref<Partial<Record<number, { name: string; content: string }>>>({})
const commentLoading = ref<Record<number, boolean>>({})

const editMode = ref(false)
const currentEditBlog = ref<number | null>(null)
const editBlogs = ref<Partial<Record<number, { name: string; description: string }>>>({})

onMounted(() => {
  getBlogs()
})

// Watch blogs and initialize
watch(blogs, (newBlogs) => {
  newBlogs.forEach(blog => {
    if (!newComments.value[blog.id]) {
      newComments.value[blog.id] = { name: '', content: '' }
      commentLoading.value[blog.id] = false
    }
  })
}, { immediate: true })

const updateCommentName = (blogId: number, value: string) => {
  if (!newComments.value[blogId]) {
    newComments.value[blogId] = { name: '', content: '' }
  }
  newComments.value[blogId]!.name = value
}

const updateCommentContent = (blogId: number, value: string) => {
  if (!newComments.value[blogId]) {
    newComments.value[blogId] = { name: '', content: '' }
  }
  newComments.value[blogId]!.content = value
}

const submitComment = async (blogId: number) => {
  const comment = newComments.value[blogId]
  if (!comment || !comment.name.trim() || !comment.content.trim()) return

  commentLoading.value[blogId] = true
  try {
    await addComment(blogId, comment)
    newComments.value[blogId] = { name: '', content: '' }
  } catch (err) {
    console.error('Error adding comment:', err)
  } finally {
    commentLoading.value[blogId] = false
  }
}

const startEdit = (blog: any) => {
  editMode.value = true
  currentEditBlog.value = blog.id
  editBlogs.value[blog.id] = { name: blog.name, description: blog.description }
}

const saveEdit = async (blogId: number) => {
  const edited = editBlogs.value[blogId]
  if (!edited) return
  try {
    await updateBlog({ id: blogId, name: edited.name, description: edited.description })
    editMode.value = false
    currentEditBlog.value = null
  } catch (err) {
    console.error('Error updating blog:', err)
  }
}

const cancelEdit = () => {
  editMode.value = false
  currentEditBlog.value = null
}

const confirmDelete = async (blogId: number) => {
  if (confirm('¿Estás seguro de que quieres eliminar esta publicación?')) {
    try {
      await deleteBlog(blogId)
    } catch (err) {
      console.error('Error deleting blog:', err)
    }
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('es-ES')
}
</script>

<style scoped>
.home {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f5f5f5;
  min-height: 100vh;
}

h1 {
  text-align: center;
  color: #333;
  margin-bottom: 2rem;
  font-size: 2.5rem;
  font-weight: 300;
}

.blog-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin-bottom: 2rem;
  transition: transform 0.2s, box-shadow 0.2s;
}

.blog-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.blog-card h2 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 1.8rem;
  font-weight: 600;
  text-align: center;
}

.blog-card p {
  color: #555;
  line-height: 1.6;
  margin-bottom: 1rem;
  font-size: 1.1rem;
  text-align: center;
}

.blog-image {
  max-width: 100%;
  height: auto;
  margin: 1rem 0;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.comments-section {
  margin-top: 2rem;
  border-top: 2px solid #e9ecef;
  padding-top: 1.5rem;
  text-align: left;
}

.comments-section h3 {
  color: #2c3e50;
  margin-bottom: 1rem;
  font-size: 1.4rem;
  font-weight: 500;
}

.comment {
  background: white;
  border: 1px solid #ddd;
  border-left: 4px solid #007bff;
  margin-bottom: 1rem;
  padding: 1rem;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.2s, border-color 0.2s;
}

.comment:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-color: #0056b3;
}

.comment strong {
  color: #007bff;
  font-weight: 600;
}

.comment small {
  color: #6c757d;
  font-size: 0.85rem;
  display: block;
  margin-top: 0.5rem;
}

.comment-form {
  margin-top: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
}

.comment-form input,
.comment-form textarea {
  padding: 0.75rem;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.comment-form input:focus,
.comment-form textarea:focus {
  outline: none;
  border-color: #007bff;
}

.comment-form textarea {
  resize: vertical;
  min-height: 100px;
}

.comment-form button {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #007bff, #0056b3);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: background 0.2s, transform 0.1s;
}

.comment-form button:hover {
  background: linear-gradient(135deg, #0056b3, #004085);
  transform: translateY(-1px);
}

.comment-form button:disabled {
  background: #6c757d;
  cursor: not-allowed;
  transform: none;
}

.loading, .error, .no-blogs, .no-comments {
  text-align: center;
  padding: 3rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  font-size: 1.2rem;
  color: #666;
}

.error {
  color: #dc3545;
  background: #f8d7da;
  border: 1px solid #f5c6cb;
}

.date {
  color: #6c757d;
  font-size: 0.9rem;
  font-style: italic;
  text-align: center;
}

.blog-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  justify-content: center;
  margin: 1rem 0;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.btn-edit, .btn-save, .btn-cancel, .btn-delete {
  padding: 0.75rem 1.5rem;
  border: 2px solid transparent;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: all 0.2s;
  margin: 0 0.25rem;
}

.btn-edit {
  background-color: #007bff;
  color: white;
}

.btn-edit:hover {
  background-color: #0056b3;
}

.btn-save {
  background-color: #28a745;
  color: white;
}

.btn-save:hover {
  background-color: #218838;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
}

.btn-cancel:hover {
  background-color: #5a6268;
}

.btn-delete {
  background-color: #6f42c1;
  color: white;
}

.btn-delete:hover {
  background-color: #5a32a3;
}

.edit-input, .edit-textarea {
  width: 100%;
  padding: 0.5rem;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.edit-input {
  font-size: 1.8rem;
  font-weight: 600;
  color: #2c3e50;
}

.edit-textarea {
  font-size: 1.1rem;
  color: #555;
  resize: vertical;
  min-height: 100px;
}

/* Responsive design */
@media (max-width: 768px) {
  .home {
    padding: 1rem;
  }

  .blog-card {
    padding: 1.5rem;
  }

  h1 {
    font-size: 2rem;
  }

  .blog-card h2 {
    font-size: 1.5rem;
  }

  .comment-form {
    padding: 1rem;
  }
}
</style>