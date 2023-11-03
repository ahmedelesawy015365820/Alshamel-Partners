import auth from "../../middleware/auth";
import checkAuth from "../../middleware/auth-check";
import companyId from "../../middleware/companyId";
import guest from "../../middleware/guest";

export default [
    {
        path: '/dashboard/avenue',
        name: 'avenue',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/avenue/index'),
    },
    {
        path: '/dashboard/country',
        name: 'country',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/country/index'),
    },
    {
        path: '/dashboard/company',
        name: 'company',
        meta: {
            middleware: [companyId,checkAuth]
        },
        component: () => import('../../views/pages/general/company/index'),
    },
    {
        path: '/dashboard/colors',
        name: 'colors',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/colors/index'),
    },
    {

        // checked by Delta
        path: '/dashboard/city',
        name: 'city',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/city/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/category',
        name: 'Category',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/category/index'),
    },
    {
        path: '/dashboard/brand',
        name: 'Brand',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/brand/index'),
    },
    {
        // checked by Delta

        path: '/dashboard/branch',
        name: 'branch',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/branch/index')
    },
    {
        path: '/dashboard/banks',
        name: 'banks',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/banks/index'),
    },
    {
        // checked by Delta

        path: '/dashboard/bankAccount',
        name: 'bankAccount',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/bankAccounts/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/customer-resource',
        name: 'customer resource',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/customerResource/index'),
    },
    {
        path: '/dashboard/department',
        name: 'department',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/department/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/customer-group',
        name: 'Customer Group',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/customerGroups/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/currency',
        name: 'currency',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/currency/index'),
    },
    {
        // checked by Delta

        path: '/dashboard/customer',
        name: 'general customer',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/customer/index'),
    },
    {
        // checked by Delta

        path: '/dashboard/financialYear',
        name: 'financialYear',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/financialYear/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/general-customer-category',
        name: 'customer-category',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/customer_category/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/employee',
        name: 'employee',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/employee/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/equipment',
        name: 'equipment',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/equipment/index'),
    },
    {
         // checked by Delta

        path: '/dashboard/externalSalesmen',
        name: 'externalSalesmen',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/externalSalesmen/index'),
    },
    {
        // checked by Delta

        path: '/dashboard/document/index',
        name: 'document',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/document/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/document/document-status',
        name: 'document status',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/document/document-status'),
    },
    {
        // checked by Delta
        path: '/dashboard/document/document-reason',
        name: 'document reason',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/document/document-reason'),
    },
    {
        path: '/dashboard/document/document-linked-categories',
        name: 'document linked categories',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/document/document-module-types'),
    },
    {
        path: '/dashboard/document/link-documents-with-status',
        name: 'link documents with status',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/document/document-with-status'),
    },
    {
        // checked by Delta
        path: '/dashboard/dictionary',
        name: 'dictionary',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/dictionary/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/periodic-maintenance',
        name: 'periodic maintenance',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/periodic-maintenance/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/paymentTypes',
        name: 'paymentTypes',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/paymentTypes/index'),
    },
    {
        path: '/dashboard/login',
        name: 'login',
        component: () => import('../../views/pages/general/auth/login'),
        meta: {
            middleware: [guest]
        },
    },
    {
        // checked by Delta
        path: '/dashboard/location',
        name: 'location',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/location/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/internalSalesman',
        name: 'internalSalesman',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/internalSalesMen/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/group',
        name: 'Group',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/group/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/governorate',
        name: 'governorate',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/governorate/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/sector',
        name: 'sector',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/sector/index'),
    },
    {
        path: '/dashboard/screen-properties',
        name: 'screen-properties',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/screenproperties/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/salesmenTypes',
        name: 'salesmenTypes',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/salesmenTypes/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/salesmen',
        name: 'salesmen',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/salesmen/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/salesman-plan',
        name: 'salesman-plan',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/salesman-plan/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/salesman-plan-details',
        name: 'salesman-plan',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/salesman-plan-details/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/roles',
        name: 'roles',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/roles/index'),
    },
    {
        path: '/dashboard/tree-properties',
        name: 'tree-properties',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/property-tree/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/priority',
        name: 'priority',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/priority/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/ticket-manager/tasks',
        name: 'ticketManager tasks',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/ticketManager/tasks'),
    },
    {
        // checked by Delta
        path: '/dashboard/ticket-manager/tasks-calender',
        name: 'ticketManager calender',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/ticketManager/calender'),
    },
    {
        // checked by Delta
        path: '/dashboard/units',
        name: 'Units',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/units/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/users',
        name: 'users',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/users/index'),
    },
    {

        // checked by Delta
        path: '/dashboard/Variant-Attributes',
        name: 'Variant Attributes',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/variantAttributes/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/statuses',
        name: 'statuses',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/statuses/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/taxes',
        name: 'Taxes',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/taxes/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/supplier',
        name: 'supplier',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/supplier/index'),
    },
    {
        // checked by Delta
        path: '/dashboard/street',
        name: 'street',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/street/index'),
    },
    {
        path: '/dashboard/store',
        name: 'store',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/store/index'),
    },
    {
        path: '/dashboard/notifications',
        name: 'notifications',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/notification')
    },
    {
        path: '/dashboard/custom-table',
        name: 'custom table',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/custom-table/index')
    },
    {
        path: '/dashboard/database-backup',
        name: 'Database backup',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/DatabaseBackup/index')
    },
    {
        path: '/dashboard/',
        name: 'home',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/dashboard/sales/index')
    },
    //**********************************************
    {
        path: '/dashboard/dashboard/crm',
        name: 'crm-dashboard',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/dashboard/crm/index')
    },
    {
        path: '/dashboard/dashboard/analytics',
        name: 'analytics-dashboard',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/dashboard/analytics/index')
    },
    {
        path:'*',
        name:'page404',
        component: () => import('../../views/pages/general/error/404.vue')
    },
    {
        path: '/dashboard/serial',
        name: 'Serial',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/serial/index'),
    },
    {
        path: '/dashboard/paymentMethod',
        name: 'Payment Method',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/paymentMethod/index'),
    },
    {
        path: '/dashboard/message',
        name: 'Message',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/message/index'),
    },
    {
        path: '/dashboard/lawyer',
        name: 'Lawyer',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/lawyer/index'),
    },
    {
        path: '/dashboard/legal-procedure',
        name: 'Legal Procedures',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/legalProcedures/index'),
    },
    {
        path: '/dashboard/reservation-calender',
        name: 'Reservation',
        meta: {
            middleware: [auth,checkAuth]
        },
        component: () => import('../../views/pages/general/reservation/index'),
    },
];
