import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";
import permissionGuard from "../../helper/permission";

export default [
    {
        // checked by delta
        path: '/dashboard/club/sponsor',
        name: 'club-sponsor',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/sponsor'),
    },
    {
        // checked by delta
        path: '/dashboard/club/payer-member-report',
        name: 'payer-member-report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/payer-member-report'),
    },
    {
        // checked by delta
        path: '/dashboard/club/permission-member-report',
        name: 'permission-member-report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/permission-member-report'),
    },
    {
        // checked by delta
        path: '/dashboard/club/member',
        name: 'club-member',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/member'),
    },
    {
        // checked by delta
        path: '/dashboard/club/member-accept',
        name: 'club-member-accept',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/member-accept'),
    },
    {
        // checked by delta
        path: '/dashboard/club/member-apply',
        name: 'club-member-apply',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/member-apply'),
    },
    {
        // checked by delta
        path: '/dashboard/club/settings',
        name: 'club-setting',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/settings'),
    },
    {
        // checked by delta
        path: '/dashboard/club/membership-renewal',
        name: 'club-membership-renewal',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/membership_renewal'),
    },
    {
        // checked by delta
        path: '/dashboard/club/pending-member',
        name: 'club-pending-member',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/pending_member'),
    },
    {
        // checked by delta
        path: '/dashboard/club/financial-status',
        name: 'club-financial-status',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/financial-status'),

    },
    {
        // checked by delta
        path: '/dashboard/club/members-accept-reject',
        name: 'club-member',
        meta: {
            middleware: [auth, checkAuth]
        },
        component: () => import('../../views/pages/club/member-request'),
    },
    {
        // checked by delta
        path: '/dashboard/club/subscription',
        name: 'club-subscription',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/subscription'),
    },
    {
        path: '/dashboard/club/multiSubscription',
        name: 'club-multi-subscription',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/multi_subscription'),
    },
    {
        
        path: '/dashboard/club/multiSubscriptionMultiple',
        name: 'club-multi-subscription-multiple',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/multi_subscription_multiple'),
    },
    {
        // checked by delta
        path: '/dashboard/club/change-spenser',
        name: 'club-change-spenser',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/change-sponsor'),
    },
    {
        // checked by delta
        path: '/dashboard/club/member-reject',
        name: 'member reject',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/member-reject'),
    },
    {
        // checked by delta
        path: '/dashboard/club/postal-report',
        name: 'club-financial-status',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/poster-report'),

    },
    {
        // checked by delta
        path: '/dashboard/club/paid-member-report',
        name: 'paid member',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/paid-member-report'),

    },
    {
        // checked by delta
        path: '/dashboard/club/unpaid-member-report',
        name: 'unpaid member',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/unpaid-member-report'),

    },
    {
        // checked by delta
        path: '/dashboard/club/group',
        name: 'Group Club',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/group'),

    },
    {
        // checked by delta
        path: '/dashboard/club/new-subscription',
        name: 'New Subscription',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/new-subscription'),

    },
    {
        // checked by delta
        path: '/dashboard/club/paid-member-report-after',
        name: 'Paid After A Specific Date',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/paid-member-report-after'),
    },
    {
        // checked by delta
        path: '/dashboard/club/status',
        name: 'club status',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/status'),
    },
    {
        // checked by delta
        path: '/dashboard/club/report/report-to-members',
        name: 'report to members',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/report/report-to-members'),
    },
    {
        // checked by delta
        path: '/dashboard/club/change-of-member-rights',
        name: 'Change of member rights',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/change-of-member-rights'),
    },
    {
        // checked by delta
        path: '/dashboard/club/multi-payment-report',
        name: 'multi payment report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/report/multi-payment-report'),
    },
    {
        // checked by delta
        path: '/dashboard/club/Payment-report-over-period',
        name: 'Change of member rights',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/Payment-report-over-period'),
    },
    {
        path: '/dashboard/club/duplicate-bonds-report',
        name: 'Change of member rights',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/duplicate-bonds-report'),
    },
    {
        path: '/dashboard/club/old-members-report',
        name: 'old members report',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/club/report/old-members-report'),
    },
];
