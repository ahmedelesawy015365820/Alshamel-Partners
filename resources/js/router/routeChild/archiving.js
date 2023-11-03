import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";

export default [
    {
        path: '/dashboard/arch-departments',
        name: 'arch-departments',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/arch-department'),
    },
    {
        path: '/dashboard/archiving',
        name: 'archiving screen',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/archiving'),
    },
    {
        path: '/dashboard/arch-doc-status',
        name: 'arch-doc-status',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/arch-doc-status'),
    },
    {
        path: '/dashboard/arch-doc-type-fields',
        name: 'arch-doc-type-fields',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/arch-doc-type-field'),
    },
    {
        path: '/dashboard/arch-documents',
        name: 'arch-documents',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/arch-document'),
    },
    {
        path: '/dashboard/arch-document-dtls',
        name: 'arch-document-dtls',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/arch-document-dtl'),
    },
    {
        path: '/dashboard/archive-closed-references',
        name: 'archive-closed-references',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/archive-closed-reference/index'),
    },
    {
        path: '/dashboard/document-fields',
        name: 'document-fields',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/document-field'),
    },
    {
        path: '/dashboard/documents',
        name: 'documents',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/archiving/gen-arch-doc-type'),
    }
];
