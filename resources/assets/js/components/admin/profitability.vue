<template>
    <div id="profitability">
        <div class="container-fluid">
            <h1>Rentabilidad</h1>
            <div v-if="isAdmin" class="horizontal-form">
                <div class="form-group row">
                    <label class="control-label col-sm-2">Laboratorio:</label>
                    <div class="col-sm-10">
                        <select class="form-control" v-model="selectedBranch" @change="refreshData">
                            <option value="null" selected hidden disabled>SELECCIONA UN LABORATORIO</option>
                            <optgroup>
                                <option :value="branch" v-for="branch in branches" :key="branch.id">{{ branch.name }}</option>
                            </optgroup>
                            <optgroup label="_________">
                                <option :value="{ id: 0, name: 'ACUMULADO GENERAL' }">ACUMULADO GENERAL</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="form-group row">
                    <label class="control-label col-sm-2">Periodo:</label>
                    <div class="col-sm-10">
                        <input class="form-control" v-model="selectedPeriod" type="month" @change="refreshData" min="2021-08" :max="actualMonth">
                        </input>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="selectedBranch != null">
            <div class="col-xs-9">
                <div class="small" v-if="data != null">
                    <Chart :chart-data="data" :options="options" v-if="showChart"></Chart>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="info-graph">
                    <b>Objetivo</b>
                    <span class="text-info">{{ base | currency }}</span>
                    <b>Progreso</b>
                    <span :class="{ 'text-danger': progress <= 0, 'text-success': progress > 0}">{{ progress | currency }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Chart from './Chart.js';
import * as moment from "moment";
export default {
    components: {
        Chart
    },
    data: () => ({
        selectedBranch: null,
        branches: [],
        data: null,
        base: 0.0,
        progress: 0.0,
        selectedPeriod: null,
        actualMonth: null,
        showChart: true,
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                   ticks: {
                       min: 0,
                       max: 100,
                       callback: function(value) {
                           return '$ ' + value.toLocaleString('us', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                       }
                   },
                   scaleLabel: {
                       display: true,
                       labelString: "Alcance"
                   }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return '$ ' + tooltipItem.yLabel.toLocaleString('us', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                    }
                }
            },
            lineAt: 10000,
            annotation: {
              drawTime: "afterDraw",
              annotations: []
            },
            plugins: {
       annotation: {
          annotations: [
          {
            type: 'line',
            mode: 'vertical',
            scaleID: 'x-axis-0',
            value: '2020-10-05',
            borderColor: 'red',
            borderWidth: 2,
            label: {
              content: 'TODAY',
              enabled: true,
              position: 'top'
            }
          }
        ]
      }
    }
        }
    }),
    computed: {
        isAdmin: function() {
            let roles = this.$parent.user.roles;
            for(let role of roles)
                if(role.name == 'Administrador Sr' || role.name == 'Administrador Jr')
                    return true;
            return false;
        }
    },
    methods: {
        refreshData () {
            if(this.selectedBranch) {
                this.$parent.inPetition = true;
                let period = this.selectedPeriod;
                axios.post(`/api/branches/${ this.selectedBranch.id }/profitability`, { period }).then(result => {
                    this.fillData(result.data.data);
                    this.$parent.inPetition = false;
                });
            } else {
                alert('Selecciona un PDV primero');
            }
        },
        fillData(data) {
            this.showChart = false;
            this.$parent.inPetition = true;
            let labels = [];
            let datasets = [];
            let optionsCount = {};
            let dataSetData = [];

            let maxValue = 0;

            for(let i in data.dataset) {
                labels.push(moment(i).format('DD'));
                dataSetData.push(data.dataset[i]);

                maxValue = Math.max(data.dataset[i], maxValue);
            }

            datasets.push({
                label: this.selectedBranch.name,
                backgroundColor: this.getRandomColor(),
                data: dataSetData,
                fill: false,
                tension: 0.1
            });

            this.data = { labels, datasets };
            this.options.scales.yAxes[0].ticks.max = data.topLimit;
            this.options.lineAt = data.base;

            this.base = data.base;
            this.progress = maxValue - this.base;

            setTimeout(() => {
                this.showChart = true;
                this.$parent.inPetition = false;
            }, 500);
        },
        getBranches(){
            this.$parent.inPetition = true;
            axios.get(tools.url("/api/branches")).then((response)=>{
                this.branches = response.data.filter(b => parseInt(b.base) != 0);
                this.$parent.inPetition = false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getRandomInt () {
            return Math.floor(Math.random() * (50 - 5 + 1)) + 5
        },
        getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
    },
    mounted() {
        this.selectedPeriod = moment().format('YYYY-MM');
        this.actualMonth = this.selectedPeriod;
        this.getBranches();
        if(!this.isAdmin) {
            this.selectedBranch = this.$parent.user.branch;
            this.refreshData();
        }
    }
}
</script>

<style lang="scss" scoped>
.small {
    max-width: 700px;
    // max-height: 600px !important;
    margin: 0px auto;
}

.info-graph {
    text-align: center;
    b, span {
        font-weight: bold;
        display: block;
        font-size: 24px;
    }

    @media all and (min-width: 800px) {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 700px;
        flex-direction: column;
    }
}
</style>
