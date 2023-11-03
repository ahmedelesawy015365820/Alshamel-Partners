<template>
  <div class="order-container">
    <loader size="large" v-if="isLoader" />
    <PosOrderInvoiceReturn :order="order"  @clear_order="deleteItemTrancute" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"  />
    <div class="control border-bottom" v-if="type != 'purchase'">
      <div class="search">
        <div class="mb-2 d-flex">
          <div class="input-group-prepend">
            <div class="input-group-text"><i class="fa fa-user"></i></div>
          </div>
          <multiselect
              :disabled="true"
              v-model="customer_id"
              :placeholder="$t('general.SearchCustomers')"
              :options="customers.map((type) => type.id)"
              :custom-label="(opt) =>$i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name: customers.find((x) => x.id == opt).name_e"
          >
          </multiselect>
        </div>
      </div>
      <div class="add-customer">
        <button
          type="button"
          disabled
          style="
            color: #fff;
            font-size: 8px;
            padding: 12px 17px;
            background: #3bafda !important;
            border-color: #3bafda !important;
          "
          class="btn btn-secondary"
        >
          <i class="fas fa-user-plus"></i>
        </button>
      </div>
    </div>
    <div v-else class="control border-bottom">
          <div class="search">
              <div class="mb-2 d-flex">
                  <div class="input-group-prepend">
                      <div class="input-group-text"><i class="fa fa-user"></i></div>
                  </div>
                  <multiselect
                      disabled
                      v-model="supplier_id"
                      :placeholder="$t('general.SearchSuppliers')"
                      :options="suppliers.map((type) => type.id)"
                      :custom-label="(opt) =>$i18n.locale == 'ar' ? suppliers.find((x) => x.id == opt).name: suppliers.find((x) => x.id == opt).name_e"
                  >
                  </multiselect>
              </div>
          </div>
          <div class="add-customer">
              <button
                  type="button"
                  disabled
                  style="
                    color: #fff;
                    font-size: 8px;
                    padding: 12px 17px;
                    background: #3bafda !important;
                    border-color: #3bafda !important;
                  "
                  class="btn btn-secondary"
              >
                  <i class="fas fa-user-plus"></i>
              </button>
          </div>
      </div>
    <div class="alert alert-danger text-center" v-if="max_error_variant && type != 'purchase'" role="alert">
          {{ $t('general.Thereisnoquantityinstock') }}
    </div>
    <div class="cart-container border-bottom">
      <template v-if="selectVariants.length > 0">
          <div :id="'accordion-'+index" v-for="(variant,index) in selectVariants" :key="index">
              <template v-if="variant.product_id && variant.product_variant_id">
                  <div :id="'headingOne-'+index">
                      <div class="all-orders border-bottom">
                          <div class="order col-5">
                              <div class="name">
                                  <button
                                      class="btn"
                                      data-toggle="collapse"
                                      :data-target="'#collapseOne-'+index"
                                      aria-expanded="true"
                                      :aria-controls="'#collapseOne-'+index"
                                  >
                                      <i
                                          :class="
                      $i18n.locale == 'en'
                        ? 'fa-solid fa-angle-right'
                        : 'fa-solid fa-angle-left'
                    "
                                      ></i>
                                  </button>
                                  <div>
                                      <div>
                                          {{
                                              $i18n.locale == 'ar' ?
                                                  selectProducts.find(el => el.id == variant.product_id).title.substr(0,26)
                                                  :selectProducts.find(el => el.id == variant.product_id).title_e.substr(0,26)
                                          }}
                                      </div>
                                      <div v-if="variantTitle(variant)">({{ variantTitle(variant) }})</div>
                                  </div>
                              </div>
                          </div>
                          <div class="increment-container">
                              <button
                                  class="btn"
                                  disabled
                                  @click.prevent="false"
                              >
                                  <span>-</span>
                              </button>
                              <span>-{{ variant.qty }}</span>
                              <button
                                  class="btn"
                                  @click.prevent="$store.commit('orderReturn/plusQty',variant.product_variant_id);"
                              >
                                  <span>+</span>
                              </button>
                          </div>
                          <div class="price">
                              <div>
                                  {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
                                  {{
                                  !(variant.discount > 0)?
                                  `-${(variant.price * variant.qty).toFixed(2)}` :
                                  `-${(variant.price * variant.qty) - ((variant.price * variant.qty * variant.discount)/100).toFixed(2)}`
                                  }}
                              </div>
                              <div>
                                  <button class="btn" @click.prevent="deleteItem(variant,index)">
                                      <i class="fa fa-trash"></i>
                                  </button>
                              </div>
                          </div>
                      </div>
                  </div>
              </template>
          </div>
      </template>
      <div class="emptyCart" v-else>
         <div class="row align-items-center justify-content-center" style="height: 100%;">
             <h3>{{ $t('general.EmptyCart') }}</h3>
         </div>
      </div>
    </div>
    <div class="info">
      <div class="data border-bottom">
        <div class="bold">{{ $t('general.sub_total') }}</div>
        <div class="bold">
            {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
            {{sub_total? `-${sub_total}`: '0.00' }}
        </div>
      </div>
      <div class="data border-bottom">
        <div>{{ $t('general.tax') }}</div>
        <div>
            {{ (currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU') }}
            {{ taxProducts? `-${taxProducts}`: '0.00' }}
        </div>
      </div>
      <div
          :class="['data border-bottom position-relative discountItem']"
          @click.self="isAllDiscount = !isAllDiscount;isSubDiscount=false"
      >
        <div>{{ $t('general.Discountonallitems') }} (%) <i class="fa fa-edit"></i></div>
        <div>{{ all_discount?all_discount:'0.00' }}%</div>
        <div v-if="isAllDiscount" :class="['position-absolute didi',$i18n.locale == 'ar'?'right':'']">
            <input type="number" disabled step=".01" v-model.number="all_discount"  class="form-control"/>
        </div>
      </div>
      <div
          :class="['data border-bottom position-relative discountSubtotal']"
          @click.self="isSubDiscount = !isSubDiscount;isAllDiscount=false"
      >
        <div>{{ $t('general.Discountonsubtotal') }} <i class="fa fa-edit"></i></div>
        <div>
            {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
            {{ sub_discount?sub_discount:'0.00' }}
        </div>
        <div v-if="isSubDiscount" :class="['position-absolute didi',$i18n.locale == 'ar'?'right':'']">
            <input step=".01" disabled v-model.number="sub_discount" @input="subDiscountFun(sub_discount)" type="number" class="form-control"/>
        </div>
      </div>
      <div
            v-if="adjustedDiscount > 0 && type != 'purchase'"
            :class="['data border-bottom']"
        >
            <div>{{ $t('general.adjustedDiscount') }}</div>
            <div>
                {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
                {{ adjustedDiscount?adjustedDiscount:'0.00' }}
            </div>
      </div>
      <div class="data border-bottom">
        <div>
          <span class="bold">{{ $t('general.total') }}</span>
        </div>
        <div v-if="type != 'purchase'">
            {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
            {{ tax_type == "excluded" ?
                total?`-${parseFloat(total.toFixed(2))}`:'0.00' :
                (total + taxProducts)?`-${parseFloat((total + taxProducts).toFixed(2))}`:'0.00'
            }}
        </div>
        <div v-else>
              {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
              {{
                 total?`-${parseFloat(total.toFixed(2))}`:'0.00'
              }}
          </div>
      </div>
      <div class="data border-bottom order-type" v-if="0">
        <div>
          <span class="bold">Order Type</span>
        </div>
        <div>
          <button class="btn highlight outline-button">
            <i class="fas fa-hamburger"></i>
            Dine In
          </button>
          <button class="btn outline-button">
            <i class="fa-solid fa-cart-shopping"></i> Take Away
          </button>
        </div>
      </div>
    </div>
    <div class="btn-container border-bottom">
      <button @click.prevent="selectVariants.length > 0?popSales():false" class="btn pay">Pay</button>
    </div>
    <div class="actions">
      <a href="#" :class="[selectVariants.length == 0?'disabaledA':'','col-12']" @click.prevent="deleteOrder">
        <i class="fa-solid fa-circle-xmark"></i>
      </a>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import Customer from "../../../../components/create/general/customerGeneral";
import PosOrderInvoiceReturn from "./posOrderInvoiceReturn"
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import loader from "../../../../components/general/loader";
export default {
  mixins: [transMixinComp],
  props: [
        'companyKeys',
        'defaultsKeys',
        'currency',
        'type'
    ],
  components: {
        Multiselect,
        Customer,
        PosOrderInvoiceReturn,
        loader
    },
  methods: {
      deleteOrder(){
          if(this.selectVariants.length > 0){
              Swal.fire({
                  title: `${this.$t("general.Areyousure")}`,
                  text: `${this.$t("general.Thisorderwillbecancelled")}`,
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                  cancelButtonText: `${this.$t("general.Nocancel")}`,
                  confirmButtonClass: "btn btn-success mt-2",
                  cancelButtonClass: "btn btn-danger ml-2 mt-2",
                  buttonsStyling: false,
              }).then((result) => {
                  if (result.value) {
                      this.deleteItemTrancute();
                  }
              });
          }
      },
      variantTitle(variant){
          let product = this.selectProducts.find(el => el.id == variant.product_id);
          return product.product_variant? product.product_variant.find(el => el.id == variant.product_variant_id).attribute_values: '';
      },
      deleteItem(variant,index){
              this.$store.commit('orderReturn/removeProduct',variant);
      },
      deleteItemTrancute(){
          this.$store.commit('orderReturn/allTurncate');
          this.customer_id = null;
          this.customers = [];
          this.debounce = {};
          this.details = [];
          this.isAllDiscount = false;
          this.isSubDiscount = false;
          this.allDiscount = 0;
          this.subDiscount = null;
          this.order = null;
      },
      popSales(){
          this.order = {
              branch_id: this.orderChoose.branch_id,
              customer_id: this.orderChoose.customer_id,
              customer: this.orderChoose.customer,
              currency: this.currency,
              tax_type: this.tax_type,
              allDiscount: this.all_discount,
              subDiscount: this.sub_discount,
              details: this.selectVariants,
              type: this.type
          };
          this.$bvModal.show('pay-create');
      }
  },
  computed: {
        selectProducts(){
            return this.$store.getters['orderReturn/allSelectProducts'];
        },
        selectVariants(){
            return this.$store.getters['orderReturn/productDetails'];
        },
        sub_total(){
            return this.$store.getters['orderReturn/sub_total'];
        },
        all_discount(){
            return this.$store.getters['orderReturn/all_discount'];
        },
        sub_discount(){
            return this.$store.getters['orderReturn/sub_discount'];
        },
        total(){
            return this.$store.getters['orderReturn/total'];
        },
        taxProducts(){
            return this.$store.getters['orderReturn/tax'];
        },
        tax_type(){
            return this.$store.getters['orderReturn/tax_type'];
        },
        adjustedDiscount(){
            return this.$store.getters['orderReturn/adjustedDiscount'];
        },
        orderChoose(){
          return this.$store.getters['orderReturn/order'];
      }
    },
  watch: {
      orderChoose(newVal,oldVal){
          if(newVal){
              if(newVal.customer_id)
                  this.customers = newVal.customer;
                  this.customer_id = newVal.customer_id;
              if(newVal.supplier_id)
                  this.suppliers = newVal.supplier;
                  this.supplier_id = newVal.supplier_id;
          }else {
              this.customers= [];
              this.customer_id = null;
              this.supplier_id = null;
              this.suppliers = [];
          }
      }
  },
  data() {
    return {
        isLoader:false,
        branch_id: null,
        customer_id: null,
        customers: [],
        debounce: {},
        details: [],
        isAllDiscount: false,
        isSubDiscount: false,
        allDiscount: 0,
        subDiscount: null,
        max_error_variant: false,
        order_id: null,
        supplier_id: null,
        suppliers: [],
        order: null
    };
  },
};
</script>

<style lang="scss" scoped>
.order-container {
  position: relative;
    overflow: hidden;
  color: #0a8fc0 !important;
  background: #f0f8ff !important;
  .highlight {
    background: #3bafda  !important;
    color: #fff !important;
    border-color: #3bafda !important;
  }
  .order-type {
    .outline-button {
      text-align: center;
      padding: 5px 8px;
      border-radius: 27px;
      border: 1px solid #0a8fc0 ;
      font-size: 13px;
      color: #0a8fc0;
      transition: all 0.5s;
      &:hover {
        color: #fff;
        background: #0a8fc0 !important;
        border-color: #0a8fc0;
      }
    }
  }
  .form-container {
    margin: 10px 0;
    input,textarea {
        border: 1px solid #3cafda;
        color: #3bafda;
    }
    label {
       color: #0a8fc0 !important;
    }
  }
  .name {
    display: flex;
    align-items: center;
    gap: 8px;
    button {
      font-size: 12px;
      display: flex;
      justify-content: center;
      width: 21px;
      padding: 0;
      height: 21px;
      border-radius: 50%;
      align-items: center;
      transition: all 0.5s;
      border: 2px solid #0a8fc0;
      color: #0a8fc0 !important;
      &.button-collapse {
        transform: rotate(45deg);
      }
      &.button-expand {
        transform: rotate(-45deg);
      }
    }
  }
  .increment-container {
    align-items: center;
    display: flex;
    gap: 8px;
    .btn {
      span {
        position: relative;
        bottom: 1px;
      }
      font-weight: bold;
      font-size: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 22px;
      padding: 0;
      height: 22px;
      border-radius: 50%;
      align-items: center;
      transition: all 0.5s;
      border: 3px solid #0a84c09c;
      color: #0a8fc0 !important;
      &:hover {
          border: 3px solid  #0a8fc0
      }
    }
  }
  .all-orders {
    gap: 7px;
    padding: 11px;

    width: 100%;
    justify-content: space-between;
    display: flex;
    .price {
      .btn {
        font-size: 11px;
        color: #0a8fc0 !important;
        &:hover {
           color:#f1556c !important;
        }
      }
      align-items: center;
      display: flex;
    }
  }

  .actions {
    display: flex;
    justify-content: space-between;
    a {
      transition: all 0.5s;
      &:hover {
          background-color: #3bafda2e;
          .fa-circle-xmark{
              color:#f1556c !important;
          }
          .fa-pause {
              color:#000 !important;
          }
      }
      &.disabaledA {
          &:hover {
              background-color: unset;
              .fa-circle-xmark{
                  color: unset;
              }
              .fa-pause {
                  color: unset;
              }
              .fa-recycle {
                  color: unset;
              }
          }
      }
      .badge {
        position: relative;
        bottom: 9px;
        font-size: 10px !important;
      }
      font-size: 16px !important;
      width: 33.3333333%;
      justify-content: center;
      display: flex;
      padding: 16px 0px;
      color: inherit;
      text-decoration: none;
    }
    a:not(:last-child) {
      border-right: 1px solid #e5e8eb;
    }
  }
  .btn-container {
    padding: 15px 18px;
    button {
      background: #3bafdabf !important;
      color: #fff;
      width: 100%;
      padding: 12px;
      font-weight: 700;
      transition: all 0.5s;
      &:hover {
        background: #0a8fc0  !important;
        border-color: #0a8fc0  !important;
      }
    }
  }
  .cart-container {
    height: 300px;
    overflow: auto;
    .emptyCart {
        height: 100% !important;
        overflow: hidden;
        h3 {
            color: #0a8fc0;
        }
    }
  }
  border-radius: 5px;
  .control {
    padding: 11px 11px 0px 11px;
    justify-content: space-between;
    display: flex;
    .search {
      width: 84%;
    }
  }
  margin-top: 12px;
  background: #fff;
  -webkit-box-shadow: 0 1px 15px 1px rgba(60, 55, 68, 0.15);
  box-shadow: 0 1px 15px 1px rgba(60, 55, 68, 0.15);
  .form-control {
    padding: 21px 16px;
  }
  .input-group-text {
    font-size: 12px;
    color: #0a8fc0 !important;
  }
  .info {
    .data {
      i {
        margin: 0 5px;
        font-size: 12px;
      }
      padding: 11px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      .bold {
        font-weight: bold;
      }
      &.discountItem {
          cursor: pointer;
          .didi{
              left: 43%;
              width: 109px;
              .right {
                  right: 46%;
                  left: unset;
              }
              input{
                  padding: 0px 16px;
                  border: 1px solid #3bafda;
                  box-shadow: 0px 0px 7px 0px #3bafda70, 0px 0px 0px -3px #3bafda78;
              }
          }
        }
      &.discountSubtotal {
            cursor: pointer;
            .didi{
                left: 38%;
                width: 109px;
                .right {
                    right: 24%;
                    left: unset;
                }
                input{
                    padding: 0px 16px;
                    border: 1px solid #3bafda;
                    box-shadow: 0px 0px 7px 0px #3bafda70, 0px 0px 0px -3px #3bafda78;

                }
            }
        }
    }
  }
  .body-tr-custom {
      cursor: pointer;
  }
}
</style>
