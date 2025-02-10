import { createWebHistory, createRouter } from "vue-router";
import Layout from "../pages/Layout";
import Dashboard from "../pages/dashboard/Index";
import Users from "../pages/users/Index";
import UserAddEdit from "../pages/users/AddEdit";
import Roles from "../pages/roles/Index";
import RolesAddEdit from "../pages/roles/AddEdit";
import Menus from "../pages/menus/Index";
import MenusAddEdit from "../pages/menus/AddEdit";
import MenuRole from "../pages/menus/MenuRole";
import Profile from "../pages/auth/Profile";

import Login from "../pages/auth/Login";
import NotFound from "../pages/auth/404";
import ForgotPassword from "../pages/auth/ForgotPassword";

import Transactions from "../pages/transactions/Index.vue";
import TransactionsValidate from "../pages/transactions/Validate.vue";
import TransactionsAddEdit from "../pages/transactions/AddEdit.vue";
import TransactionAdd from "../pages/transactions/Add.vue";
import TransactionsDetail from "../pages/transactions/Detail.vue";

import Event from "../pages/events/Index.vue";
import EventAddEdit from "../pages/events/AddEdit.vue";
import PaymentMethods from "../pages/payment-methods/Index.vue";
import PaymentMethodsAddEdit from "../pages/payment-methods/AddEdit.vue";
import EventDetail from "../pages/events/Detail.vue";

const routes = [
    {
        path: "/panel",
        name: "Layout",
        component: Layout,
        children: [
            { path: '', component: Dashboard, meta: { protected: true, title: "Dashboard" } },
            { path: 'dashboard', component: Dashboard, meta: { protected: true, title: "Dashboard" } },
            { path: 'users', component: Users, meta: { protected: true, title: "User" } },
            { path: 'users/:id', component: UserAddEdit, meta: { protected: true, title: "User" } },
            { path: 'roles', component: Roles, meta: { protected: true, title: "Role" } },
            { path: 'roles/:id', component: RolesAddEdit, meta: { protected: true, title: "Role" } },
            { path: 'menus', component: Menus, meta: { protected: true, title: "Menu" } },
            { path: 'menus/:id', component: MenusAddEdit, meta: { protected: true, title: "Menu" } },
            { path: 'menu-role', component: MenuRole, meta: { protected: true, title: "Menu Role" } },
            { path: 'profile', component: Profile, meta: { protected: true, title: "Profile" } },

            // Payment Methods
            { path: 'payment-methods', component: PaymentMethods, meta: { protected: true, title: "Metode Pembayaran" } },
            { path: 'payment-methods/:id', component: PaymentMethodsAddEdit, meta: { protected: true, title: "Metode Pembayaran" } },

            // Transaction Details
            { path: 'transactions', component: Transactions, meta: { protected: true, title: "Transactions" } },
            { path: 'add-transactions', component: TransactionAdd, meta: { protected: true, title: "Add Transaction" } },
            { path: 'transaction-detail/:id', component: TransactionsDetail, meta: { protected: true, title: "Transaction Detail" } },

            { path: 'events', component: Event, meta: { protected: true, title: "Events" } },
            { path: 'events/:id', component: EventAddEdit, meta: { protected: true, title: "Event Update" } },
            { path: 'events-detail/:id', component: EventDetail, meta: { protected: true, title: "Event Detail" } },
        ]
    },
    { path: "/auth/404", name: "not-found", component: NotFound },
    { path: "/auth/login", name: "login", component: Login },
    { path: "/auth/forgot-password", name: "forgot-password", component: ForgotPassword },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

router.beforeEach(async (to, from) => {
    let isAuthenticated = localStorage.getItem('user_token');
    if (!isAuthenticated && to.meta.protected) {
        window.location = '/auth/login'
    }
})

const DEFAULT_TITLE = 'Rocket Arena';
router.afterEach((to, from) => {
    document.title = to.meta.title || DEFAULT_TITLE;
});

export default router;
