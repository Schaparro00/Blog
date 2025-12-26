import { defineStore } from "pinia";
import axios from "axios";

const apiUrl = import.meta.env.VITE_APP_API_URL;

interface Comment {
  id: number;
  blog_id: number;
  name: string;
  content: string;
  created_at: string;
  updated_at: string;
}

interface Blog {
  id: number;
  name: string;
  description: string;
  image: string | null;
  created_at: string;
  updated_at: string;
  comments: Comment[];
}

export const useBlogStore = defineStore("blog", {
  state: () => ({
    blogs: [] as Blog[],          // lista de blogs
    loading: false,
    error: null as string | null,
  }),

  actions: {
    // 🔹 OBTENER BLOGS
    async getBlogs() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get(`${apiUrl}/blogs`);
        this.blogs = response.data; // Direct array as per user example
      } catch (err) {
        this.error = 'Error loading blogs';
      } finally {
        this.loading = false;
      }
    },

    // 🔹 CREAR BLOG
    async addBlog(blog: any) {
      try {
        const response = await axios.post(`${apiUrl}/blogs`, blog);
        this.blogs.unshift(response.data);
        return response.data;
      } catch (err) {
        this.error = 'Error creating blog';
        throw err;
      }
    },

    // 🔹 ACTUALIZAR BLOG
    async updateBlog(blog: any) {
      try {
        const response = await axios.put(
          `${apiUrl}/blogs/${blog.id}`,
          blog
        );

        const index = this.blogs.findIndex(b => b.id === blog.id);
        if (index !== -1) {
          this.blogs[index] = response.data;
        }
      } catch (err) {
        this.error = 'Error updating blog';
      }
    },

    // 🔹 ELIMINAR BLOG
    async deleteBlog(id: number) {
      try {
        await axios.delete(`${apiUrl}/blogs/${id}`);
        this.blogs = this.blogs.filter(b => b.id !== id);
      } catch (err) {
        this.error = 'Error deleting blog';
      }
    },

    // 🔹 AGREGAR COMENTARIO
    async addComment(blogId: number, comment: any) {
      try {
        await axios.post(`${apiUrl}/blogs/${blogId}/comments`, comment);
        // Refresh blogs to get updated comments
        await this.getBlogs();
      } catch (err) {
        this.error = 'Error adding comment';
        throw err;
      }
    },
  },
});