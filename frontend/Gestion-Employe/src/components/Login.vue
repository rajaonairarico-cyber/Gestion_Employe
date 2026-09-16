<template>
  <div class="login-scene">
    <!-- Particules flottantes -->
    <div class="particles">
      <span v-for="i in 12" :key="i" class="particle" :style="particleStyle(i)"></span>
    </div>

    <div class="glass-card" :class="{ shake: loginError }">

      <!-- Lueur supérieure -->
      <div class="glow-top"></div>

      <!-- Logo -->
      <div class="logo-wrap">
        <div class="logo-ring">
          <div class="logo-pulse"></div>
          <i class="bi bi-shield-lock-fill logo-icon"></i>
        </div>
        <h3 class="card-title">Connexion</h3>
        <p class="card-sub">Entrez vos identifiants pour accéder</p>
      </div>

      <!-- Formulaire -->
      <form @submit.prevent="login">

        <!-- Username -->
        <div class="field-wrap" :class="{ focused: focusedField === 'username' }">
          <div class="field-icon-box">
            <i class="bi bi-person"></i>
          </div>
          <div class="field-inner">
            <label class="field-label">Nom d'utilisateur</label>
            <input
              v-model="username"
              type="text"
              class="field-input"
              @focus="focusedField = 'username'"
              @blur="focusedField = null"
              autocomplete="username"
              required
            />
          </div>
        </div>

        <!-- Password -->
        <div class="field-wrap" :class="{ focused: focusedField === 'password', 'has-error': loginError }">
          <div class="field-icon-box">
            <i class="bi bi-lock"></i>
          </div>
          <div class="field-inner">
            <label class="field-label">Mot de passe</label>
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              class="field-input"
              @focus="focusedField = 'password'"
              @blur="focusedField = null"
              autocomplete="current-password"
              required
            />
          </div>
          <button type="button" class="eye-btn" @click="showPassword = !showPassword" tabindex="-1">
            <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
          </button>
        </div>

        <!-- Bouton -->
        <button
          type="submit"
          class="submit-btn"
          :disabled="isLoading"
          :class="{ loading: isLoading }"
        >
          <span v-if="!isLoading" class="btn-content">
            <i class="bi bi-box-arrow-in-right"></i>
            Se connecter
          </span>
          <span v-else class="btn-content">
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Connexion en cours...
          </span>
          <div class="btn-shine"></div>
        </button>

      </form>

      <!-- Message -->
      <transition name="slide-msg">
        <p v-if="message" class="feedback-msg" :class="messageColor">
          <i class="bi" :class="messageIcon"></i>
          {{ message }}
        </p>
      </transition>

    </div>
  </div>
</template>

<script>
import API_BASE from '../config'

export default {
  data() {
    return {
      username: '',
      password: '',
      showPassword: false,
      isLoading: false,
      loginError: false,
      focusedField: null,
      message: '',
      messageColor: 'text-danger',
      messageIcon: 'bi-exclamation-circle'
    }
  },
  methods: {
    particleStyle(i) {
      const sizes   = [4, 6, 3, 5, 4, 7, 3, 5, 4, 6, 3, 5];
      const tops    = [10, 25, 40, 60, 75, 85, 15, 50, 30, 70, 55, 20];
      const lefts   = [8, 18, 30, 45, 60, 72, 85, 92, 15, 38, 65, 80];
      const delays  = [0, 1.2, 2.4, 0.6, 3, 1.8, 2.1, 0.3, 3.5, 1.5, 2.7, 0.9];
      const durs    = [6, 7, 5, 8, 6, 7, 5, 6, 8, 7, 6, 5];
      const s = sizes[i - 1];
      return {
        width: s + 'px',
        height: s + 'px',
        top: tops[i - 1] + '%',
        left: lefts[i - 1] + '%',
        animationDelay: delays[i - 1] + 's',
        animationDuration: durs[i - 1] + 's'
      };
    },
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },
    async login() {
      this.isLoading = true;
      this.loginError = false;
      this.message = '';
      try {
        const response = await fetch(`${API_BASE}/login.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username: this.username, password: this.password })
        });
        const data = await response.json();
        if (data.message === 'Connexion réussie') {
          this.message = 'Connexion réussie !';
          this.messageColor = 'text-success';
          this.messageIcon = 'bi-check-circle-fill';
          setTimeout(() => this.$router.push('/liste'), 800);
        } else {
          this.message = 'Identifiants incorrects. Réessayez.';
          this.messageColor = 'text-danger';
          this.messageIcon = 'bi-x-circle-fill';
          this.loginError = true;
          setTimeout(() => { this.loginError = false; }, 600);
        }
      } catch (error) {
        this.message = 'Erreur de connexion au serveur.';
        this.messageColor = 'text-danger';
        this.messageIcon = 'bi-wifi-off';
        this.loginError = true;
        setTimeout(() => { this.loginError = false; }, 600);
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');

/* ── Scène ── */
.login-scene {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
  font-family: 'Plus Jakarta Sans', sans-serif;
  position: relative;
  overflow: hidden;
}

/* ── Particules ── */
.particles { position: fixed; inset: 0; pointer-events: none; }
.particle {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.15);
  animation: floatUp linear infinite;
}
@keyframes floatUp {
  0%   { transform: translateY(0) scale(1); opacity: 0; }
  20%  { opacity: 1; }
  80%  { opacity: 1; }
  100% { transform: translateY(-80px) scale(1.4); opacity: 0; }
}

/* ── Carte ── */
.glass-card {
  position: relative;
  width: 420px;
  background: rgba(255, 255, 255, 0.96);
  border-radius: 28px;
  padding: 48px 40px 40px;
  box-shadow:
    0 24px 80px rgba(0, 0, 0, 0.45),
    0 0 0 1px rgba(255, 255, 255, 0.12),
    0 0 60px rgba(102, 126, 234, 0.15);
  backdrop-filter: blur(20px);
  animation: cardUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
  overflow: hidden;
}
@keyframes cardUp {
  from { opacity: 0; transform: translateY(40px) scale(0.95); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
.glass-card.shake {
  animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
}
@keyframes shake {
  0%,100% { transform: translateX(0); }
  15%  { transform: translateX(-8px); }
  30%  { transform: translateX(7px); }
  45%  { transform: translateX(-5px); }
  60%  { transform: translateX(4px); }
  75%  { transform: translateX(-2px); }
}

/* Lueur supérieure */
.glow-top {
  position: absolute;
  top: -60px; left: 50%;
  transform: translateX(-50%);
  width: 200px; height: 120px;
  background: radial-gradient(ellipse, rgba(102, 126, 234, 0.25), transparent 70%);
  pointer-events: none;
}

/* ── Logo ── */
.logo-wrap {
  text-align: center;
  margin-bottom: 36px;
}
.logo-ring {
  position: relative;
  width: 76px; height: 76px;
  margin: 0 auto 18px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 8px 32px rgba(102, 126, 234, 0.45);
}
.logo-pulse {
  position: absolute;
  inset: -4px;
  border-radius: 50%;
  border: 2px solid rgba(102, 126, 234, 0.3);
  animation: ringPulse 2.5s ease-in-out infinite;
}
@keyframes ringPulse {
  0%,100% { transform: scale(1); opacity: 0.6; }
  50%      { transform: scale(1.1); opacity: 0; }
}
.logo-icon {
  font-size: 1.8rem;
  color: #fff;
}
.card-title {
  font-size: 1.6rem;
  font-weight: 800;
  color: #1a1a2e;
  margin: 0 0 6px;
  letter-spacing: -0.02em;
}
.card-sub {
  font-size: 0.82rem;
  color: #888;
  margin: 0;
}

/* ── Champs ── */
.field-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f7f8fc;
  border: 1.5px solid #e8eaf0;
  border-radius: 14px;
  padding: 14px 16px;
  margin-bottom: 14px;
  transition: border-color 0.25s, background 0.25s, box-shadow 0.25s;
}
.field-wrap.focused {
  border-color: #667eea;
  background: #fff;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}
.field-wrap.has-error {
  border-color: #ef4444;
  box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.field-icon-box {
  width: 36px; height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  font-size: 0.9rem;
  flex-shrink: 0;
  transition: transform 0.2s;
}
.field-wrap.focused .field-icon-box {
  transform: scale(1.08);
}

.field-inner {
  flex: 1;
}
.field-label {
  display: block;
  font-size: 0.68rem;
  font-weight: 700;
  color: #aaa;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  margin-bottom: 3px;
  transition: color 0.2s;
}
.field-wrap.focused .field-label { color: #667eea; }

.field-input {
  width: 100%;
  background: none;
  border: none;
  outline: none;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.95rem;
  font-weight: 600;
  color: #1a1a2e;
  padding: 0;
  caret-color: #667eea;
}

.eye-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #bbb;
  font-size: 0.95rem;
  padding: 4px;
  display: flex; align-items: center;
  transition: color 0.2s;
  flex-shrink: 0;
}
.eye-btn:hover { color: #667eea; }

/* ── Bouton ── */
.submit-btn {
  position: relative;
  width: 100%;
  padding: 16px;
  margin-top: 8px;
  border: none;
  border-radius: 14px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 1rem;
  font-weight: 800;
  letter-spacing: 0.02em;
  cursor: pointer;
  overflow: hidden;
  box-shadow: 0 6px 24px rgba(102, 126, 234, 0.4);
  transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
}
.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 32px rgba(102, 126, 234, 0.55);
}
.submit-btn:active:not(:disabled) { transform: translateY(0); }
.submit-btn:disabled { opacity: 0.65; cursor: not-allowed; }

.btn-content {
  position: relative;
  z-index: 1;
  display: flex; align-items: center; justify-content: center;
  gap: 10px;
}

/* Effet brillance */
.btn-shine {
  position: absolute;
  top: 0; left: -120%;
  width: 60%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.22), transparent);
  transform: skewX(-20deg);
  animation: shine 3.5s ease-in-out infinite;
}
@keyframes shine {
  0%   { left: -120%; }
  35%  { left: 160%; }
  100% { left: 160%; }
}

/* ── Message ── */
.feedback-msg {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 18px;
  font-size: 0.84rem;
  font-weight: 600;
  padding: 12px 16px;
  border-radius: 10px;
}
.feedback-msg.text-success {
  background: #f0fdf4;
  color: #16a34a;
  border: 1px solid #bbf7d0;
}
.feedback-msg.text-danger {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

/* Transitions */
.slide-msg-enter-active, .slide-msg-leave-active { transition: all 0.3s ease; }
.slide-msg-enter-from, .slide-msg-leave-to { opacity: 0; transform: translateY(-8px); }
</style>