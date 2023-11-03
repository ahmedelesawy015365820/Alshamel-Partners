<template>
  <div>
    <b-modal
      size="lg"
      v-if="selectedMainProduct"
      id="variant"
      :title="
        selectedMainProduct
          ? $i18n.locale == 'ar'
            ? selectedMainProduct.title
            : selectedMainProduct.title_e
          : ''
      "
      title-class="font-18"
      :hide-footer="true"
      @hidden="selectedMainProduct = null"
    >
      <div class="alert alert-danger text-center" v-if="max_error_variant && type != 'purchase'" role="alert">
            {{ $t('general.Thereisnoquantityinstock') }}
      </div>
      <div class="alert alert-danger text-center" v-if="min_error_variant && type != 'purchase'" role="alert">
            {{ $t('general.Outofstock') }}
      </div>
      <div class="products row">
        <div
          v-for="item in selectedMainProduct.product_variant"
          :key="item.id"
          class="col-lg-4"
        >
          <div
              :class="['product',selectVariants.find(el => el.product_variant_id == item.id)? 'active':'']"
              @click.prevent="type != 'purchase'?getVariant(selectedMainProduct,item):getVariantPurchase(selectedMainProduct,item)"
          >
            <template>
              <div v-if="item.quantity == 0 && type != 'purchase'" class="out-of-stock">
                  {{ $t('general.Outofstock') }}
              </div>
              <div class="price">
                  {{ type != 'purchase' ? item.selling_price : item.purchase_price }}
                  {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
              </div>
            </template>
            <img
              class="img-fluid"
              :src="
                item.media && item.media.length
                  ? item.media[0].webp
                  : 'https://www.nidec-avtronencoders.com/avtron-middleware2/public/assets/img/relnoimgnew.png'
              "
            />
            <div class="title">
              <div class="text-center">
                {{ item.attribute_values }}
              </div>
              <div
                style="font-weight: lighter"
                v-for="(attr,ind) in item.product_attributes"
                :key="ind"
              >
                {{ $i18n.locale == "ar" ? attr.name : attr.name_e }} :
                {{ attr.values }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </b-modal>
    <div class="row control">
      <div class="col-lg-9">
        <label class="sr-only" for="inlineFormInputGroup">{{ $t('general.SearchProduct') }}</label>
        <div class="input-group mb-2">
          <div class="input-group-prepend">
            <div class="input-group-text"><i class="fa fa-search"></i></div>
          </div>
          <input
            v-model="search"
            type="text"
            class="form-control"
            id="inlineFormInputGroup"
            placeholder="Search Product"
          />
        </div>
      </div>
      <div class="col-lg-3">
        <div class="dropdown show">
          <a
            class="btn dropdown-toggle"
            href="#"
            role="button"
            id="dropdownMenuLink"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <span>Category</span>
            <i class="fas fa-caret-down"></i>
          </a>

          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <div class="row" style="padding: 0px 29px">
              <div
                v-for="item in categories"
                :key="item.id"
                class="col-lg-6 mb-3"
              >
                <div class="form-check">
                  <input
                    :checked="selectedCategoriesIds.includes(item.id)"
                    @change="selectItem(item.id)"
                    class="form-check-input"
                    type="checkbox"
                    value=""
                    id="flexCheckDefault"
                  />
                  <label class="form-check-label" for="flexCheckDefault">
                    {{ $i18n.locale == "ar" ? item.name : item.name_e }}
                  </label>
                </div>
              </div>
            </div>
            <div
              class="row border-top"
              style="
                align-items: center;
                padding: 15px 26px;
                justify-content: space-between;
                display: flex;
              "
            >
              <button @click="clearCategory" class="btn border">
                {{ $t("general.clear") }}
              </button>
              <button
              @click="getProducts"
                style="background: #3bafda !important; color: #fff"
                class="btn"
              >
                {{ $t("general.Apply") }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
      <div class="alert alert-danger text-center" v-if="max_error && type != 'purchase'" role="alert">
          {{ $t('general.Thereisnoquantityinstock') }}
      </div>
      <div  class="alert alert-danger text-center" v-if="min_error && type != 'purchase'" role="alert">
          {{ $t('general.Outofstock') }}
      </div>
    <div class="products row" :style="{
          'overflow-y': products.length > 8 ? 'scroll' : 'unset',
          'height': products.length == 0 ? '73vh' : 'unset',
          'max-height': products.length > 0 ? '73vh' : 'unset',
        }"
    >
      <loader size="large" v-if="isLoader" />
      <template v-for="item in products">
          <div
              :key="item.id"
              v-if="item.product_variant.length > 0"
              @click="onProductClicked(item)"
              class="col-lg-3"
          >
              <div
                  :class="['product',selectProducts.find(el => el.id == item.id)? 'active':'']"
                  @click.prevent="type != 'purchase'? getStanderd(item):getStanderdPurchase(item)"
              >
                  <template v-if="item.product_type == 'standard'">
                      <div
                          v-if="item.product_variant[0].quantity == 0 && type != 'purchase'"
                          class="out-of-stock"
                      >
                          {{ $t('general.Outofstock') }}
                      </div>
                      <div class="price">
                          {{ type != 'purchase'?item.product_variant[0].selling_price : item.product_variant[0].purchase_price }}
                          {{ currency ? $i18n.locale == 'ar'? currency.symbol:currency.symbol_e : 'KU' }}
                      </div>
                  </template>
                  <img
                      class="img-fluid"
                      :src="
              item.media && item.media.length
                ? item.media[0].webp
                : 'https://www.nidec-avtronencoders.com/avtron-middleware2/public/assets/img/relnoimgnew.png'
            "
                  />
                  <div class="title standard">
                      <div class="text-center">
                          {{ $i18n.locale == "ar" ? item.title.substr(0,20) : item.title_e.substr(0,20) }}
                      </div>
                  </div>
              </div>
          </div>
      </template>
    </div>
  </div>
</template>

<script>
import adminApi from "../../../../api/adminAxios";
import loader from "../../../../components/general/loader";

export default {
    name: "posSalesProduct",
  components: {
    loader,
  },
  mounted() {
    this.getCategories();
  },
  methods: {
    clearCategory() {
      this.selectedCategoriesIds = [];
    },
    selectItem(id) {
      let index = this.selectedCategoriesIds.findIndex((item) => item == id);
      if (index >= 0) {
        this.selectedCategoriesIds.splice(index, 1);
      } else {
        this.selectedCategoriesIds.push(id);
      }
    },
    getCategories() {
      adminApi
        .get(`/categories`)
        .then((res) => {
          this.categories = res.data.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    onProductClicked(item) {
      this.selectedMainProduct = item;
      if (this.selectedMainProduct.product_type != "standard") {
        this.$bvModal.show(`variant`);
      }
    },
    async getProducts() {
      this.isLoader = true;
      await adminApi
        .get(
          `/point-of-sale/sales/products?branch_id=${this.currentBranch.id}&search=${this.search}&columns[0]=title&columns[1]=title_e`,
          {
            params: {
              categories_id: this.selectedCategoriesIds,
            },
          }
        )
        .then((res) => {
          this.isLoader = false;
          this.products = res.data.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    getStanderd(item){
        if(item.product_type == 'standard'){
            if(item.product_variant[0].quantity > 0){
                if(!this.selectVariants.find(el => el.product_variant_id == item.product_variant[0].id)){
                    this.$store.commit(
                        'order/addProduct',
                        {product:item,variant_id:item.product_variant[0].id,type:this.type}
                    );
                }else{
                    if(
                        this.selectVariants.find(el => el.product_variant_id == item.product_variant[0].id).qty <
                        item.product_variant[0].quantity
                    ){
                        this.$store.commit(
                            'order/addProduct',
                            {product:item,variant_id:item.product_variant[0].id,type:this.type}
                        );
                    }else {
                        this.max_error = true;
                        setTimeout(() => {this.max_error = false},3000);
                    }
                }
            }else {
                this.min_error = true;
                setTimeout(() => {this.min_error = false},3000);
            }
        }
    },
    getStanderdPurchase(item){
          if(item.product_type == 'standard'){
              this.$store.commit('order/addProduct',{product:item,variant_id:item.product_variant[0].id,type:this.type});
          }
      },
    getVariant(selectedMainProduct,item){
        if(item.quantity > 0){
            if(!this.selectVariants.find(el => el.product_variant_id == item.id)){
                this.$store.commit(
                    'order/addProduct',
                    {product:selectedMainProduct,variant_id:item.id,type:this.type}
                );
            }else{
                if(
                    this.selectVariants.find(el => el.product_variant_id == item.id).qty <
                    item.quantity
                ){
                    this.$store.commit(
                        'order/addProduct',
                        {product:selectedMainProduct,variant_id:item.id,type:this.type}
                    );
                }else {
                    this.max_error_variant = true;
                    setTimeout(() => {this.max_error_variant = false},3000);
                }
            }
        }else {
            this.min_error_variant = true;
            setTimeout(() => {this.min_error_variant = false},3000);
        }
    },
    getVariantPurchase(selectedMainProduct,item){
        this.$store.commit(
            'order/addProduct',
            {product:selectedMainProduct,variant_id:item.id,type:this.type}
        );
    }
  },
  props: ["selectedBranch","currency","type"],
  watch: {
    selectedBranch: {
      handler(newV, old) {
        this.currentBranch = newV;
        if (!newV) return;
        this.getProducts();
      },
    },
    search(after, befour) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getProducts();
      }, 400);
    },
  },
  computed: {
        selectProducts(){
            return this.$store.getters['order/allSelectProducts'];
        },
        selectVariants(){
            return this.$store.getters['order/productDetails'];
        }
    },
  data() {
    return {
      debounce: {},
      search: "",
      categories: [],
      selectedCategoriesIds: [],
      products: [],
      isLoader: false,
      selectedMainProduct: null,
      currentBranch: null,
      max_error_variant: false,
      min_error_variant: false,
      max_error: false,
      min_error: false,
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
