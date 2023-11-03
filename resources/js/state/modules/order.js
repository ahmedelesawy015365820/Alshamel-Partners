// state
export const state = {
    allSelectProducts: [],
    productDetails: [],
    sub_total: 0,
    all_discount: 0,
    sub_discount: 0,
    total: 0,
    varientObj: null,
    tax: 0,
    tax_type:"",
    orderFinish: false
}

// getters
export const getters = {
    allSelectProducts: state => state.allSelectProducts,
    productDetails: state => state.productDetails,
    sub_total: state => state.sub_total,
    all_discount: state => state.all_discount,
    total: state => state.total,
    sub_discount: state => state.sub_discount,
    varientObj: state => state.varientObj,
    tax: state => state.tax,
    tax_type: state => state.tax_type
}

// mutation
export const mutations = {
    addProduct({allSelectProducts,productDetails,varientObj,all_discount},{product,variant_id,type}){
        if(!allSelectProducts.find(el => el.id == product.id)){
            allSelectProducts.push(product);
            productDetails.push({
                product_id: product.id,
                product_variant_id: variant_id,
                tax_id: product.tax?product.tax.id:null,
                qty: 1,
                price: type != 'purchase'?product.product_variant.find(el => el.id == variant_id).selling_price ?? 0 :
                    product.product_variant.find(el => el.id == variant_id).purchase_price ?? 0,
                discount: all_discount,
                note: ''
            });
        }else {
            let variant = productDetails.find(el => el.product_variant_id == variant_id);
            if(variant){
                this.commit('order/varientObjectFun',variant);
                ++variant.qty;
            }else {
                productDetails.push({
                    product_id: product.id,
                    product_variant_id: variant_id,
                    tax_id: product.tax?product.tax.id:null,
                    qty: 1,
                    price: type != 'purchase'?product.product_variant.find(el => el.id == variant_id).selling_price ?? 0 :
                        product.product_variant.find(el => el.id == variant_id).purchase_price ?? 0,
                    discount: all_discount,
                    note: ''
                });
            }
        }
        this.commit('order/allTotal');
    },
    plusQty({productDetails},variant_id){
        let variant = productDetails.find(el => el.product_variant_id == variant_id);
        ++variant.qty;
        this.commit('order/allTotal');
    },
    minsQty({productDetails},variant_id){
        let variant = productDetails.find(el => el.product_variant_id == variant_id);
        --variant.qty;
        this.commit('order/allTotal');
    },
    note({productDetails}, {variant_id,note}){
        let variant = productDetails.find(el => el.product_variant_id == variant_id);
        variant.note = note;
    },
    price({productDetails}, {variant_id,price}){
        let variant = productDetails.find(el => el.product_variant_id == variant_id);
        variant.price = !isNaN(price) ? price: 0;
        this.commit('order/allTotal');
    },
    qty({productDetails}, {variant_id,qty}){
        let variant = productDetails.find(el => el.product_variant_id == variant_id);
        variant.qty = !isNaN(qty) ? qty: 0;
        this.commit('order/allTotal');
    },
    discount({productDetails}, {variant_id,discount}){
        let variant = productDetails.find(el => el.product_variant_id == variant_id);
        variant.discount = !isNaN(discount)? discount: 0;
        this.commit('order/allTotal');
    },
    removeProduct({allSelectProducts,productDetails},variant){
        let attrs = productDetails.filter(el => el.product_id == variant.product_id);
        if(attrs.length > 1){
            let vIndex = productDetails.findIndex(el => el.product_variant_id == variant.product_variant_id);
            productDetails.splice(vIndex,1);
        }else if(attrs.length == 1) {
            let vIndex = productDetails.findIndex(el => el.product_variant_id == variant.product_variant_id);
            productDetails.splice(vIndex,1);
            let pIndex = allSelectProducts.findIndex(el => el.id == variant.product_id);
            allSelectProducts.splice(pIndex,1);
        }
        this.commit('order/allTotal');
    },
    allTurncate(state){
        state.allSelectProducts = [];
        state.productDetails = [];
        state.sub_total = 0;
        state.all_discount = 0;
        state.sub_discount = 0;
        state.tax = 0;
        state.total = 0;
    },
    allTotal(state){
        let sub_total = 0;
        let tax = 0;
        let taxProduct = null;
        state.productDetails.forEach(e => {
            if(e.product_id && e.product_variant_id){
                taxProduct = null;
                sub_total += !(parseFloat(e.discount) > 0)?
                    (
                        parseFloat(e.price.toFixed(2))*
                        parseFloat(e.qty.toFixed(2))
                    ) :
                    (
                        parseFloat(e.price.toFixed(2))*parseFloat(e.qty.toFixed(2))
                        -
                        (
                            parseFloat(e.price.toFixed(2))*
                            parseFloat(e.qty.toFixed(2))*
                            parseFloat(e.discount.toFixed(2))
                        )
                        /100
                    );
                if(e.tax_id){
                    taxProduct = state.allSelectProducts.find(el => el.id == e.product_id);
                    tax += !(parseFloat(e.discount) > 0)? (
                            parseFloat(e.price.toFixed(2))*
                            parseFloat(e.qty.toFixed(2)) *
                            parseFloat(taxProduct.tax.percentage)
                        ) / 100
                        :
                        ((
                            parseFloat(e.price.toFixed(2))*parseFloat(e.qty.toFixed(2))
                            -
                            (
                                parseFloat(e.price.toFixed(2))*
                                parseFloat(e.qty.toFixed(2))*
                                parseFloat(e.discount.toFixed(2))
                            )
                            /100
                        )* parseFloat(taxProduct.tax.percentage)) / 100;
                }
            }
        });
        state.tax = tax;
        state.sub_total = sub_total;
        state.total = sub_total - state.sub_discount;
    },
    allItemDiscount(state,discount){
        state.all_discount = discount?discount:0;
        state.productDetails.forEach(e => e.discount = discount?discount:0);
        this.commit('order/allTotal');
    },
    subDiscountFun(state,discount){
        state.sub_discount = discount;
        state.productDetails.push({
            price: discount,
            note: ''
        });
        this.commit('order/allTotal');
    },
    subDiscountFunRemove(state){
        state.sub_discount = 0;
        let detail = state.productDetails.find(el => !el.product_id || !el.product_variant_id);
        if(detail){
            let index = state.productDetails.findIndex(el => !el.product_id || !el.product_variant_id);
            state.productDetails.splice(index,1);
        }
        this.commit('order/allTotal');
    },
    varientObjectFun(state,varient){
        state.varientObj = varient;
    },
    varientObjectFunRemove(state){
        state.varientObj = null;
    },
    updateHold(state,order){
        let variants = [];
        let products = [];
        order.order_items.forEach(el => {
            let quantity = el.product ? el.product.product_variant.find(e => e.id == el.variant_id).quantity : 1;
            if(quantity >= (el.quantity * -1)){
                if(!products.find(e => e.id == el.product_id)){
                    products.push(el.product);
                }
                variants.push({
                    product_id: el.product_id,
                    product_variant_id: el.variant_id,
                    tax_id: el.tax_id,
                    qty: el.quantity * -1,
                    price: el.price,
                    discount:  el.discount,
                    note: el.note
                });
            }
        });
        state.productDetails = variants;
        state.allSelectProducts = products;
        // state.sub_total = order.sub_total;
        state.all_discount = order.all_discount;
        state.tax_type = order.tax_type;
        state.sub_discount = order.sub_discount;
        // state.total = order.total;
        this.commit('order/allTotal');
    }
};

// actions
export const actions = {};
