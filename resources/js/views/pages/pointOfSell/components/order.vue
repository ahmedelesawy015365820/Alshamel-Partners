<template>
  <div class="order-container">
    <loader size="large" v-if="isLoader" />
    <Customer
        v-if="type != 'purchase'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
        :isPermission="isPermission" :id="'customer-create-order'" :isPage="false" type="create"
    />
    <PosOrderInvoice :order="order" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @clear_order="deleteItemTrancute"  />
    <b-modal
          v-if="type != 'purchase'"
          id="hold"
          :title="$t('general.HoldOrderlist')"
          title-class="font-18"
          body-class="p-4 "
          size="lg"
          :hide-footer="true"
      >
          <!-- start .table-responsive-->
          <div class="table-responsive mb-3 custom-table-theme position-relative">

              <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                     id="printBuilding">
                  <thead>
                  <tr>
                      <th>
                          <div class="d-flex justify-content-center">
                              <span>{{ $t('general.invoice') }}</span>
                          </div>
                      </th>
                      <th>
                          <div class="d-flex justify-content-center">
                              <span>{{ $t('general.date') }}</span>
                          </div>
                      </th>
                      <th class="do-not-print">
                          {{ $t("general.Action") }}
                      </th>
                  </tr>
                  </thead>
                  <tbody v-if="holds.length > 0">
                  <tr
                      @dblclick.capture="OrderRecycle(data.id)"
                      v-for="(data, index) in holds"
                      :key="data.id"
                      class="body-tr-custom"
                  >
                      <td>
                          <h5 class="m-0 font-weight-normal">{{ data.prefix }}</h5>
                      </td>
                      <td>
                          <h5 class="m-0 font-weight-normal">{{ data.date }}</h5>
                      </td>
                      <td class="text-center">
                          <a
                              class="dropdown-item text-black"
                              href="#"
                              @click.prevent="deleteHold(data.id)"
                          >
                              <div
                                  class="text-black"
                              >
                                  <span>{{ $t("general.delete") }}</span>
                                  <i class="fas fa-times text-danger"></i>
                              </div>
                          </a>
                      </td>
                  </tr>
                  </tbody>
                  <tbody v-else>
                  <tr>
                      <th class="text-center" colspan="6">
                          {{ $t("general.notDataFound") }}
                      </th>
                  </tr>
                  </tbody>
              </table>
          </div>
          <!-- end .table-responsive-->
      </b-modal>
    <!--   start tax  -->
      <b-modal
          v-if="type != 'purchase' && selectVariants.length > 0"
          id="tax"
          :title="$t('general.TaxSettings')"
          title-class="font-18"
          body-class="p-4 "
          :hide-footer="true"
      >
        <div class="row">
            <div class="col-md-12" >
                <div class="form-group">
                    <label class="my-1 mr-2">
                        {{ $t('general.productPriceTax') }}
                        <span  class="text-danger">*</span>
                    </label>
                    <b-form-group>
                        <b-form-radio
                            class="d-inline-block"
                            v-model="tax"
                            name="some_is_quantity"
                            value="included"
                        >{{ $t("general.included") }}</b-form-radio
                        >
                        <b-form-radio
                            class="d-inline-block m-1"
                            v-model="tax"
                            name="some_is_quantity"
                            value="excluded"
                        >{{ $t("general.excluded") }}</b-form-radio
                        >
                    </b-form-group>
                </div>
            </div>
        </div>
      </b-modal>
    <!--   end tax  -->
    <div v-if="type != 'purchase'" class="control border-bottom">
      <div class="search">
        <div class="mb-2 d-flex">
          <div class="input-group-prepend">
            <div class="input-group-text"><i class="fa fa-user"></i></div>
          </div>
          <multiselect
              v-model="customer_id"
              :placeholder="$t('general.SearchCustomers')"
              :internalSearch="false"
              @input="showCustomerModel"
              @search-change="searchCustomer"
              :options="customers.map((type) => type.id)"
              :custom-label="(opt) =>$i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name: customers.find((x) => x.id == opt).name_e"
          >
          </multiselect>
        </div>
      </div>
      <div class="add-customer">
        <button
          type="button"
          @click.prevent="$bvModal.show('customer-create-order')"
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
                      v-model="supplier_id"
                      :placeholder="$t('general.SearchSuppliers')"
                      :internalSearch="false"
                      @search-change="searchSupplier"
                      :options="suppliers.map((type) => type.id)"
                      :custom-label="(opt) =>$i18n.locale == 'ar' ? suppliers.find((x) => x.id == opt).name: suppliers.find((x) => x.id == opt).name_e"
                  >
                  </multiselect>
              </div>
          </div>
          <div class="add-customer">
            <button
                type="button"
                @click.prevent="false"
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
              <template v-if="variant.product_id || variant.product_variant_id">
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
                                      <div>{{
                                              $i18n.locale == 'ar' ?
                                                  selectProducts.find(el => el.id == variant.product_id).title.substr(0,26)
                                                  :selectProducts.find(el => el.id == variant.product_id).title_e.substr(0,26)
                                          }}</div>
                                      <div v-if="variantTitle(variant)">({{ variantTitle(variant) }})</div>
                                  </div>
                              </div>
                          </div>
                          <div class="increment-container">
                              <button
                                  class="btn"
                                  :disabled="details[index].qty <= 1"
                                  @click.prevent="$store.commit('order/minsQty',variant.product_variant_id);--details[index].qty"
                              >
                                  <span>-</span>
                              </button>
                              <span>{{ variant.qty }}</span>
                              <button
                                  class="btn"
                                  :disabled="
                                        type != 'purchase'? details[index].qty == selectProducts.find(e => e.id == variant.product_id).
                                        product_variant.find(el => el.id == variant.product_variant_id).quantity : false
                                    "
                                  @click.prevent="$store.commit('order/plusQty',variant.product_variant_id);++details[index].qty"
                              >
                                  <span>+</span>
                              </button>
                          </div>
                          <div class="price">
                              <div>
                                  {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }} {{
                                  !(variant.discount > 0)?
                                  (variant.price * variant.qty).toFixed(2) :
                                  (variant.price * variant.qty) -
                                  ((variant.price * variant.qty * variant.discount)/100).toFixed(2)
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
                  <div
                      :id="'collapseOne-'+index"
                      class="collapse"
                      :aria-labelledby="'headingOne-'+index"
                      :data-parent="'#accordion-'+index"
                  >
                      <div class="form-container container">
                          <div class="row">
                              <div class="col-lg-4">
                                  <div class="form-group">
                                      <label>{{ $t('general.quantity') }}</label>
                                      <input
                                          type="number" class="form-control"
                                          step="0.01"
                                          v-model.number="details[index].qty"
                                          min="0"
                                          @input="maxQty(variant,index,details[index].qty)"
                                      />
                                  </div>
                              </div>
                              <div class="col-lg-4">
                                  <div class="form-group">
                                      <label>{{ $t('general.Price') }}</label>
                                      <input
                                          min="0"
                                          type="number"
                                          class="form-control"
                                          v-model.number="details[index].price"
                                          step="0.01"
                                          @input="minPrice(variant,index)"
                                      />
                                  </div>
                              </div>
                              <div class="col-lg-4">
                                  <div class="form-group">
                                      <label>{{ $t('general.discount') }} (%)</label>
                                      <input
                                          min="0"
                                          type="number"
                                          class="form-control"
                                          step="0.01"
                                          v-model.number="details[index].discount"
                                          @input="minDiscount(variant,index)"
                                      />
                                  </div>
                              </div>
                              <div class="col-lg-12">
                                  <div class="form-group">
                                      <label>{{ $t('general.note') }}</label>
                                      <textarea
                                          class="form-control"
                                          v-model="details[index].note"
                                          @input="e =>
                        $store.commit('order/note',{variant_id:variant.product_variant_id,note:details[index].note})
                     "
                                      ></textarea>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </template>
              <template v-else>
                  <div :id="'headingOne-'+index">
                      <div class="all-orders border-bottom">
                          <div class="order col-5">
                              <div class="name">
                                  <button
                                      class="btn"
                                      disabled
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
                                      <div>{{ $t('general.discount') }}</div>
                                  </div>
                              </div>
                          </div>
                          <div class="increment-container">
                              <button class="btn" disabled>
                                  <span>-</span>
                              </button>
                              <span>{{ 1 }}</span>
                              <button
                                  class="btn"
                                  disabled
                              >
                                  <span>+</span>
                              </button>
                          </div>
                          <div class="price">
                              <div>
                                  ${{  (variant.price).toFixed(2) }}
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
            {{sub_total? sub_total: '0.00' }}
        </div>
      </div>
      <div class="data border-bottom">
        <div>{{ $t('general.tax') }}</div>
        <div>
            {{ (currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU') }}
            {{ taxProducts? taxProducts: '0.00' }}
        </div>
      </div>
      <div
          :class="['data border-bottom position-relative discountItem']"
          @click.self="isAllDiscount = !isAllDiscount;isSubDiscount=false"
      >
        <div>{{ $t('general.Discountonallitems') }} (%) <i class="fa fa-edit"></i></div>
        <div>{{ all_discount?all_discount:'0.00' }}%</div>
        <div v-if="isAllDiscount" :class="['position-absolute didi',$i18n.locale == 'ar'?'right':'']">
            <input type="number" step=".01" v-model.number="allDiscount" @input="allDiscountFun(allDiscount)" class="form-control"/>
        </div>
      </div>
      <div
          :class="['data border-bottom position-relative discountSubtotal']"
          @click.self="isSubDiscount = !isSubDiscount;isAllDiscount=false"
      >
        <div>{{ $t('general.Discountonsubtotal') }} <i class="fa fa-edit"></i></div>
        <div>
            {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
            {{ subDiscount?subDiscount:'0.00' }}
        </div>
        <div v-if="isSubDiscount" :class="['position-absolute didi',$i18n.locale == 'ar'?'right':'']">
            <input step=".01" v-model.number="subDiscount" @input="subDiscountFun(subDiscount)" type="number" class="form-control"/>
        </div>
      </div>
      <div class="data border-bottom">
        <div>
          <span class="bold">{{ $t('general.total') }}</span>
            ({{ type != 'purchase' ? tax == "excluded" ?$t('general.TaxExcluded'): tax : '' }})
          <i v-if="type != 'purchase'" class="fa fa-edit" @click.prevent="$bvModal.show('tax')" style="margin: 0 32px"></i>
        </div>
        <div>
            {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
            {{ tax == "excluded" && type != 'purchase' ?total?total:'0.00' : (total + taxProducts)?(total + taxProducts):'0.00' }}
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
      <button @click.prevent="selectVariants.length > 0?popSales():false" class="btn pay">{{ $t('general.Pay') }}</button>
    </div>
    <div class="actions">
      <a
          v-if="type != 'purchase'"
          href="#"
          :class="[holds.length == 0?'disabaledA':'']"
          @click.prevent="holds.length > 0? $bvModal.show('hold'):false"
      >
        <i class="fa-solid fa-recycle"></i>
        <span class="badge badge-danger" v-if="holds.length > 0">{{ holds.length }}</span>
      </a>
      <a href="#" v-if="type != 'purchase'"  :class="[selectVariants.length == 0?'disabaledA':'']" @click.prevent="selectVariants.length > 0? pauseOrder():false">
        <i class="fa-solid fa-pause"></i>
      </a>
      <a href="#" :class="[
            selectVariants.length == 0?'disabaledA':'',
            type == 'purchase'?'col-12':''
          ]"
         @click.prevent="deleteOrder"
      >
        <i class="fa-solid fa-circle-xmark"></i>
      </a>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import Customer from "../../../../components/create/general/customerGeneral";
import PosOrderInvoice from "./posOrderInvoice"
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import loader from "../../../../components/general/loader";
export default {
  mixins: [transMixinComp],
  props: [
      'selectedBranch',
        'companyKeys',
        'defaultsKeys',
        'currency',
        'type',
      'isPermission'
    ],
  components: {
        Multiselect,
        Customer,
        PosOrderInvoice,
        loader
    },
  methods: {
      async searchCustomer(e) {
          let search = e??'';
          clearTimeout(this.debounce);
          this.debounce = setTimeout(() => {
              this.getCustomers(search);
          }, 500);
      },
      showCustomerModel(){
          if(this.customer_id && this.customer_id > 0){
              let customer = this.customers.find(el => el.id == this.customer_id);
              if(customer){
                  if(customer.customerGroup){
                      this.allDiscount = parseFloat(customer.customerGroup.discount);
                      this.allDiscountFun(parseFloat(customer.customerGroup.discount));
                  }
              }
          }else {
              this.allDiscount = 0;
              this.allDiscountFun(0);
          }
      },
      async searchSupplier(e){
          let search = e??'';
          clearTimeout(this.debounce);
          this.debounce = setTimeout(() => {
              this.getSuppliers(search);
          }, 500);
      },
      async getCustomers(search='') {
          this.isLoader = true;
          await adminApi
              .get(`/general-customer?limet=10&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
              .then((res) => {
                  this.isLoader = false;
                  let l = res.data.data;
                  this.customers = l;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      },
      async getSuppliers(search='') {
          this.isLoader = true;
          await adminApi
              .get(`/suppliers?limit=10&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
              .then((res) => {
                  this.isLoader = false;
                  let l = res.data.data;
                  this.suppliers = l;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      },
      variantTitle(variant){
          let product = this.selectProducts.find(el => el.id == variant.product_id);
          return product.product_variant? product.product_variant.find(el => el.id == variant.product_variant_id).attribute_values: '';
      },
      deleteItem(variant,index){
          if(!variant.product_id){
              this.subDiscountFun(0);
              this.subDiscount = null;
          }else {
              this.$store.commit('order/removeProduct',variant);
              this.details.splice(index,1);
          }
      },
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
      allDiscountFun(Discount){
           this.details.forEach(el => el.discount = Discount?Discount:0);
           this.$store.commit('order/allItemDiscount',Discount?Discount:0);
      },
      subDiscountFun(subDiscount){
          let variant = this.details.find(el => !el.id);
          if(variant){
              let index = this.details.findIndex(el => !el.id);
              this.details.splice(index,1);
              this.$store.commit('order/subDiscountFunRemove');
          }
          if(subDiscount){
              this.$store.commit('order/subDiscountFun',subDiscount);
          }
      },
      minQty(variant,index){
          this.$store.commit(
                'order/qty',
                {variant_id:variant.product_variant_id,qty:0}
            );
          this.details[index].qty = 0;
      },
      minPrice(variant,index){
          if(this.details[index].price && this.details[index].price > 0){
              this.$store.commit('order/price',
                  {
                      variant_id: variant.product_variant_id,
                      price: parseFloat(this.details[index].price.toFixed(2))
                  });
          }else {
              this.$store.commit(
                  'order/price',
                  {variant_id:variant.product_variant_id,price:0}
              );
              this.details[index].price = 0;
          }
      },
      minDiscount(variant,index){
          if(this.details[index].discount && this.details[index].discount > 0){
              this.$store.commit('order/discount',
                  {
                      variant_id: variant.product_variant_id,
                      discount: parseFloat(this.details[index].discount.toFixed(2))
                  });
          }else {
              this.$store.commit(
                  'order/discount',
                  {variant_id:variant.product_variant_id,discount:0}
              );
              this.details[index].discount = 0;
          }
      },
      maxQty(item,index,qty){
          if(this.type != 'purchase'){
              let mainQuantity = this.selectProducts.find(el => el.id == item.product_id)
                  .product_variant.find(el => el.id == item.product_variant_id).quantity;
              if(qty){
                  if(
                      qty < mainQuantity
                  ){
                      this.$store.commit(
                          'order/qty',
                          {variant_id:item.product_variant_id,qty:
                                  qty?parseFloat(qty.toFixed(2)):0
                          }
                      );
                  }else {
                      this.$store.commit(
                          'order/qty',
                          {variant_id:item.product_variant_id,qty: mainQuantity}
                      );
                      this.details[index].qty = mainQuantity;
                      this.max_error_variant = true;
                      setTimeout(() => {this.max_error_variant = false},3000);
                  }
              }
              else {
                  this.minQty(item,index)
              }
          }else {
              if(qty >= 0 && typeof qty == 'number'){
                  this.$store.commit(
                      'order/qty',
                      {variant_id:item.product_variant_id,qty:
                              qty?parseFloat(qty.toFixed(2)):0
                      }
                  );
              }
              else {
                  this.minQty(item,index)
              }
          }
      },
      getHold(){
          this.isLoader = true;
           adminApi
              .get(`/point-of-sale/oreders?status=hold`)
              .then((res) => {
                  this.holds = res.data.data;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              })
              .finally(() => this.isLoader = false)
      },
      pauseOrder(){
              Swal.fire({
                  title: `${this.$t("general.Areyousure")}`,
                  text: `${this.$t("general.Thisorderwillbesaved")}`,
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonText: `${this.$t("general.Yes")}`,
                  cancelButtonText: `${this.$t("general.No")}`,
                  confirmButtonClass: "btn btn-success mt-2",
                  cancelButtonClass: "btn btn-danger ml-2 mt-2",
                  buttonsStyling: false,
              }).then((result) => {
                  if (result.value) {
                      this.isLoader = true;
                      let created_by =
                          this.$store.state.auth.type == 'admin' ?
                              this.$store.state.auth.partner ? this.$store.state.auth.partner.name:this.$store.state.auth.partner.name_e:
                              this.$store.state.auth.user ? this.$store.state.auth.user.name:this.$store.state.auth.user.name_e;
                      adminApi
                          .post(`/point-of-sale/oreders/hold`,{
                              customer_id: this.customer_id,
                              all_discount:this.allDiscount,
                              branch_id: this.branch_id,
                              created_by,
                              document_id: 4,
                              products: this.selectVariants
                          })
                          .then((res) => {
                              this.deleteItemTrancute();
                              this.getHold();
                          })
                          .catch((err) => {
                              Swal.fire({
                                  icon: "error",
                                  title: `${this.$t("general.Error")}`,
                                  text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                              });
                          })
                          .finally(() => this.isLoader = false);
                  }
              });
      },
      OrderRecycle(id){
          this.deleteItemTrancute();
          let hold = this.holds.find(el => el.id == id);
          this.order_id = id;
          this.customer_id = hold.customer_id ? hold.customer_id: null;
          if(hold.customer) this.customers.push(hold.customer);
          this.subDiscount = hold.all_discount;
          this.branch_id = hold.branch_id;
          hold.order_items.forEach(el => {
              let quantity = el.product.product_variant.find(e => e.id == el.variant_id).quantity;
              if(quantity >= (el.quantity * -1)){
                  this.details.push({
                      id: el.variant_id,
                      product_id: el.product_id,
                      qty: (el.quantity * -1) ,
                      price: el.price,
                      discount: el.discount,
                      note: el.note
                  });
              }
          });
          this.$store.commit('order/updateHold',hold);
          this.$bvModal.hide('hold');
      },
      deleteItemTrancute(){
          this.$store.commit('order/allTurncate');
          this.customer_id = null;
          this.supplier_id = null;
          this.customers = [];
          this.suppliers = [];
          this.debounce = {};
          this.details = [];
          this.isAllDiscount = false;
          this.isSubDiscount = false;
          this.allDiscount = 0;
          this.subDiscount = null;
          this.tax = "excluded";
          if(this.order){
              if(this.order.order_id){
                  let holdIndex = this.holds.findIndex(el => el.id == this.order.order_id);
                  this.holds.splice(holdIndex,1);
              }
          }
          this.order = null;
      },
      deleteHold(id){
          Swal.fire({
              title: `${this.$t("general.Areyousure")}`,
              text: `${this.$t("general.Youwontbeabletoreverthis")}`,
              type: "warning",
              showCancelButton: true,
              confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
              cancelButtonText: `${this.$t("general.Nocancel")}`,
              confirmButtonClass: "btn btn-success mt-2",
              cancelButtonClass: "btn btn-danger ml-2 mt-2",
              buttonsStyling: false,
          }).then((result) => {
              if (result.value) {
                  this.isLoader = true;

                  adminApi
                      .delete(`/point-of-sale/oreders/${id}`)
                      .then((res) => {
                          this.checkAll = [];
                          this.getHold();
                          Swal.fire({
                              icon: "success",
                              title: `${this.$t("general.Deleted")}`,
                              text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                              showConfirmButton: false,
                              timer: 1500,
                          });
                      })
                      .catch((err) => {
                          if (err.response.status == 400) {
                              Swal.fire({
                                  icon: "error",
                                  title: `${this.$t("general.Error")}`,
                                  text: `${this.$t("general.CantDeleteRelation")}`,
                              });
                          } else {
                              Swal.fire({
                                  icon: "error",
                                  title: `${this.$t("general.Error")}`,
                                  text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                              });
                          }
                      })
                      .finally(() => {
                          this.isLoader = false;
                      });
              }
          });
      },
      popSales(){
          let customer = null;
          if(this.customer_id){
              customer = this.customers.find(el => el.id == this.customer_id);
          }
          this.order = {
              type: this.type,
              branch_id: this.branch_id,
              customer_id: this.customer_id,
              supplier_id: this.supplier_id,
              currency: this.currency,
              orderType: 'sales',
              allDiscount: this.allDiscount,
              subDiscount: this.subDiscount,
              details: this.details,
              order_id: this.order_id,
              customer,
              tax: this.type != 'purchase' ? this.tax : 'included'
          };
          this.$bvModal.show('pay-create');
      }
  },
  watch:{
      selectVariants(newItem,oldItem){
          if(this.details.length != this.selectVariants.length){
              if(!this.selectVariants[this.selectVariants.length - 1].product_variant_id){
                  this.details.push({
                      id: null,
                      price: this.selectVariants[this.selectVariants.length - 1].price,
                      note: ''
                  });
              }else {
                  this.details.push({
                      id: this.selectVariants[this.selectVariants.length - 1].product_variant_id,
                      product_id: this.selectVariants[this.selectVariants.length - 1].product_id,
                      qty: 1,
                      price: this.selectVariants[this.selectVariants.length - 1].price,
                      discount: this.all_discount,
                      note: ''
                  });
              }
          }
      },
      varientObject(newItem,oldItem){
          if(newItem){
              let varient = this.details.find(el => el.id == newItem.product_variant_id);
              ++varient.qty;
              this.$store.commit('order/varientObjectFunRemove');
          }
      },
      selectedBranch: {
          handler(newV, old) {
             this.branch_id = newV?newV.id:null;
          },
      },
  },
  computed: {
        selectProducts(){
            return this.$store.getters['order/allSelectProducts'];
        },
        selectVariants(){
            return this.$store.getters['order/productDetails'];
        },
        sub_total(){
            return this.$store.getters['order/sub_total'];
        },
        all_discount(){
            return this.$store.getters['order/all_discount'];
        },
        sub_discount(){
            return this.$store.getters['order/sub_discount'];
        },
        total(){
            return this.$store.getters['order/total'];
        },
        varientObject(){
            return this.$store.getters['order/varientObj'];
        },
        taxProducts(){
            return this.$store.getters['order/tax'];
        },
    },
  mounted(){
      if(this.type != 'purchase'){
          this.getHold();
      }
  },
  data() {
    return {
        tax: "excluded",
        suppliers: [],
        isLoader:false,
        holds: [],
        branch_id: null,
        customer_id: null,
        supplier_id: null,
        customers: [],
        debounce: {},
        details: [],
        isAllDiscount: false,
        isSubDiscount: false,
        allDiscount: 0,
        subDiscount: null,
        max_error_variant: false,
        order_id: null,
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
          .fa-recycle {
              color:#ffc04d !important;
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
    height: 200px;
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
