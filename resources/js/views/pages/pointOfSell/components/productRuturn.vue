<template>
  <div>
    <div class="row control">
      <div class="col-lg-12">
        <label class="sr-only" for="inlineFormInputGroup">{{ $t('general.searchOrder') }}</label>
        <div class="mb-2 d-flex">
          <div class="input-group-prepend">
            <div class="input-group-text"><i class="fa fa-search"></i></div>
          </div>
            <multiselect
                v-model="order_id"
                :placeholder="$t('general.searchOrder')"
                :disabled="selectVariants.length > 0? true:false"
                :internalSearch="false"
                @input="orderModel"
                @search-change="searchOrder"
                :options="orders.map((type) => type.id)"
                :custom-label="(opt) => `${orders.find((x) => x.id == opt).prefix} - ${orders.find((x) => x.id == opt).date}`"
            >
            </multiselect>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import adminApi from "../../../../api/adminAxios";
import loader from "../../../../components/general/loader";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";

export default {
   name: "posSalesProduct",
  components: {
    loader,
      Multiselect,
  },
  methods: {
      async searchOrder(e) {
          let search = e??'';
          clearTimeout(this.debounce);
          this.debounce = setTimeout(() => {
              if(search){
                  this.getOrders(search);
              }
          }, 500);
      },
      async getOrders(search='') {
          let order_type = this.type != 'purchase' ? 'sales': 'receiving'
          await adminApi
              .get(`/point-of-sale/oreders/get-return-orders?branch_id=${this.currentBranch.id}&status=1&order_type=${order_type}&limit=10&company_id=${this.company_id}&search=${search}&columns[0]=prefix`)
              .then((res) => {
                  let l = res.data.data;
                  this.orders = l;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      },
      orderModel(){
          let order = this.orders.find(el => el.id == this.order_id);
          if(order){
              this.$store.commit('orderReturn/updateOrder',order);
          }
          this.order_id = null;
          this.orders = [];
      },
  },
  props: ["currency",'type',"selectedBranch"],
  watch: {
      selectedBranch: {
          handler(newV, old) {
              this.currentBranch = newV;
          },
      },
  },
  computed: {
        selectVariants(){
            return this.$store.getters['orderReturn/productDetails'];
        }
    },
  data() {
    return {
      debounce: {},
      isLoader: false,
      orders: [],
      order_id: null,
      currentBranch: null,
    };
  },
};
</script>

<style lang="scss" scoped>
.dropdown-menu {
  width: 410px;
  position: absolute !important;
  will-change: transform !important;
  left: -251px !important;
  padding: 18px 0 0 0;
}
.products {
  position: relative;
  overflow: hidden;
  padding: 0 10px;
  .product {
    &:hover {
      cursor: pointer;
    }
    .out-of-stock {
      font-size: 13px;
      border-radius: 0.25rem;
      padding: 3px;
      z-index: 3;
      color: #ffffff !important;
      background-color: #ffcd4d !important;
      position: absolute;
      top: 33px;
      text-align: center;
    }
    .price {
      align-items: center;
      display: flex;
      width: 64px;
      justify-content: center;
      border-top-right-radius: 0.25rem !important;
      border-bottom-right-radius: 0.25rem !important;
      padding: 2px;
      background: #535561 !important;
      color: #fff;
      left: 22px;
      top: 77px;
      position: absolute;
    }
    margin-bottom: 23px;
    padding: 9px 11px;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    display: flex;
    border-radius: 6px;
    background: #f0f8ff;
    -webkit-box-shadow: 0 1px 15px 1px rgba(60, 55, 68, 0.15);
    box-shadow: 0 1px 15px 1px rgba(60, 55, 68, 0.15);

    &:hover {
      border: 1px solid #60bcff;
    }
    &.active {
      border: 1px solid #60bcff;
    }
    img {
      background: none;
      object-fit: cover;
      height: 130px;
    }
    .title {
      font-weight: bold;
      transition: all 0.5s;
      width: 100%;
      border-radius: 6px;
      padding: 11px;
      min-height: 57px;
      background: #e1f0fd;
      color: #0a8fc0 !important;
      &.standard {
          display: flex;
          justify-content: center;
          align-items: center;
      }
    }
    &:hover .title {
      background: #2494be;
      color: #fff !important;
    }
  }
}
.control {
  .input-group-text {
    color: #0a8fc0 !important;
    font-size: 11px;
  }
  padding-top: 11px;
  .dropdown-toggle {
    align-items: center;
    display: flex;
    justify-content: space-between;
    border-radius: 6px;
    color: #0a8fc0 !important;
    border: 1px solid #0a8fc0 !important;
    width: 100%;
    padding: 9px;
  }
  .form-control {
    padding: 20px 14px;
  }
  margin: 12px 9px;
  background: #f0f8ff;
  -webkit-box-shadow: 0 1px 15px 1px rgba(60, 55, 68, 0.15);
  box-shadow: 0 1px 15px 1px rgba(60, 55, 68, 0.15);
  border-radius: 0.25rem;
  box-shadow: 0 1px 15px 1px rgba(60, 55, 68, 0.15) !important;
}
</style>
