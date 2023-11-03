<script>
/**
 * Widget-chart component
 */
export default {
    props: {
        number: {
            type: Number,
            required: true,
            default: 0
        },
        chartColor: {
            type: String,
            required: true,
        },
        text: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            chartOptions: {
                chart: {
                    sparkline: {
                        enabled: true,
                    },
                },
                grid: {
                    padding: {
                        top: 10,
                    },
                },
                tooltip: {
                    fixed: {
                        enabled: false,
                    },
                    x: {
                        show: false,
                    },
                    y: {
                        title: {
                            formatter: (seriesName) => {
                                return "";
                            },
                        },
                    },
                    marker: {
                        show: false,
                    },
                },
                stroke: {
                    show: true,
                    curve: "smooth",
                    width: 3,
                },
                colors: [this.chartColor],
            },
            series: [{
                data: [12, 14, 2, 47, 42, 15, 47, 75, 65, 19, 14],
            }, ],
        };
    },
};
</script>
<template>
<div class="card">
    <div class="card-body">
        <div class="float-left" dir="ltr">
            <div>
                <apexchart type="line" width="140" height="54" :series="series" :options="chartOptions"></apexchart>
            </div>
        </div>
        <div class="text-right">
            <h3 class="mb-1">{{ number }}</h3>
            <p class="text-muted mb-1">{{ $t(`general.${text}`) }}</p>
        </div>
    </div>
</div>
</template>
<style scoped>
.card-body {
    background-color: #bee3fe;
    padding: 1.5rem 10px !important;
}
</style>
