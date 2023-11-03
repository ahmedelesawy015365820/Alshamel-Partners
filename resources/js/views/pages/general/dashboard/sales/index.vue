<script>
import Layout from "../../../../layouts/main";
import Swal from "sweetalert2";
import WidgetChart from "../../../../../components/widgets/Widget-chart";
import PageHeader from "../../../../../components/general/Page-header";
import RevenueReport from "../../../../../components/widgets/Revenue-report";
import ProductsSales from "../../../../../components/widgets/Products-sales";
import MarketingReports from "../../../../../components/widgets/Marketing-reports";
import Portlet from "../../../../../components/widgets/Portlet";
import RevenueHistory from "../../../../../components/widgets/Revenue-history";
import Projections from "../../../../../components/widgets/Projections"
import adminApi from "../../../../../api/adminAxios";


/**
 * Sales-Dashboard component
 */
export default {
  page: {
    title: "Sales-Dashboard",
      meta: [{ name: "description", content: 'kdjsd' }],
  },
  components: {
    Layout,
    WidgetChart,
    PageHeader,
    RevenueReport,
    ProductsSales,
    MarketingReports,
    Portlet,
    RevenueHistory,
    Projections,
  },
  data() {
    return {
      title: "Welcome !",
      items: [
        {
          text: "Minton",
        },
        {
          text: "Dashboards",
        },
        {
          text: "Sales",
          active: true,
        },
      ],
      tableTitle: "Top Selling Products",
      productData: [
        {
          name: "ASOS Ridley High Waist",
          price: "$79.49",
          quantity: 82,
          amount: "$6,518.18",
          date: "Sep 1, 2018",
          sales: 54,
          productid: 200125,
        },
        {
          name: "Marco Lightweight Shirt",
          price: "$128.50",
          quantity: 37,
          amount: "$4,754.50",
          date: "Sep 15, 2018",
          sales: 28,
          productid: 200130,
        },
        {
          name: "Half Sleeve Shirt",
          price: "$39.99",
          quantity: 64,
          amount: "$2,559.36",
          date: "Nov 1, 2018",
          sales: 55,
          productid: 200140,
        },
        {
          name: "Lightweight Jacket",
          price: "$20.00",
          quantity: 184,
          amount: "$3,680.00",
          date: "Nov 15, 2018",
          sales: 85,
          productid: 200250,
        },
        {
          name: "Marco Shoes",
          price: "$28.49",
          quantity: 69,
          amount: "$1,965.81",
          date: "Jan 1, 2019",
          sales: 49,
          productid: 200256,
        },
        {
          name: "ASOS Ridley High Waist",
          price: "$79.49",
          quantity: 82,
          amount: "$6,518.18",
          date: "Sep 1, 2018",
          sales: 54,
          productid: 200125,
        },
        {
          name: "Half Sleeve Shirt",
          price: "$39.99",
          quantity: 64,
          amount: "$2,559.36",
          date: "Nov 1, 2018",
          sales: 55,
          productid: 200140,
        },
        {
          name: "Lightweight Jacket",
          price: "$20.00",
          quantity: 184,
          amount: "$3,680.00",
          date: "Nov 15, 2018",
          sales: 85,
          productid: 200250,
        },
      ],
      isLoader : false,
      statices: {}
    };
  },
  mounted() {
      this.getStatices();
  },
  methods: {
      getStatices() {
          this.isLoader = true;

          adminApi.get(`/booking/statistics`)
              .then((res) => {
                  let l = res.data.data;
                  this.statices = l;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: 'error',
                      title: `${this.$t('general.Error')}`,
                      text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                  });
              })
              .finally(() => {
                  this.isLoader = false;
              });
      },
  }
};
</script>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />
      <div class="dashboard-fluid">
          <div class="row mt-3">
              <div class="col-xl-3 col-md-6">
                  <WidgetChart
                      :number="statices.statusCount"
                      :text="'departments'"
                      :chart-color="'#1abc9c'"
                  />
              </div>
              <div class="col-xl-3 col-md-6">
                  <WidgetChart
                      :number="statices.UnitsCount"
                      :text="'rooms'"
                      :chart-color="'#f1556c'"
                  />
              </div>
              <!-- end col -->
          </div>

          <Projections :statices="statices"  />
      </div>
  </Layout>
</template>

<style scoped>
.content-page {
    padding: 70px 15px 5px !important;
}
.dashboard-fluid {
    padding: 0 10px !important;
}
</style>
