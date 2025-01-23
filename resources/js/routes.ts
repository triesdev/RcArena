// Handler for the router

import { createRouter, createWebHistory } from "vue-router";

// Import the components
import Login from "./views/Auth/Login.vue";
import User from "./views/User/Index.vue";
import IndexDashboard from "./views/Admin/Index.vue";
import Dashboard from "./views/Admin/Dashboard.vue";

// Define the routes
import type { RouteRecordRaw } from "vue-router";
import Register from "./views/Auth/Register.vue";

const routes: RouteRecordRaw[] = [
    {
        path: "/auth/login",
        name: "LoginPage",
        component: Login,
    },
    {
        path: "/auth/register",
        name: "RegisterPage",
        component: Register,
    },
    {
        path: "/adm",
        name: "IndexDashboard",
        component: IndexDashboard,
        children: [
            {
                path: "dashboard",
                name: "Dashboard",
                component: Dashboard,
            },
            {
                path: "user",
                name: "User",
                component: User,
            },
            {
                path: "event",
                name: "Event",
                component: () => import("./views/Event/Index.vue"),
            },
            {
                path: "event/create",
                name: "CreateEvent",
                component: () => import("./views/Event/Create.vue"),
            }

        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes, // The routes array
});

export default router;
