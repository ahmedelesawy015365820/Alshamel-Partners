import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/point-of-sell/product',
        name: 'POS product',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/product'),
    },
    {
        path: '/dashboard/point-of-sell/inventories',
        name: 'POS inventories',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/inventories'),
    },
    {
        path: '/dashboard/point-of-sell/sales',
        name: 'POS sales',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/sales'),
    },
    {
        path: '/dashboard/point-of-sell/sales-list',
        name: 'POS sales list',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/sales-list'),
    },
    {
        path: '/dashboard/point-of-sell/sales-report',
        name: 'POS sales report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/sales'),
    },
    {
        path: '/dashboard/point-of-sell/purchases-report',
        name: 'POS purchases report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/purchases'),
    },
    {
        path: '/dashboard/point-of-sell/purchases-summary-report',
        name: 'POS purchases summary report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/purchases_summary'),
    },
    {
        path: '/dashboard/point-of-sell/sales-details-report',
        name: 'POS sales details report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/sales_details'),
    },
    {
        path: '/dashboard/point-of-sell/sales-summary-report',
        name: 'POS sales summary report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/sales_summary'),
    },
    {
        path: '/dashboard/point-of-sell/return',
        name: 'POS return',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/salesReturn'),
    },
    {
        path: '/dashboard/point-of-sell/purchase',
        name: 'POS purchase',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/purchase'),
    },
    {
        path: '/dashboard/point-of-sell/purchase-return',
        name: 'POS purchase return',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/purchaseReturn'),
    },
    {
        path: '/dashboard/point-of-sell/register-logs',
        name: 'POS register logs',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/register_logs'),
    },
    {
        path: '/dashboard/point-of-sell/inventories-report',
        name: 'POS inventories report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/inventories'),
    },
    {
        path: '/dashboard/point-of-sell/payments-report',
        name: 'POS payments report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/payments'),
    },
    {
        path: '/dashboard/point-of-sell/payments-summary-report',
        name: 'POS purchase return',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/payment_summary'),
    },
    {
        path: '/dashboard/point-of-sell/taxes-report',
        name: 'POS taxes report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/taxes'),
    },
    {
        path: '/dashboard/point-of-sell/profit-loss',
        name: 'POS profit loss',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/profit_loss'),
    },
    {
        path: '/dashboard/point-of-sell/customer-summary',
        name: 'POS customer summary',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/customer_summary'),
    },
    {
        path: '/dashboard/point-of-sell/supplier-summary',
        name: 'POS supplier summary',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/supplier_summary'),
    },
    {
        path: '/dashboard/point-of-sell/sale-purchase',
        name: 'POS sale purchase',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/sales_purchases'),
    },
    {
        path: '/dashboard/point-of-sell/stock-adjustment',
        name: 'POS stock adjustment',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/stock_adjustment'),
    },
    {
        path: '/dashboard/point-of-sell/shipment-report',
        name: 'POS shipment report',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/reports/shipment_report'),
    },
    {
        path: '/dashboard/point-of-sell/Cash-Register',
        name: 'POS Cash Register',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/pointOfSell/cashRegister'),
    }
];
