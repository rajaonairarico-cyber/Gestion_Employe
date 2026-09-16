<template>
  <div class="container py-5">
    <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInUp">
      <div class="stats-header">
        <div class="header-blob blob-1"></div>
        <div class="header-blob blob-2"></div>
        <div class="d-flex align-items-center position-relative">
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
            <div class="stat-card stat-total rounded-4 p-4 text-center h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
              <div class="stat-icon">
                <i class="bi bi-currency-exchange"></i>
              </div>
              <h6 class="stat-label">Salaire Total</h6>
              <h3 class="stat-value">{{ total }}</h3>
              <div class="stat-unit">Ariary (Ar)</div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-card stat-min rounded-4 p-4 text-center h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
              <div class="stat-icon">
                <i class="bi bi-arrow-down-circle"></i>
              </div>
              <h6 class="stat-label">Salaire Minimum</h6>
              <h3 class="stat-value">{{ min }}</h3>
              <div class="stat-unit">Ariary (Ar)</div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-card stat-max rounded-4 p-4 text-center h-100 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
              <div class="stat-icon">
                <i class="bi bi-arrow-up-circle"></i>
              </div>
              <h6 class="stat-label">Salaire Maximum</h6>
              <h3 class="stat-value">{{ max }}</h3>
              <div class="stat-unit">Ariary (Ar)</div>
            </div>
          </div>
        </div>

        <!-- Graphique amélioré -->
        <div class="row">
          <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white chart-card">
              <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div class="d-flex align-items-center">
                  <div class="icon-circle-sm bg-primary-10 text-primary me-2">
                    <i class="bi bi-bar-chart-fill"></i>
                  </div>
                  <h6 class="mb-0 fw-semibold">Répartition des salaires</h6>
                </div>
                <span class="badge rounded-pill legend-badge">
                  <i class="bi bi-dot text-primary"></i> Ar = Ariary
                </span>
              </div>
              <div class="chart-wrap">
                <canvas id="salaryChart"></canvas>
              </div>
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
import API_BASE from '../config';
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
        const response = await axios.get(`${API_BASE}/stats.php`);
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

      // Dégradés personnalisés
      const gradientTotal = ctx.createLinearGradient(0, 0, 0, 300);
      gradientTotal.addColorStop(0, 'rgba(102, 126, 234, 0.85)');
      gradientTotal.addColorStop(1, 'rgba(102, 126, 234, 0.1)');

      const gradientMin = ctx.createLinearGradient(0, 0, 0, 300);
      gradientMin.addColorStop(0, 'rgba(34, 197, 94, 0.85)');
      gradientMin.addColorStop(1, 'rgba(34, 197, 94, 0.1)');

      const gradientMax = ctx.createLinearGradient(0, 0, 0, 300);
      gradientMax.addColorStop(0, 'rgba(245, 158, 11, 0.85)');
      gradientMax.addColorStop(1, 'rgba(245, 158, 11, 0.1)');

      this.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Total', 'Min', 'Max'],
          datasets: [{
            label: 'Salaires (Ar)',
            data: [this.total, this.min, this.max],
            backgroundColor: [gradientTotal, gradientMin, gradientMax],
            borderColor: ['#667eea', '#22c55e', '#f59e0b'],
            borderWidth: 2,
            borderRadius: 12,
            barPercentage: 0.55
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
              backgroundColor: 'rgba(29,29,43,0.9)',
              titleColor: '#fff',
              bodyColor: '#fff',
              padding: 12,
              cornerRadius: 10,
              boxPadding: 4,
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
                padding: 8,
                callback: function(value) {
                  return value + ' Ar';
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  family: "'Plus Jakarta Sans', sans-serif",
                  weight: 700
                }
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
/* ── En-tête dégradé ── */
.stats-header {
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
.blob-1 { width: 180px; height: 180px; top: -80px; right: -40px; }
.blob-2 { width: 100px; height: 100px; bottom: -50px; left: 60px; background: rgba(255, 255, 255, 0.07); }

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

.bg-white-20 { background: rgba(255, 255, 255, 0.2); }
.bg-primary-10 { background: rgba(102, 126, 234, 0.12); }

/* ── Cartes statistiques colorées ── */
.stat-card {
  position: relative;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 40px rgba(29, 29, 43, 0.14);
}

.stat-total {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}
.stat-min {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: #fff;
}
.stat-max {
  background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
  color: #fff;
}

.stat-icon {
  width: 56px;
  height: 56px;
  margin: 0 auto 14px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-label {
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-size: 0.72rem;
  font-weight: 700;
  opacity: 0.85;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 1.7rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  margin-bottom: 4px;
  word-break: break-word;
}

.stat-unit {
  font-size: 0.72rem;
  font-weight: 600;
  opacity: 0.8;
}

/* ── Carte graphique ── */
.chart-card {
  border: 1px solid var(--line);
  transition: box-shadow 0.3s ease;
}
.chart-card:hover {
  box-shadow: 0 14px 36px rgba(29, 29, 43, 0.10) !important;
}

.legend-badge {
  background: #f1f2fa;
  color: #6a6f88;
  font-weight: 600;
  font-size: 0.72rem;
  padding: 5px 12px;
}

.chart-wrap {
  position: relative;
  height: 320px;
  width: 100%;
}
</style>