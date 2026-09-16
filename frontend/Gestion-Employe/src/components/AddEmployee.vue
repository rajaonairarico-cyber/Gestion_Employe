<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-xl-7">
        <!-- Carte principale avec animation -->
        <div class="add-card animate__animated animate__fadeInUp">
          <!-- En-tête avec dégradé + décoration -->
          <div class="add-header">
            <div class="header-blob blob-1"></div>
            <div class="header-blob blob-2"></div>
            <div class="d-flex align-items-center position-relative">
              <div class="icon-circle bg-white-20 me-3">
                <i class="bi bi-person-plus-fill fs-3"></i>
              </div>
              <div>
                <h4 class="mb-0 fw-bold">Ajouter un employé</h4>
                <p class="mb-0 opacity-75 small">Remplissez les informations ci-dessous</p>
              </div>
            </div>
          </div>

          <!-- Corps du formulaire -->
          <div class="card-body p-4 p-lg-5">
            <form @submit.prevent="addEmployee">
              <!-- Row 1 : Numéro et Nom -->
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="numEmp" class="form-label fw-semibold text-muted small text-uppercase tracking-wide">
                      <i class="bi bi-hash me-2 primary-accent"></i>Numéro employé
                    </label>
                    <div class="input-group has-validation">
                      <input
                        v-model="numEmp"
                        type="text"
                        class="form-control rounded-4 border-2 p-3"
                        id="numEmp"
                        placeholder="Ex: E003"
                        required
                        :class="{ 'is-invalid': errorNumEmp }"
                        @input="errorNumEmp = false"
                      >
                      <div class="invalid-feedback">
                        <i class="bi bi-exclamation-circle me-1"></i>Ce numéro est déjà utilisé
                      </div>
                    </div>
                    <div class="mt-2">
                      <button type="button" class="btn btn-sm btn-soft-primary rounded-3" @click="suggestNextNum">
                        <i class="bi bi-lightning-charge-fill me-1"></i>Suggérer un numéro libre
                      </button>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="nom" class="form-label fw-semibold text-muted small text-uppercase tracking-wide">
                      <i class="bi bi-person me-2 primary-accent"></i>Nom
                    </label>
                    <input
                      v-model="nom"
                      type="text"
                      class="form-control rounded-4 border-2 p-3"
                      id="nom"
                      placeholder="Nom complet"
                      required
                    >
                  </div>
                </div>
              </div>

              <!-- Row 2 : Emploi et Jours -->
              <div class="row g-4 mt-2">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="emploi" class="form-label fw-semibold text-muted small text-uppercase tracking-wide">
                      <i class="bi bi-briefcase me-2 primary-accent"></i>Emploi
                    </label>
                    <select
                      v-model="emploi"
                      class="form-select rounded-4 border-2 p-3"
                      id="emploi"
                      required
                      @change="updateTauxJournalier"
                    >
                      <option value="" disabled selected>Choisissez un emploi</option>
                      <option v-for="(job, index) in emploisList" :key="index" :value="job.nom">
                        {{ job.nom }}
                      </option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="nbJours" class="form-label fw-semibold text-muted small text-uppercase tracking-wide">
                      <i class="bi bi-calendar3 me-2 primary-accent"></i>Nombre de jours
                    </label>
                    <input
                      v-model="nb_jours"
                      type="number"
                      class="form-control rounded-4 border-2 p-3"
                      id="nbJours"
                      placeholder="Ex: 22"
                      required
                    >
                  </div>
                </div>
              </div>

              <!-- Row 3 : Taux journalier (auto) -->
              <div class="row g-4 mt-2">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="taux" class="form-label fw-semibold text-muted small text-uppercase tracking-wide">
                      <i class="bi bi-currency-exchange me-2 primary-accent"></i>Taux journalier (Ar)
                    </label>
                    <input
                      v-model="taux_journalier"
                      type="number"
                      step="0.01"
                      class="form-control rounded-4 border-2 p-3 bg-light"
                      id="taux"
                      placeholder="Auto"
                      required
                      readonly
                    >
                  </div>
                </div>
              </div>

              <!-- Bouton principal -->
              <div class="mt-5">
                <button
                  type="submit"
                  class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow-sm hover-shadow-lg transition-all position-relative overflow-hidden"
                  :disabled="isLoading"
                >
                  <span v-if="!isLoading">
                    <i class="bi bi-plus-circle me-2"></i>Ajouter l'employé
                  </span>
                  <span v-else class="d-flex align-items-center justify-content-center">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Ajout en cours...
                  </span>
                </button>
              </div>

              <!-- Message feedback -->
              <transition name="slide-fade">
                <div v-if="message" class="alert mt-4 rounded-4 border-0 shadow-sm" :class="messageClass">
                  <i class="bi" :class="messageIcon"></i> {{ message }}
                </div>
              </transition>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal erreur doublon -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
          <div class="modal-header bg-danger text-white border-0 rounded-top-5 py-3">
            <h5 class="modal-title" id="errorModalLabel">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>Erreur
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center p-5">
            <div class="icon-circle-large bg-danger-10 text-danger mb-3">
              <i class="bi bi-person-x-fill fs-1"></i>
            </div>
            <h5 class="text-danger fw-bold">Numéro déjà utilisé !</h5>
            <p class="text-muted mb-0">
              Le numéro employé <strong>{{ numEmp }}</strong> existe déjà.
              <br>Veuillez choisir un autre numéro.
            </p>
          </div>
          <div class="modal-footer justify-content-center border-0 pb-4">
            <button type="button" class="btn btn-primary px-5 rounded-4" data-bs-dismiss="modal">
              <i class="bi bi-check-circle me-2"></i>Compris
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
  data() {
    return {
      numEmp: '',
      nom: '',
      emploi: '',
      nb_jours: '',
      taux_journalier: '',
      message: '',
      messageType: 'success',
      isLoading: false,
      errorNumEmp: false,
      emploisList: [
        { nom: 'Développeur', taux: 55000 },
        { nom: 'Designer', taux: 45000 },
        { nom: 'Manager', taux: 40000 },
        { nom: 'Commercial', taux: 38000 },
        { nom: 'Assistant', taux: 35000 }
      ]
    }
  },
  computed: {
    messageClass() {
      return this.messageType === 'success' ? 'alert-success' : 'alert-danger';
    },
    messageIcon() {
      return this.messageType === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
    }
  },
  mounted() {
    this.suggestNextNum();
  },
  methods: {
    async suggestNextNum() {
      try {
        const response = await axios.get('http://localhost/projet_employe/backend/employes.php');
        const employes = response.data;
        const nums = employes
          .map(emp => {
            const match = emp.numEmp.match(/\d+/);
            return match ? parseInt(match[0]) : 0;
          })
          .filter(n => n > 0);
        const nextNum = nums.length > 0 ? Math.max(...nums) + 1 : 1;
        this.numEmp = 'E' + String(nextNum).padStart(3, '0');
      } catch (error) {
        console.warn('Impossible de suggérer un numéro.');
      }
    },
    updateTauxJournalier() {
      const job = this.emploisList.find(j => j.nom === this.emploi);
      this.taux_journalier = job ? job.taux : '';
    },
    async addEmployee() {
      this.isLoading = true;
      this.errorNumEmp = false;
      this.message = '';

      try {
        const checkResponse = await axios.get(`http://localhost/projet_employe/backend/employes.php?numEmp=${this.numEmp}`);
        if (checkResponse.data.exists) {
          this.errorNumEmp = true;
          const modalElement = document.getElementById('errorModal');
          const modal = new Modal(modalElement);
          modal.show();
          this.isLoading = false;
          return;
        }

        const now = new Date();
        const dateStr = now.toISOString().slice(0, 19).replace('T', ' ');

        await axios.post('http://localhost/projet_employe/backend/employes.php', {
          numEmp: this.numEmp,
          nom: this.nom,
          emploi: this.emploi,
          nb_jours: parseInt(this.nb_jours),
          taux_journalier: parseFloat(this.taux_journalier),
          date_ajout: dateStr
        });

        this.message = 'Employé ajouté avec succès !';
        this.messageType = 'success';
        this.numEmp = '';
        this.nom = '';
        this.emploi = '';
        this.nb_jours = '';
        this.taux_journalier = '';
        this.suggestNextNum();

      } catch (error) {
        this.message = "Une erreur s'est produite lors de l'ajout.";
        this.messageType = 'danger';
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
/* ── Carte principale ── */
.add-card {
  background: #fff;
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

/* ── En-tête dégradé ── */
.add-header {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  padding: 32px 40px;
  overflow: hidden;
}

.header-blob {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.10);
  pointer-events: none;
}
.blob-1 { width: 160px; height: 160px; top: -70px; right: -30px; }
.blob-2 { width: 90px; height: 90px; bottom: -40px; left: 40px; background: rgba(255, 255, 255, 0.07); }

.icon-circle {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
}

/* ── Grand cercle d'icône dans la modal ── */
.icon-circle-large {
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  margin: 0 auto;
}

.bg-danger-10 { background: rgba(220, 53, 69, 0.1); }
.bg-white-20 { background: rgba(255, 255, 255, 0.2); }

/* ── Accents ── */
.primary-accent { color: #667eea; }

/* ── Animation pour les messages ── */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}

/* ── Bouton doux ── */
.btn-soft-primary {
  background: rgba(102, 126, 234, 0.1);
  color: #667eea;
  border: 1px solid rgba(102, 126, 234, 0.2);
}
.btn-soft-primary:hover {
  background: rgba(102, 126, 234, 0.2);
}

/* ── Typographie ── */
.tracking-wide {
  letter-spacing: 0.05em;
}

/* ── Ombre améliorée ── */
.hover-shadow-lg:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>