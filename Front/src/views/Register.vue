<template>
  <div class="register-page">
    <div class="register-container">
      <div class="register-card">
        <div class="register-header">
          <div class="logo">🎉</div>
          <h1>Crear Cuenta</h1>
          <p class="subtitle">Únete a nuestra comunidad</p>
        </div>

        <form @submit.prevent="handleRegister" class="register-form">
          <div class="form-group">
            <label for="name">
              <span class="icon">👤</span>
              Nombre Completo
            </label>
            <div class="input-wrapper">
              <input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Tu nombre"
                required
                autocomplete="name"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="email">
              <span class="icon">📧</span>
              Correo Electrónico
            </label>
            <div class="input-wrapper">
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="tu@email.com"
                required
                autocomplete="email"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="password">
              <span class="icon">🔒</span>
              Contraseña
            </label>
            <div class="input-wrapper">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Mínimo 8 caracteres"
                required
                autocomplete="new-password"
                minlength="8"
              />
              <button
                type="button"
                class="toggle-password"
                @click="showPassword = !showPassword"
                tabindex="-1"
              >
                {{ showPassword ? '👁️' : '👁️‍🗨️' }}
              </button>
            </div>
            <div class="password-strength">
              <div class="strength-bar" :class="passwordStrength.class">
                <div class="strength-fill" :style="{ width: passwordStrength.width }"></div>
              </div>
              <span class="strength-text">{{ passwordStrength.text }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="password_confirmation">
              <span class="icon">✓</span>
              Confirmar Contraseña
            </label>
            <div class="input-wrapper">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Repite tu contraseña"
                required
                autocomplete="new-password"
              />
              <button
                type="button"
                class="toggle-password"
                @click="showConfirmPassword = !showConfirmPassword"
                tabindex="-1"
              >
                {{ showConfirmPassword ? '👁️' : '👁️‍🗨️' }}
              </button>
            </div>
            <div v-if="form.password_confirmation && form.password !== form.password_confirmation" class="password-mismatch">
              ⚠️ Las contraseñas no coinciden
            </div>
            <div v-else-if="form.password_confirmation && form.password === form.password_confirmation" class="password-match">
              ✓ Las contraseñas coinciden
            </div>
          </div>

          <button type="submit" :disabled="loading || !isFormValid" class="btn-submit">
            <span v-if="!loading">Crear Cuenta</span>
            <span v-else class="loading-content">
              <span class="spinner"></span>
              Creando cuenta...
            </span>
          </button>
        </form>

        <div class="divider">
          <span>O</span>
        </div>

        <div class="login-section">
          <p class="login-text">¿Ya tienes una cuenta?</p>
          <router-link to="/login" class="btn-login">
            Iniciar Sesión
          </router-link>
        </div>

        <div v-if="error" class="error-message">
          <span class="error-icon">⚠️</span>
          <span>{{ error }}</span>
        </div>
      </div>

      <div class="decorative-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore()
const router = useRouter()
const { register, loading, error } = authStore

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const passwordStrength = computed(() => {
  const password = form.password
  if (!password) return { width: '0%', class: '', text: '' }
  
  let strength = 0
  if (password.length >= 8) strength++
  if (password.length >= 12) strength++
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++
  if (/\d/.test(password)) strength++
  if (/[^a-zA-Z0-9]/.test(password)) strength++
  
  if (strength <= 2) return { width: '33%', class: 'weak', text: 'Débil' }
  if (strength <= 3) return { width: '66%', class: 'medium', text: 'Media' }
  return { width: '100%', class: 'strong', text: 'Fuerte' }
})

const isFormValid = computed(() => {
  return form.name.trim() &&
         form.email.trim() &&
         form.password.length >= 8 &&
         form.password === form.password_confirmation
})

const handleRegister = async () => {
  if (form.password !== form.password_confirmation) {
    authStore.error = 'Las contraseñas no coinciden'
    return
  }

  try {
    await register({
      name: form.name,
      email: form.email,
      password: form.password,
    })
    router.push('/')
  } catch (err) {
    // Error is handled in store
  }
}
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  position: relative;
  overflow: hidden;
}

.register-container {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 500px;
}

.register-card {
  background: white;
  border-radius: 25px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  padding: 3rem 2.5rem;
  position: relative;
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.register-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo {
  font-size: 4rem;
  margin-bottom: 1rem;
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

.register-header h1 {
  margin: 0 0 0.5rem 0;
  font-size: 2rem;
  color: #2c3e50;
  font-weight: 700;
}

.subtitle {
  margin: 0;
  color: #666;
  font-size: 1rem;
}

.register-form {
  margin-bottom: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.icon {
  font-size: 1.2rem;
}

.input-wrapper {
  position: relative;
}

.form-group input {
  width: 100%;
  padding: 1rem 1.25rem;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  transition: all 0.3s ease;
  background: #fafafa;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.toggle-password {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.3rem;
  padding: 0.5rem;
  transition: transform 0.2s ease;
}

.toggle-password:hover {
  transform: translateY(-50%) scale(1.1);
}

.password-strength {
  margin-top: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.strength-bar {
  flex: 1;
  height: 6px;
  background: #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
}

.strength-fill {
  height: 100%;
  transition: all 0.3s ease;
  border-radius: 10px;
}

.strength-bar.weak .strength-fill {
  background: linear-gradient(90deg, #ff6b6b, #ee5a6f);
}

.strength-bar.medium .strength-fill {
  background: linear-gradient(90deg, #feca57, #ff9ff3);
}

.strength-bar.strong .strength-fill {
  background: linear-gradient(90deg, #48dbfb, #0abde3);
}

.strength-text {
  font-size: 0.85rem;
  font-weight: 600;
  min-width: 60px;
}

.strength-bar.weak + .strength-text {
  color: #ff6b6b;
}

.strength-bar.medium + .strength-text {
  color: #feca57;
}

.strength-bar.strong + .strength-text {
  color: #0abde3;
}

.password-mismatch {
  margin-top: 0.5rem;
  color: #ff6b6b;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.password-match {
  margin-top: 0.5rem;
  color: #0abde3;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.btn-submit {
  width: 100%;
  padding: 1.1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 1.05rem;
  transition: all 0.3s ease;
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  margin-top: 0.5rem;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
}

.btn-submit:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.loading-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.divider {
  position: relative;
  text-align: center;
  margin: 2rem 0;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(to right, transparent, #e0e0e0, transparent);
}

.divider span {
  position: relative;
  background: white;
  padding: 0 1rem;
  color: #999;
  font-size: 0.9rem;
  font-weight: 600;
}

.login-section {
  text-align: center;
}

.login-text {
  margin: 0 0 1rem 0;
  color: #666;
}

.btn-login {
  display: inline-block;
  padding: 0.9rem 2rem;
  background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
  color: #667eea;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
  border: 2px solid #667eea;
}

.btn-login:hover {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.error-message {
  margin-top: 1.5rem;
  padding: 1rem 1.25rem;
  background: linear-gradient(135deg, #ff6b6b15 0%, #ee5a6f15 100%);
  border: 2px solid #ff6b6b;
  border-radius: 12px;
  color: #d63031;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 600;
  font-size: 0.95rem;
  animation: shake 0.5s ease;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-10px); }
  75% { transform: translateX(10px); }
}

.error-icon {
  font-size: 1.3rem;
}

.decorative-shapes {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 1;
}

.shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 300px;
  height: 300px;
  top: -150px;
  right: -100px;
  animation-delay: 0s;
}

.shape-2 {
  width: 200px;
  height: 200px;
  bottom: -100px;
  left: -50px;
  animation-delay: 2s;
}

.shape-3 {
  width: 150px;
  height: 150px;
  top: 50%;
  left: 10%;
  animation-delay: 4s;
}

@keyframes float {
  0%, 100% {
    transform: translate(0, 0) rotate(0deg);
  }
  33% {
    transform: translate(30px, -30px) rotate(120deg);
  }
  66% {
    transform: translate(-20px, 20px) rotate(240deg);
  }
}

@media (max-width: 768px) {
  .register-page {
    padding: 1rem;
  }

  .register-card {
    padding: 2rem 1.5rem;
  }

  .register-header h1 {
    font-size: 1.7rem;
  }

  .logo {
    font-size: 3rem;
  }
}
</style>