<template>
  <div id="app">
    <!-- Menu de navigation (caché sur la page de connexion) -->
    <nav v-if="$route.path !== '/'" class="navbar navbar-expand-lg navbar-dark bg-gradient-primary mb-4">
      <div class="container">
        <a class="navbar-brand" href="#">
          <i class="bi bi-person-badge-fill me-2"></i>GestionRH
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link class="nav-link" to="/ajout">
                <i class="bi bi-plus-circle-fill me-1"></i>Ajouter
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/liste">
                <i class="bi bi-list-ul me-1"></i>Liste
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/bilan">
                <i class="bi bi-bar-chart-fill me-1"></i>Bilan
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Affichage de la page courante -->
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
/* Style du dégradé */
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea, #764ba2) !important;
}

.navbar-brand {
  font-weight: bold;
  font-size: 1.2rem;
}

.nav-link {
  font-weight: 500;
  transition: 0.3s;
}

.nav-link:hover {
  transform: scale(1.05);
  text-shadow: 0 0 10px rgba(255,255,255,0.3);
}
</style>