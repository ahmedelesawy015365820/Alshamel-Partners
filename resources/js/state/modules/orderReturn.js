// state
export const state = {
    order: null,
    allSelectProducts: [],
    productDetails: [],
    sub_total: 0,
    all_discount: 0,
    sub_discount: 0,
    total: 0,
    varientObj: null,
    tax: 0,
    tax_type:"",
    adjustedDiscount: 0,
    allNumQty: 0
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
    tax_type: state => state.tax_type,
    adjustedDiscount: state => state.adjustedDiscount,
    order: state => state.order,
    allNumQty: state => state.allNumQty
}

// mutation
export const mutations = {
    plusQty(state,variant_id){
        let variant = state.productDetails.find(el => el.product_variant_id == variant_id);
        variant.qty -= 1;
        if(variant.qty == 0){
            this.commit('orderReturn/removeProduct',variant);
        }
        let adjustedDis = 0;
        state.productDetails.forEach(e => {
            if(e.product_id && e.product_variant_id){
                if(state.order.sub_discount > 0 && state.order.sub_discount){
                    adjustedDis += !(parseFloat(e.discount) > 0)?
                        (
                            parseFloat(e.price.toFixed(2))*
                            parseFloat(e.qty.toFixed(2))
                        ) * ( state.order.sub_discount / state.order.sub_total ):
                        (
                            parseFloat(e.price.toFixed(2))*parseFloat(e.qty.toFixed(2))
                            -
                            (
                                parseFloat(e.price.toFixed(2))*
                                parseFloat(e.qty.toFixed(2))*
                                parseFloat(e.discount.toFixed(2))
                            )
                            /100
                        ) * ( state.order.sub_discount / state.order.sub_total );
                }
            }
        });
        state.adjustedDiscount = adjustedDis > 0 ? parseFloat(adjustedDis.toFixed(2)): 0;
        this.commit('orderReturn/allTotal');
    },
    removeProduct(state,variant){
        let vIndex = state.productDetails.findIndex(el => el.product_variant_id == variant.product_variant_id);
        state.productDetails.splice(vIndex,1);
        let adjustedDis = 0;
        state.productDetails.forEach(e => {
            if(e.product_id && e.product_variant_id){
                if(state.order.sub_discount > 0 && state.order.sub_discount){
                    adjustedDis += !(parseFloat(e.discount) > 0)?
                        (
                            parseFloat(e.price.toFixed(2))*
                            parseFloat(e.qty.toFixed(2))
                        ) * ( state.order.sub_discount / state.order.sub_total ):
                        (
                            parseFloat(e.price.toFixed(2))*parseFloat(e.qty.toFixed(2))
                            -
                            (
                                parseFloat(e.price.toFixed(2))*
                                parseFloat(e.qty.toFixed(2))*
                                parseFloat(e.discount.toFixed(2))
                            )
                            /100
                        ) * ( state.order.sub_discount / state.order.sub_total );
                }
            }
        });
        state.adjustedDiscount = adjustedDis > 0 ? parseFloat(adjustedDis.toFixed(2)) : 0;
        this.commit('orderReturn/allTotal');
    },
    allTurncate(state){
        state.allSelectProducts = [];
        state.productDetails = [];
        state.sub_total = 0;
        state.all_discount = 0;
        state.sub_discount = 0;
        state.tax = 0;
        state.total = 0;
        state.adjustedDiscount = 0;
        state.order = null;
        state.allNumQty = 0;
    },
    allTotal(state){
        let sub_total = 0;
        let tax = 0;
        let adjustedDiscount = 0;
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
                if(state.order.sub_discount > 0 && state.order.sub_discount){
                    adjustedDiscount += !(parseFloat(e.discount) > 0)?
                        (
                            parseFloat(e.price.toFixed(2))*
                            parseFloat(e.qty.toFixed(2))
                        ) * ( state.order.sub_discount / state.order.sub_total ):
                        (
                            parseFloat(e.price.toFixed(2))*parseFloat(e.qty.toFixed(2))
                            -
                            (
                                parseFloat(e.price.toFixed(2))*
                                parseFloat(e.qty.toFixed(2))*
                                parseFloat(e.discount.toFixed(2))
                            )
                            /100
                        ) * ( state.order.sub_discount / state.order.sub_total );
                }
            }
        });
        if(state.order.order_type != 'receiving'){
            state.tax = tax;
            state.sub_total = sub_total;
            state.total = sub_total - (state.adjustedDiscount > 0? state.adjustedDiscount :state.sub_discount);
        }else {
            state.total = sub_total;
        }
    },
    subDiscountFunRemove(state){
        state.sub_discount = 0;
        let detail = state.productDetails.find(el => !el.product_id || !el.product_variant_id);
        if(detail){
            let index = state.productDetails.findIndex(el => !el.product_id || !el.product_variant_id);
            state.productDetails.splice(index,1);
        }
        this.commit('orderReturn/allTotal');
    },
    updateOrder(state,order){
        state.order = order;
        let variants = [];
        let products = [];
        let allNumQty = 0;
        order.order_items.forEach(el => {
            if(el.product_id && el.variant_id && el.item_count > 0){
                if(!products.find(e => e.id == el.product_id)){
                    products.push(el.product);
                }
                allNumQty += el.item_count;

                variants.push({
                    parent_id: el.id,
                    product_id: el.product_id,
                    product_variant_id: el.variant_id,
                    tax_id: el.tax_id,
                    qty: (el.item_count),
                    price: el.price,
                    discount:  el.discount,
                    note: el.note
                });
            }
        });
        state.allNumQty = allNumQty;
        state.productDetails = variants;
        state.allSelectProducts = products;
        // state.sub_total = order.sub_total;
        state.all_discount = order.all_discount;
        state.tax_type = order.tax_type;
        state.sub_discount = order.sub_discount;
        // state.total = order.total;
        this.commit('orderReturn/allTotal');
    },
    plusQtyConvert(state){
        state.productDetails.forEach(el => el.qty = el.qty * -1);
    }
};

// actions
export const actions = {};
