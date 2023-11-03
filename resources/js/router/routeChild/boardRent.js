import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/boardRent/panel',
        name: 'boardRent panel',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/panel'),
    },
    {
        path: '/dashboard/boardRent/package',
        name: 'boardRent package',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/package'),
    },
    {
        path: '/dashboard/boardRent/invoice',
        name: 'boardRent invoice',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/invoice'),
    },
    {
        path: '/dashboard/boardRent/unit-status',
        name: 'boardRent unitStatus',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/unitStatus'),
    },
    {
        path: '/dashboard/boardRent/look-up',
        name: 'boardRent look up',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/lookUps'),
    },
    {
        path: '/dashboard/boardRent/request-quotation',
        name: 'board-Rent-request-quotation',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/requestQuotation'),
    },
    {
        path: '/dashboard/boardRent/board-Rent-quotation',
        name: 'board-Rent-quotation',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/quotationBoardRent'),
    },
    {
        path: '/dashboard/boardRent/publication-Contract',
        name: 'board-Rent-publication-Contract',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/publicationContract'),
    },
    {
        path: '/dashboard/boardRent/Job-Order',
        name: 'board-Rent-Job-order',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/jobOrder'),
    },
    {
        path: '/dashboard/boardRent/report/finance-report',
        name: 'board-rent-finance-report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/report/finance-report'),
    },
    {
        path: '/dashboard/boardRent/report/annual-finance-report',
        name: 'board-rent-annual-finance-report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/report/annual-finance-report'),
    },
    {
        path: '/dashboard/boardRent/report/panel-usage-rate-report',
        name: 'board-rent-panel-usage-rate-report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/report/panel-usage-rate'),
    },
    {
        path: '/dashboard/boardRent/report/customer-statement-of-account',
        name: 'board-rent-customer-statement-of-account-report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/report/customer-statment-of-account'),
    },
    {
        path: '/dashboard/boardRent/sell-method',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/boardRent/sell-method'),
    },
    
];
