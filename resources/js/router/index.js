import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Contact from '../views/Contact.vue';
import About from '../views/About.vue';
import Projects from '../views/Projects.vue';
import AdminLogin from '../views/admin/Login.vue';
import AdminDashboard from '../views/admin/Dashboard.vue';
import AdminProjects from '../views/admin/Projects.vue';
import AdminProjectsNew from '../views/admin/ProjectsNew.vue';
import AdminMessages from '../views/admin/Messages.vue';
import AdminSettings from '../views/admin/Settings.vue';
import AdminSkills from '../views/admin/Skills.vue';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,
    },
    {
        path: '/about',
        name: 'About',
        component: About,
    },
    {
        path: '/projects',
        name: 'Projects',
        component: Projects,
    },
    {
        path: '/contact',
        name: 'Contact',
        component: Contact,
    },
    {
        path: '/admin/login',
        name: 'AdminLogin',
        component: AdminLogin,
    },
    {
        path: '/admin/dashboard',
        name: 'AdminDashboard',
        component: AdminDashboard,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/projects',
        name: 'AdminProjects',
        component: AdminProjects,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/projects/new',
        name: 'AdminProjectsNew',
        component: AdminProjectsNew,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/messages',
        name: 'AdminMessages',
        component: AdminMessages,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/settings',
        name: 'AdminSettings',
        component: AdminSettings,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/skills',
        name: 'AdminSkills',
        component: AdminSkills,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Auth guard
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('authToken');
    
    if (to.meta.requiresAuth && !token) {
        next('/admin/login');
    } else if (to.name === 'AdminLogin' && token) {
        next('/admin/dashboard');
    } else {
        next();
    }
});

export default router;
