<template>
  <div class="container py-5">
    <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInUp">
      <div class="card-header bg-gradient-primary-to-secondary text-white border-0 py-4">
        <div class="d-flex align-items-center">
          <div class="icon-circle bg-white-20 me-3">
            <i class="bi bi-graph-up-arrow fs-3"></i>
          </div>
          <div>
            <h4 class="mb-0 fw-bold">Bilan des salaires</h4>
            <p class="mb-0 opacity-75 small">Vue d'ensemble des rémunérations</p>
          </div>
        </div>
      </div>

      <div class="card-body p-4 p-lg-5">
        <!-- Cartes statistiques -->
        <div class="row g-4 mb-5">
          <div class="col-md-4">
            <div class="stat-card border-0 shadow-sm rounded-4 p-4 text-center h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
              <div class="icon-circle-large bg-primary-10 text-primary mb-3">
                <i class="bi bi-currency-euro fs-2"></i>
              </div>
              <h6 class="text-uppercase text-muted small fw-bold">Salaire Total</h6>
              <h3 class="fw-bold text-primary display-6">{{ total }} Ar</h3>
              <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-primary" style="width: 100%"></div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-card border-0 shadow-sm rounded-4 p-4 text-center h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
              <div class="icon-circle-large bg-success-10 text-success mb-3">
                <i class="bi bi-arrow-down-circle fs-2"></i>
              </div>
              <h6 class="text-uppercase text-muted small fw-bold">Salaire Minimum</h6>
              <h3 class="fw-bold text-success display-6">{{ min }} Ar</h3>
              <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-success" style="width: 60%"></div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-card border-0 shadow-sm rounded-4 p-4 text-center h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
              <div class="icon-circle-large bg-warning-10 text-warning mb-3">
                <i class="bi bi-arrow-up-circle fs-2"></i>
              </div>
              <h6 class="text-uppercase text-muted small fw-bold">Salaire Maximum</h6>
              <h3 class="fw-bold text-warning display-6">{{ max }} Ar</h3>
              <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-warning" style="width: 100%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Graphique amélioré -->
        <div class="row">
          <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
              <div class="d-flex align-items-center mb-4">
                <div class="icon-circle-sm bg-primary-10 text-primary me-2">
                  <i class="bi bi-bar-chart-fill"></i>
                </div>
                <h6 class="mb-0 fw-semibold">Répartition des salaires</h6>
              </div>
              <canvas id="salaryChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
  data() {
    return {
      total: 0,
      min: 0,
      max: 0,
      chart: null
    }
  },
  mounted() {
    this.fetchStats();
    window.addEventListener('resize', this.handleResize);
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy();
    }
    window.removeEventListener('resize', this.handleResize);
  },
  methods: {
    async fetchStats() {
      try {
        const response = await axios.get('http://localhost/projet_employe/backend/stats.php');
        this.total = parseFloat(response.data.total || 0).toFixed(2);
        this.min = parseFloat(response.data.min || 0).toFixed(2);
        this.max = parseFloat(response.data.max || 0).toFixed(2);
        this.createChart();
      } catch (error) {
        console.error('Erreur lors du chargement des stats', error);
      }
    },
    createChart() {
      const ctx = document.getElementById('salaryChart').getContext('2d');
      if (this.chart) {
        this.chart.destroy();
      }

      // Dégradé personnalisé
      const gradient = ctx.createLinearGradient(0, 0, 0, 300);
      gradient.addColorStop(0, 'rgba(102, 126, 234, 0.8)');
      gradient.addColorStop(1, 'rgba(102, 126, 234, 0.1)');

      this.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Total', 'Min', 'Max'],
          datasets: [{
            label: 'Salaires (Ar)',
            data: [this.total, this.min, this.max],
            backgroundColor: gradient,
            borderColor: '#667eea',
            borderWidth: 2,
            borderRadius: 8,
            barPercentage: 0.6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0,0,0,0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              padding: 12,
              cornerRadius: 8,
              callbacks: {
                label: function(context) {
                  return context.parsed.y + ' Ar';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0,0,0,0.05)'
              },
              ticks: {
                callback: function(value) {
                  return value + ' Ar';
                }
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          },
          animation: {
            duration: 1000,
            easing: 'easeInOutQuart'
          }
        }
      });
    },
    handleResize() {
      if (this.chart) {
        this.chart.resize();
      }
    }
  }
}
</script>

<style scoped>
/* Dégradé de l'en-tête */
.bg-gradient-primary-to-secondary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Cercle d'icône */
.icon-circle {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
}

.icon-circle-sm {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.icon-circle-large {
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  margin: 0 auto;
}

.bg-primary-10 { background: rgba(13, 110, 253, 0.1); }
.bg-success-10 { background: rgba(25, 135, 84, 0.1); }
.bg-warning-10 { background: rgba(255, 193, 7, 0.1); }

/* Cartes avec effet hover */
.stat-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.1) !important;
}

/* Progress bar */
.progress {
  background: rgba(0,0,0,0.05);
  border-radius: 10px;
}
.progress-bar {
  border-radius: 10px;
}

/* Canvas height */
canvas {
  max-height: 300px;
}
</style>