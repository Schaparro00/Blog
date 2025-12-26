import { defineStore } from "pinia";
import axios from "axios";

const apiUrl = import.meta.env.VITE_APP_API_URL;

interface User {
  id: number;
  name: string;
  email: string;
}

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null as User | null,
    token: localStorage.getItem("token") || null,
    loading: false,
    error: null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
  },

  actions: {
    // Initialize auth state from localStorage
    initializeAuth() {
      const token = localStorage.getItem("token");
      if (token) {
        this.token = token;
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        this.fetchUser();
      }
    },

    // Login
    async login(credentials: { email: string; password: string }) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post(`${apiUrl}/auth/login`, credentials);
        const { token, user } = response.data;

        this.token = token;
        this.user = user;
        localStorage.setItem("token", token);
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        return user;
      } catch (err: any) {
        this.error = err.response?.data?.message || "Login failed";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // Register
    async register(userData: { name: string; email: string; password: string }) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post(`${apiUrl}/auth/register`, userData);
        // Assuming register returns token and user like login
        const { token, user } = response.data;

        this.token = token;
        this.user = user;
        localStorage.setItem("token", token);
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        return user;
      } catch (err: any) {
        this.error = err.response?.data?.message || "Registration failed";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // Fetch current user
    async fetchUser() {
      if (!this.token) return;

      try {
        const response = await axios.get(`${apiUrl}/auth/me`);
        this.user = response.data.user;
      } catch (err) {
        this.logout();
      }
    },

    // Logout
    async logout() {
      try {
        await axios.post(`${apiUrl}/auth/logout`);
      } catch (err) {
        // Ignore errors on logout
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem("token");
        delete axios.defaults.headers.common["Authorization"];
      }
    },
  },
});