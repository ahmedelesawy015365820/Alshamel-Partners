import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        // checked by delta
        path: '/dashboard/realEstate/unitstatus',
        name: 'realEstate-unitstatus',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/unit-status'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/admin-report',
        name: 'admin-report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/admin-report'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/unsold-unit-report',
        name: 'unsold-unit-report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/unsold-unit-report'),
    },

    {
        // checked by delta
        path: '/dashboard/realEstate/contract',
        name: 'realEstate-contract',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/contract'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/invoice',
        name: 'realEstate-invoice',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/invoice'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/unit-type',
        name: 'unit-type',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/unit-type'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/view',
        name: 'view',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/view'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/finishing',
        name: 'finishing',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/finishing'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/building-wallet',
        name: 'realEstate-building-wallet',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/building-wallet'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/contractunit',
        name: 'realEstate-contractunit',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/contractunit'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/owner',
        name: 'realEstate-owner',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/owner'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/building',
        name: 'realEstate-building',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/building'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/wallet',
        name: 'realEstate-wallet',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/wallet'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/unit',
        name: 'realEstate-wallet-owner',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/unit'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/reservation',
        name: 'realEstate-reservation',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/reservation'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/category',
        name: 'realEstate-category',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/item-category'),
    },
    {
        // checked by delta
        path: '/dashboard/realEstate/item',
        name: 'realEstate-item',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/item'),
    },
    {
        // repeated
        path: '/dashboard/realEstate/invoice',
        name: 'realEstate-invoice',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/realEstate/invoice'),
    },
];
