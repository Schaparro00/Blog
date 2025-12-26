import { defineStore } from "pinia";
import axios from "axios";

const apiUrl = import.meta.env.VITE_APP_API_URL;

interface Comment {
  id: number;
  content: string;
  author: {
    id: number;
    name: string;
    email: string;
  };
  post_id: number;
  created_at: string;
  updated_at: string;
}

interface Post {
  id: number;
  title: string;
  content: string;
  image: string | null;
  author: {
    id: number;
    name: string;
    email: string;
  };
  created_at: string;
  updated_at: string;
  comments: Comment[];
}

interface PaginatedPosts {
  data: Post[];
  meta: {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
    from: number;
    to: number;
  };
}

export const useBlogStore = defineStore("blog", {
  state: () => ({
    posts: [] as Post[],
    pagination: null as any,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    // Get posts with pagination
    async getPosts(page = 1, perPage = 10) {
      this.loading = true;
      this.error = null;
      console.log('Fetching posts', { page, perPage, apiUrl });

      try {
        const response = await axios.get(`${apiUrl}/posts?page=${page}&per_page=${perPage}`);
        console.log('Posts response', response.data);
        this.posts = response.data.data;
        this.pagination = response.data.meta;
        console.log('Posts set', this.posts.length, 'Pagination', this.pagination);
      } catch (err: any) {
        console.error('Error loading posts', err);
        this.error = err.response?.data?.message || 'Error loading posts';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // Create post
    async createPost(postData: { title: string; content: string; image?: File }) {
      this.error = null;

      try {
        const formData = new FormData();
        formData.append('title', postData.title);
        formData.append('content', postData.content);
        if (postData.image) {
          formData.append('image', postData.image);
        }

        const response = await axios.post(`${apiUrl}/posts`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        // Add to the beginning of posts
        this.posts.unshift(response.data);
        return response.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Error creating post';
        throw err;
      }
    },

    // Add comment to post
    async addComment(postId: number, content: string) {
      this.error = null;

      try {
        const response = await axios.post(`${apiUrl}/posts/${postId}/comments`, {
          content,
        });

        // Find the post and add the comment
        const postIndex = this.posts.findIndex(p => p.id === postId);
        if (postIndex !== -1) {
          this.posts[postIndex]!.comments.push(response.data);
        }

        return response.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Error adding comment';
        throw err;
      }
    },
  },
});