<template>
  <div id="app">
    <!-- Menu de navigation (caché sur la page de connexion) -->
    <nav v-if="$route.path !== '/'" class="navbar navbar-expand-lg sticky-top app-navbar">
      <div class="container">
        <router-link class="navbar-brand d-flex align-items-center" to="/liste">
          <span class="brand-badge">
            <i class="bi bi-person-badge-fill"></i>
          </span>
          <span class="brand-text">
            <span class="brand-name">GestionRH</span>
            <span class="brand-tag">Gestion des employés</span>
          </span>
        </router-link>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item">
              <router-link class="nav-link" to="/ajout" active-class="active">
                <span class="nav-icon"><i class="bi bi-plus-circle-fill"></i></span>
                <span>Ajouter</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/liste" active-class="active">
                <span class="nav-icon"><i class="bi bi-list-ul"></i></span>
                <span>Liste</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/bilan" active-class="active">
                <span class="nav-icon"><i class="bi bi-bar-chart-fill"></i></span>
                <span>Bilan</span>
              </router-link>
            </li>
          </ul>

          <!-- Bouton déconnexion -->
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-logout" type="button" @click="logout">
              <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Affichage de la page courante -->
    <div class="page-shell">
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  name: 'App',
  methods: {
    logout() {
      this.$router.push('/');
    }
  }
}
</script>

<style>
.app-navbar {
  background: rgba(255, 255, 255, 0.82);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-bottom: 1px solid rgba(233, 234, 242, 0.9);
  padding-top: 10px;
  padding-bottom: 10px;
  z-index: 1030;
}

/* ── Marque ── */
.brand-badge {
  width: 42px;
  height: 42px;
  border-radius: 13px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 1.15rem;
  box-shadow: 0 6px 18px rgba(102, 126, 234, 0.4);
  margin-right: 12px;
  transition: transform 0.25s ease;
}
.navbar-brand:hover .brand-badge { transform: rotate(-8deg) scale(1.06); }

.brand-text { display: flex; flex-direction: column; line-height: 1.1; }
.brand-name {
  font-weight: 800;
  font-size: 1.15rem;
  color: #1d1d2b;
  letter-spacing: -0.02em;
}
.brand-tag { font-size: 0.68rem; color: #8a8fa3; font-weight: 600; }

/* ── Liens ── */
.app-navbar .nav-link {
  display: flex;
  align-items: center;
  gap: 9px;
  font-weight: 600;
  color: #5a5f77;
  padding: 9px 18px;
  margin: 0 4px;
  border-radius: 12px;
  transition: all 0.25s ease;
  position: relative;
}
.app-navbar .nav-link .nav-icon { font-size: 1rem; }
.app-navbar .nav-link:hover { color: #667eea; background: rgba(102, 126, 234, 0.08); }
.app-navbar .nav-link.active {
  color: #fff;
  background: linear-gradient(135deg, #667eea, #764ba2);
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.35);
}

/* ── Déconnexion ── */
.btn-logout {
  background: #f1f2fa;
  color: #6a6f88;
  border: 1px solid transparent;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.88rem;
  padding: 8px 16px;
  transition: all 0.25s ease;
}
.btn-logout:hover {
  background: #fdecec;
  color: #dc2626;
  border-color: rgba(220, 53, 69, 0.2);
}

/* ── Contenu ── */
.page-shell {
  min-height: calc(100vh - 62px);
  position: relative;
}

.navbar-toggler { border-color: rgba(102, 126, 234, 0.3); }
.navbar-toggler:focus { box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25); }

@media (max-width: 991.98px) {
  .app-navbar .nav-link { justify-content: flex-start; }
  .navbar-collapse { padding-top: 8px; }
}
</style>