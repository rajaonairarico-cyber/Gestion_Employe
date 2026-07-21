<template>
  <div class="container py-5">

    <!-- ════════════ BARRE DE NOTIFICATIONS ════════════ -->
    <transition-group name="notif" tag="div" class="notif-stack mb-4">
      <div
        v-for="notif in notifications"
        :key="notif.id"
        class="notif-card"
        :class="notif.type"
      >
        <div class="notif-icon">
          <i class="bi" :class="notif.icon"></i>
        </div>
        <div class="notif-body">
          <strong>{{ notif.titre }}</strong>
          <p>{{ notif.message }}</p>
        </div>
        <button class="notif-close" @click="dismissNotif(notif.id)">
          <i class="bi bi-x"></i>
        </button>
        <div class="notif-progress" :style="{ animationDuration: notif.duration + 'ms' }"></div>
      </div>
    </transition-group>

    <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInUp">

      <!-- En-tête -->
      <div class="card-header bg-gradient-primary-to-secondary text-white border-0 py-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center">
            <div class="icon-circle me-3">
              <i class="bi" :class="isArchived ? 'bi-archive-fill' : 'bi-people-fill'"></i>
            </div>
            <div>
              <h4 class="mb-0 fw-bold">{{ isArchived ? 'Archives' : 'Liste des employés' }}</h4>
              <p class="mb-0 opacity-75 small">{{ filteredEmployes.length }} employé(s)</p>
            </div>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm rounded-3 px-3"
              :class="!isArchived ? 'btn-light text-primary fw-bold' : 'btn-outline-light'"
              @click="isArchived = false">
              <i class="bi bi-person me-1"></i> Actifs
            </button>
            <button type="button" class="btn btn-sm rounded-3 px-3"
              :class="isArchived ? 'btn-light text-danger fw-bold' : 'btn-outline-light'"
              @click="isArchived = true">
              <i class="bi bi-archive me-1"></i> Archives
            </button>
          </div>
        </div>
      </div>

      <!-- Corps -->
      <div class="card-body p-4 p-lg-5">

        <!-- Barre de recherche -->
        <div class="row mb-4">
          <div class="col-md-6 col-lg-5">
            <div class="input-group rounded-3 shadow-sm">
              <span class="input-group-text bg-white border-end-0 rounded-start-3">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input type="text" class="form-control border-start-0 rounded-end-3 py-2"
                v-model="searchQuery"
                placeholder="Rechercher par nom, emploi ou numéro..."
                aria-label="Rechercher un employé">
              <button v-if="searchQuery" class="btn btn-outline-secondary rounded-end-3"
                @click="searchQuery = ''" type="button">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <div class="col-md-6 col-lg-7 text-md-end mt-2 mt-md-0">
            <small class="text-muted">
              <i class="bi bi-funnel me-1"></i>
              {{ filteredEmployes.length }} résultat(s) sur {{ employes.length }}
            </small>
          </div>
        </div>

        <!-- Tableau -->
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">N°</th>
                <th class="ps-3">Nom</th>
                <th class="text-center">Emploi</th>
                <th class="text-center">Contrat</th>
                <th class="text-center">Jours réels</th>
                <th class="text-center">Taux</th>
                <th class="text-center">Salaire</th>
                <th class="text-center">Fin</th>
                <th class="text-center">Statut</th>
                <th class="text-end pe-3">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="emp in filteredEmployes"
                :key="emp.id"
                class="animate__animated animate__fadeIn"
                :class="rowClass(emp)"
              >
                <td class="ps-3">
                  <span class="badge bg-dark rounded-pill">{{ emp.numEmp }}</span>
                </td>
                <td class="ps-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle"
                      :class="isArchived ? 'bg-danger-10 text-danger' : 'bg-primary-10 text-primary'">
                      <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="ms-2">
                      <span class="fw-semibold d-block">{{ emp.nom }}</span>
                      <span v-if="!isArchived" class="badge badge-suivi" :class="suiviBadgeClass(emp)">
                        {{ suiviLabel(emp) }}
                      </span>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-info text-dark">{{ emp.emploi }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-secondary rounded-pill">{{ emp.nb_jours }}j</span>
                </td>
                <td class="text-center">
                  <span class="badge rounded-pill"
                    :class="joursReels(emp) >= emp.nb_jours ? 'bg-success' : 'bg-warning text-dark'">
                    {{ joursReels(emp) }}j / {{ emp.nb_jours }}j
                  </span>
                  <div class="progress mt-1" style="height:4px; min-width:70px;">
                    <div class="progress-bar"
                      :class="joursReels(emp) >= emp.nb_jours ? 'bg-success' : 'bg-warning'"
                      :style="{ width: Math.min(100, (joursReels(emp)/emp.nb_jours)*100) + '%' }">
                    </div>
                  </div>
                </td>
                <td class="text-center">{{ emp.taux_journalier }} Ar</td>
                <td class="text-center">
                  <span class="badge bg-success rounded-pill px-3">
                    {{ (emp.nb_jours * emp.taux_journalier).toFixed(2) }} Ar
                  </span>
                </td>
                <td class="text-center">
                  <small class="text-muted">
                    <i class="bi bi-calendar me-1"></i>{{ formatDate(emp.date_fin_contrat) }}
                  </small>
                </td>
                <td class="text-center">
                  <span v-if="emp.statut_paiement === 'Payé'" class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i>Payé
                  </span>
                  <span v-else-if="peutEtrePaye(emp)" class="badge bg-danger animate__animated animate__pulse animate__infinite">
                    <i class="bi bi-exclamation-circle me-1"></i>À payer
                  </span>
                  <span v-else class="badge bg-warning text-dark">
                    <i class="bi bi-clock me-1"></i>En cours
                  </span>
                </td>
                <td class="text-end pe-3">
                  <template v-if="!isArchived">
                    <div class="d-flex justify-content-end gap-1">
                      <button
                        v-if="emp.statut_paiement !== 'Payé' && peutEtrePaye(emp)"
                        @click="payEmployee(emp)"
                        class="btn btn-sm btn-success rounded-3 btn-pay-pulse"
                        title="Payer cet employé">
                        <i class="bi bi-cash-coin me-1"></i>Payer
                      </button>
                      <button @click="editEmployee(emp)"
                        class="btn btn-sm btn-outline-primary rounded-3"
                        title="Modifier">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button @click="openArchiveModal(emp.id, emp.nom)"
                        class="btn btn-sm btn-outline-warning rounded-3"
                        title="Archiver">
                        <i class="bi bi-archive"></i>
                      </button>
                    </div>
                  </template>
                  <template v-else>
                    <button @click="restoreEmployee(emp.id)"
                      class="btn btn-sm btn-outline-success rounded-3 me-1" title="Restaurer">
                      <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    <button @click="openPermanentDeleteModal(emp.id, emp.nom)"
                      class="btn btn-sm btn-outline-danger rounded-3" title="Supprimer définitivement">
                      <i class="bi bi-trash"></i>
                    </button>
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Liste vide -->
        <div v-if="filteredEmployes.length === 0" class="text-center py-5 text-muted">
          <i class="bi" :class="isArchived ? 'bi-archive' : 'bi-people'" style="font-size: 3rem;"></i>
          <p class="mt-3 fs-5">{{ isArchived ? 'Aucun employé archivé' : 'Aucun employé enregistré' }}</p>
        </div>

        <!-- Formulaire de modification -->
        <transition name="slide-fade">
          <div v-if="editing" class="mt-5 p-4 bg-light rounded-4 border shadow-sm">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-circle-sm bg-primary-10 text-primary me-2">
                <i class="bi bi-pencil-fill"></i>
              </div>
              <h5 class="mb-0 fw-bold">Modifier l'employé</h5>
              <button type="button" class="btn-close ms-auto" @click="editing = false"></button>
            </div>

            <!-- Info box : logique paiement -->
            <div class="alert alert-info border-0 rounded-3 mb-3 py-2 px-3 small">
              <i class="bi bi-info-circle me-1"></i>
              <strong>Logique de paiement :</strong> Le paiement sera disponible dès que les jours travaillés réels
              (depuis la date d'ajout jusqu'à aujourd'hui) atteignent le nombre de jours du contrat.
              <br>
              <span v-if="editForm.date_ajout">
                Jours travaillés actuels depuis le
                <strong>{{ formatDateShort(editForm.date_ajout) }}</strong> :
                <strong class="text-primary">{{ joursDepuis(editForm.date_ajout) }} jours</strong>.
                Avec <strong>{{ editForm.nb_jours }} jours</strong> de contrat →
                <span :class="joursDepuis(editForm.date_ajout) >= editForm.nb_jours ? 'text-success fw-bold' : 'text-warning fw-bold'">
                  {{ joursDepuis(editForm.date_ajout) >= editForm.nb_jours ? '✅ Paiement autorisé' : '⏳ Paiement en attente' }}
                </span>
              </span>
            </div>

            <form @submit.prevent="updateEmployee">
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="form-floating">
                    <input v-model="editForm.nom" type="text" class="form-control rounded-3" id="editNom" required>
                    <label for="editNom">Nom</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating">
                    <input v-model.number="editForm.nb_jours" type="number" class="form-control rounded-3" id="editJours" required>
                    <label for="editJours">Nombre de jours (contrat)</label>
                    <small v-if="previewDateFin" class="text-muted d-block mt-1">
                      <i class="bi bi-calendar-event me-1"></i>Nouvelle fin : {{ previewDateFin }}
                    </small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating">
                    <input v-model="editForm.taux_journalier" type="number" step="0.01" class="form-control rounded-3" id="editTaux" required>
                    <label for="editTaux">Taux journalier (Ar)</label>
                  </div>
                </div>
              </div>
              <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-3">
                  <i class="bi bi-save me-2"></i>Enregistrer
                </button>
                <button type="button" class="btn btn-secondary rounded-3" @click="editing = false">
                  <i class="bi bi-x-circle me-2"></i>Annuler
                </button>
              </div>
              <p v-if="editMessage" class="mt-2 fw-semibold" :class="editMessageClass">
                <i class="bi" :class="editMessageIcon"></i> {{ editMessage }}
              </p>
            </form>
          </div>
        </transition>

        <!-- Message global -->
        <transition name="slide-fade">
          <div v-if="message" class="alert mt-4 rounded-4 border-0" :class="messageClass">
            <i class="bi" :class="messageIcon"></i> {{ message }}
          </div>
        </transition>
      </div>
    </div>

    <!-- Modal : Archivage -->
    <div class="modal fade" id="archiveModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
          <div class="modal-header bg-warning text-white border-0 rounded-top-5 py-3">
            <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmation</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center p-5">
            <div class="icon-circle-large bg-warning-10 text-warning mb-3">
              <i class="bi bi-archive fs-1"></i>
            </div>
            <h5 class="text-warning fw-bold">Archiver cet employé ?</h5>
            <p class="text-muted mb-0">
              Vous êtes sur le point d'archiver <strong>{{ archiveName }}</strong>.
              <br>Il pourra être restauré ultérieurement.
            </p>
          </div>
          <div class="modal-footer justify-content-center border-0 pb-4">
            <button type="button" class="btn btn-secondary px-4 rounded-4" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-2"></i> Annuler
            </button>
            <button type="button" class="btn btn-warning px-4 rounded-4" @click="confirmArchive">
              <i class="bi bi-archive me-2"></i> Archiver
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal : Suppression définitive -->
    <div class="modal fade" id="permanentDeleteModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
          <div class="modal-header bg-danger text-white border-0 rounded-top-5 py-3">
            <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i> Suppression définitive</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center p-5">
            <div class="icon-circle-large bg-danger-10 text-danger mb-3">
              <i class="bi bi-trash fs-1"></i>
            </div>
            <h5 class="text-danger fw-bold">Supprimer définitivement ?</h5>
            <p class="text-muted mb-0">
              Vous êtes sur le point de supprimer <strong>{{ permanentDeleteName }}</strong>.
              <br>Cette action est <strong>irréversible</strong>.
            </p>
          </div>
          <div class="modal-footer justify-content-center border-0 pb-4">
            <button type="button" class="btn btn-secondary px-4 rounded-4" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-2"></i> Annuler
            </button>
            <button type="button" class="btn btn-danger px-4 rounded-4" @click="confirmPermanentDelete">
              <i class="bi bi-trash me-2"></i> Supprimer définitivement
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
      employes: [],
      isArchived: false,
      searchQuery: '',
      editing: false,
      editForm: {
        id: null,
        nom: '',
        nb_jours: '',
        taux_journalier: '',
        date_ajout: null
      },
      message: '',
      messageType: 'success',
      editMessage: '',
      editMessageType: 'success',
      previewDateFin: '',
      archiveId: null,
      archiveName: '',
      permanentDeleteId: null,
      permanentDeleteName: '',
      refreshInterval: null,
      notifications: [],
      notifIdCounter: 0,
      notifiesDejaEmises: new Set()
    };
  },

  computed: {
    filteredEmployes() {
      let result = this.employes;
      if (this.searchQuery.trim()) {
        const q = this.searchQuery.toLowerCase().trim();
        result = result.filter(emp =>
          (emp.nom && emp.nom.toLowerCase().includes(q)) ||
          (emp.emploi && emp.emploi.toLowerCase().includes(q)) ||
          (emp.numEmp && emp.numEmp.toString().includes(q))
        );
      }
      return result;
    },
    messageClass() { return this.messageType === 'success' ? 'alert-success' : 'alert-danger'; },
    messageIcon()  { return this.messageType === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; },
    editMessageClass() { return this.editMessageType === 'success' ? 'text-success' : 'text-danger'; },
    editMessageIcon()  { return this.editMessageType === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; }
  },

  mounted() {
    this.fetchEmployes();
    this.refreshInterval = setInterval(() => this.fetchEmployes(), 60000);
  },

  beforeDestroy() {
    if (this.refreshInterval) clearInterval(this.refreshInterval);
  },

  watch: {
    isArchived() {
      this.searchQuery = '';
      this.fetchEmployes();
    },
    'editForm.nb_jours': function(newJours) {
      if (this.editForm.date_ajout) {
        const d = new Date(this.editForm.date_ajout);
        if (!isNaN(d)) {
          const fin = new Date(d);
          fin.setDate(d.getDate() + Number(newJours));
          this.previewDateFin = this.formatDate(fin);
        }
      }
    }
  },

  methods: {

    // ════════════════════════════════
    //  LOGIQUE JOURS / PAIEMENT
    // ════════════════════════════════

    joursDepuis(dateAjout) {
      if (!dateAjout) return 0;
      const debut = new Date(dateAjout);
      debut.setHours(0, 0, 0, 0);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return Math.max(0, Math.floor((today - debut) / (1000 * 60 * 60 * 24)));
    },

    joursReels(emp) {
      return this.joursDepuis(emp.date_ajout);
    },

    peutEtrePaye(emp) {
      if (emp.statut_paiement === 'Payé') return false;
      return this.joursReels(emp) >= Number(emp.nb_jours);
    },

    // ════════════════════════════════
    //  SUIVI / BADGE
    // ════════════════════════════════

    suiviLabel(emp) {
      if (emp.statut_paiement === 'Payé') return '✅ Payé';
      const reels = this.joursReels(emp);
      const contrat = Number(emp.nb_jours);
      if (reels >= contrat) return '🔴 Paiement dû';
      const restant = contrat - reels;
      if (restant <= 3) return `⚠️ ${restant}j restant${restant > 1 ? 's' : ''}`;
      return `📅 ${reels}j / ${contrat}j`;
    },

    suiviBadgeClass(emp) {
      if (emp.statut_paiement === 'Payé') return 'badge-suivi-paye';
      const reels = this.joursReels(emp);
      const contrat = Number(emp.nb_jours);
      if (reels >= contrat) return 'badge-suivi-du';
      if (contrat - reels <= 3) return 'badge-suivi-proche';
      return 'badge-suivi-encours';
    },

    rowClass(emp) {
      if (this.isArchived) return '';
      if (emp.statut_paiement === 'Payé') return '';
      if (this.peutEtrePaye(emp)) return 'table-danger';
      const restant = Number(emp.nb_jours) - this.joursReels(emp);
      if (restant <= 3) return 'table-warning';
      return '';
    },

    // ════════════════════════════════
    //  NOTIFICATIONS
    // ════════════════════════════════

    pushNotif({ titre, message, type = 'info', icon = 'bi-bell-fill', duration = 6000 }) {
      const id = ++this.notifIdCounter;
      this.notifications.push({ id, titre, message, type, icon, duration });
      setTimeout(() => this.dismissNotif(id), duration);
    },

    dismissNotif(id) {
      this.notifications = this.notifications.filter(n => n.id !== id);
    },

    analyserNotifications() {
      if (this.isArchived) return;
      this.employes.forEach(emp => {
        if (emp.statut_paiement === 'Payé') return;

        const reels = this.joursReels(emp);
        const contrat = Number(emp.nb_jours);
        const key = `${emp.id}-paiement-du`;
        const keyProche = `${emp.id}-fin-proche`;

        if (reels >= contrat && !this.notifiesDejaEmises.has(key)) {
          this.notifiesDejaEmises.add(key);
          this.pushNotif({
            titre: `💰 Paiement dû — ${emp.nom}`,
            message: `Le contrat de ${emp.nom} est terminé (${reels} jours écoulés sur ${contrat}). Veuillez procéder au paiement.`,
            type: 'danger',
            icon: 'bi-exclamation-circle-fill',
            duration: 10000
          });
        }

        const restant = contrat - reels;
        if (restant > 0 && restant <= 3 && !this.notifiesDejaEmises.has(keyProche)) {
          this.notifiesDejaEmises.add(keyProche);
          this.pushNotif({
            titre: `⚠️ Fin de contrat proche — ${emp.nom}`,
            message: `Il reste ${restant} jour${restant > 1 ? 's' : ''} avant la fin du contrat de ${emp.nom} (${reels}j / ${contrat}j).`,
            type: 'warning',
            icon: 'bi-clock-fill',
            duration: 8000
          });
        }
      });
    },

    // ════════════════════════════════
    //  CRUD
    // ════════════════════════════════

    async fetchEmployes() {
      try {
        const url = this.isArchived
          ? 'http://localhost/projet_employe/backend/employes.php?archived=true'
          : 'http://localhost/projet_employe/backend/employes.php';
        const response = await axios.get(url);
        this.employes = response.data;
        this.message = '';
        // ✅ Réinitialisation du set de notifications pour les actifs
        if (!this.isArchived) {
          this.notifiesDejaEmises.clear();
          this.analyserNotifications();
        }
      } catch (error) {
        this.message = 'Erreur lors du chargement des employés.';
        this.messageType = 'danger';
      }
    },

    formatDate(dateString) {
      if (!dateString) return '-';
      return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
      });
    },

    formatDateShort(dateString) {
      if (!dateString) return '-';
      return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric'
      });
    },

    editEmployee(emp) {
      // ✅ Petit correctif : empêcher modification si déjà payé
      if (emp.statut_paiement === 'Payé') {
        this.pushNotif({
          titre: '⛔ Modification impossible',
          message: `${emp.nom} a déjà été payé. Vous ne pouvez plus modifier son contrat.`,
          type: 'warning',
          icon: 'bi-shield-exclamation',
          duration: 5000
        });
        return;
      }
      this.editing = true;
      this.editForm = { ...emp };
      this.previewDateFin = '';
      this.$nextTick(() => {
        const el = document.querySelector('.bg-light.rounded-4');
        if (el) el.scrollIntoView({ behavior: 'smooth' });
      });
    },

    async updateEmployee() {
      try {
        const response = await axios.put('http://localhost/projet_employe/backend/employes.php', this.editForm);
        this.editMessage = response.data.message || 'Modification réussie !';
        this.editMessageType = 'success';
        this.editing = false;
        await this.fetchEmployes();
        this.pushNotif({
          titre: 'Modification enregistrée',
          message: `Le contrat de ${this.editForm.nom} a été mis à jour.`,
          type: 'success',
          icon: 'bi-check-circle-fill'
        });
      } catch (error) {
        this.editMessage = 'Erreur lors de la modification.';
        this.editMessageType = 'danger';
      }
    },

    async payEmployee(emp) {
      try {
        const response = await axios.post(
          'http://localhost/projet_employe/backend/employes.php?pay=true',
          { id: emp.id }
        );
        this.message = response.data.message || 'Paiement effectué avec succès !';
        this.messageType = 'success';
        await this.fetchEmployes();
        this.pushNotif({
          titre: `✅ Paiement effectué`,
          message: `${emp.nom} a été payé avec succès (${(emp.nb_jours * emp.taux_journalier).toFixed(2)} Ar).`,
          type: 'success',
          icon: 'bi-cash-coin',
          duration: 7000
        });
      } catch (error) {
        this.message = 'Erreur lors du paiement.';
        this.messageType = 'danger';
      }
    },

    openArchiveModal(id, name) {
      this.archiveId = id;
      this.archiveName = name;
      new Modal(document.getElementById('archiveModal')).show();
    },

    async confirmArchive() {
      if (!this.archiveId) return;
      try {
        const response = await axios.delete('http://localhost/projet_employe/backend/employes.php', {
          data: { id: this.archiveId }
        });
        this.message = response.data.message || 'Employé archivé avec succès.';
        this.messageType = 'success';
        await this.fetchEmployes();
        Modal.getInstance(document.getElementById('archiveModal')).hide();
        this.archiveId = null;
        this.archiveName = '';
      } catch (error) {
        this.message = "Erreur lors de l'archivage.";
        this.messageType = 'danger';
      }
    },

    async restoreEmployee(id) {
      try {
        const response = await axios.post('http://localhost/projet_employe/backend/employes.php?restore=true', { id });
        this.message = response.data.message || 'Employé restauré avec succès.';
        this.messageType = 'success';
        await this.fetchEmployes();
      } catch (error) {
        this.message = 'Erreur lors de la restauration.';
        this.messageType = 'danger';
      }
    },

    openPermanentDeleteModal(id, name) {
      this.permanentDeleteId = id;
      this.permanentDeleteName = name;
      new Modal(document.getElementById('permanentDeleteModal')).show();
    },

    async confirmPermanentDelete() {
      if (!this.permanentDeleteId) return;
      try {
        const response = await axios.delete('http://localhost/projet_employe/backend/employes.php?permanent=true', {
          data: { id: this.permanentDeleteId }
        });
        this.message = response.data.message || 'Employé supprimé définitivement.';
        this.messageType = 'success';
        await this.fetchEmployes();
        Modal.getInstance(document.getElementById('permanentDeleteModal')).hide();
        this.permanentDeleteId = null;
        this.permanentDeleteName = '';
      } catch (error) {
        this.message = 'Erreur lors de la suppression définitive.';
        this.messageType = 'danger';
      }
    }
  }
};
</script>

<style scoped>
/* ── Gradient header ── */
.bg-gradient-primary-to-secondary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* ── Icônes ── */
.icon-circle {
  width: 48px; height: 48px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
}
.icon-circle-sm {
  width: 32px; height: 32px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%;
}
.icon-circle-large {
  width: 80px; height: 80px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%;
  margin: 0 auto;
}

/* ── Couleurs utilitaires ── */
.bg-primary-10 { background: rgba(13,110,253,0.1); }
.bg-danger-10  { background: rgba(220,53,69,0.1); }
.bg-warning-10 { background: rgba(255,193,7,0.1); }

.avatar-circle {
  width: 36px; height: 36px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%;
}

/* ── Badges de suivi ── */
.badge-suivi {
  font-size: 0.65rem;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 10px;
  letter-spacing: 0.03em;
}
.badge-suivi-paye    { background: #d1fae5; color: #065f46; }
.badge-suivi-du      { background: #fee2e2; color: #991b1b; animation: pulse-red 1.2s infinite; }
.badge-suivi-proche  { background: #fef3c7; color: #92400e; }
.badge-suivi-encours { background: #e0e7ff; color: #3730a3; }

@keyframes pulse-red {
  0%, 100% { box-shadow: 0 0 0 0 rgba(220,53,69,0.4); }
  50%       { box-shadow: 0 0 0 5px rgba(220,53,69,0); }
}

/* ── Bouton payer ── */
.btn-pay-pulse {
  animation: pulse-green 1.5s infinite;
}
@keyframes pulse-green {
  0%, 100% { box-shadow: 0 0 0 0 rgba(25,135,84,0.5); }
  50%       { box-shadow: 0 0 0 6px rgba(25,135,84,0); }
}

/* ── NOTIFICATIONS ── */
.notif-stack {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 380px;
  width: 100%;
}

.notif-card {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 14px;
  background: #fff;
  box-shadow: 0 8px 30px rgba(0,0,0,0.14);
  position: relative;
  overflow: hidden;
  border-left: 4px solid #6366f1;
  animation: slideInRight 0.35s ease;
}
.notif-card.success { border-left-color: #22c55e; }
.notif-card.warning { border-left-color: #f59e0b; }
.notif-card.danger  { border-left-color: #ef4444; }
.notif-card.info    { border-left-color: #6366f1; }

.notif-icon {
  font-size: 1.3rem;
  padding-top: 2px;
}
.notif-card.success .notif-icon { color: #22c55e; }
.notif-card.warning .notif-icon { color: #f59e0b; }
.notif-card.danger  .notif-icon { color: #ef4444; }
.notif-card.info    .notif-icon { color: #6366f1; }

.notif-body {
  flex: 1;
}
.notif-body strong {
  font-size: 0.85rem;
  display: block;
  margin-bottom: 2px;
}
.notif-body p {
  font-size: 0.78rem;
  margin: 0;
  color: #555;
  line-height: 1.4;
}

.notif-close {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  color: #aaa;
  padding: 0;
  line-height: 1;
  flex-shrink: 0;
}
.notif-close:hover { color: #333; }

/* Barre de progression auto-disparition */
.notif-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  width: 100%;
  background: currentColor;
  opacity: 0.3;
  animation: shrink linear forwards;
  transform-origin: left;
}
.notif-card.success .notif-progress { color: #22c55e; }
.notif-card.warning .notif-progress { color: #f59e0b; }
.notif-card.danger  .notif-progress { color: #ef4444; }
.notif-card.info    .notif-progress { color: #6366f1; }

@keyframes shrink {
  from { transform: scaleX(1); }
  to   { transform: scaleX(0); }
}
@keyframes slideInRight {
  from { transform: translateX(100%); opacity: 0; }
  to   { transform: translateX(0);    opacity: 1; }
}

/* Transitions Vue notif */
.notif-enter-active { animation: slideInRight 0.35s ease; }
.notif-leave-active { animation: slideInRight 0.25s ease reverse; }

/* ── Transitions formulaire ── */
.slide-fade-enter-active, .slide-fade-leave-active {
  transition: all 0.3s ease;
}
.slide-fade-enter-from, .slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}

/* ── Tableau ── */
.table-hover tbody tr:hover {
  background: rgba(13,110,253,0.03);
}
.table-warning { background-color: #fff3cd !important; }
.table-danger  { background-color: #ffe0e0 !important; }
</style>