<script>
import Portlet from "./Portlet";
/**
 * Projections component
 */
export default {
    data() {
        return {
            colors: ["#3bafda", "#1abc9c", "#f7b84b","#675aa9","#f1556c","#fd7e14","#02a8b5","#98a6ad","#323a46","#f672a7","#37cde6"],
            series1: [],
            chartOptions1: {
                legend: {
                    show: true,
                    position: "bottom",
                    horizontalAlign: "center",
                    verticalAlign: "middle",
                    floating: false,
                    fontSize: "14px",
                    offsetX: 0,
                    offsetY: 7
                },
                labels: [],
                colors: [],
                responsive: [{
                    breakpoint: 600,
                    options: {
                        chart: {
                            height: 240
                        },
                        legend: {
                            show: false
                        }
                    }
                }]
            },
        };
    },
    props: ['statices'],
    watch:{
        statices(newDa,old){
            newDa.unitsWithStatusWithDetailsCount.forEach((el,index) => {
                this.series1.push(el.percentage);
                this.chartOptions1.labels.push(this.$i18n.locale == 'ar'?el.name: el.name_e);
                this.chartOptions1.colors.push(this.colors[index]);
            });
        }
    },
    components:{
        Portlet
    }
};
</script>

<template>
    <div>
        <div class="row">

            <!-- end col -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">

                        <h4 class="header-title">{{ $t('general.unitsWithStatusCount') }}</h4>

                        <div class="mt-3 text-center" dir="ltr">
                            <apexchart
                                class="apex-charts"
                                type="donut"
                                :options="chartOptions1"
                                height="312"
                                :series="series1"
                            >
                            </apexchart>
                        </div>

                        <div class="row mt-3">
                            <div class="col-4" v-for="item in statices.unitsWithStatusWithDetailsCount">
                                <p class="font-15 mb-1 text-truncate">{{ $i18n.locale == 'ar' ?item.name:item.name_e }}</p>
                                <h4>{{ item.count }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->

        </div>
    </div>
    <!-- end row -->
    <!-- end card-box -->
</template>

<style scoped>
.card-body {
    background-color: #bee3fe !important;
}

h1 {
    font-size: 30px;
}

.card-body {
    color: #000 !important;
}

.ordinaryMember{
    min-height: 522px;
}
</style>
