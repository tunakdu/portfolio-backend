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
import AdminArticles from '../views/admin/Articles.vue';
import ArticleForm from '../views/admin/ArticleForm.vue';
import ArticleImport from '../views/admin/ArticleImport.vue';
import BlogList from '../views/Blog.vue';
import BlogDetail from '../views/BlogDetail.vue';
import { useAuth } from '../composables/useAuth';
import axios from 'axios';

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
        path: '/blog',
        name: 'Blog',
        component: BlogList,
    },
    {
        path: '/blog/:slug',
        name: 'BlogDetail',
        component: BlogDetail,
        props: true,
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
        path: '/admin/articles',
        name: 'AdminArticles',
        component: AdminArticles,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/articles/new',
        name: 'AdminArticleNew',
        component: ArticleForm,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/articles/edit/:slug',
        name: 'AdminArticleEdit',
        component: ArticleForm,
        props: true,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/articles/import',
        name: 'AdminArticleImport',
        component: ArticleImport,
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
router.beforeEach(async (to, from, next) => {
    const { isAuthenticated, checkAuth } = useAuth();
    
    // Admin rotaları için auth kontrolü
    if (to.path.startsWith('/admin') && to.name !== 'AdminLogin') {
        const token = localStorage.getItem('authToken');
        
        if (!token) {
            next('/admin/login');
            return;
        }
        
        // Token varsa ancak user bilgisi yoksa auth durumunu kontrol et
        if (!isAuthenticated.value) {
            try {
                await checkAuth();
                if (!isAuthenticated.value) {
                    next('/admin/login');
                    return;
                }
            } catch (error) {
                next('/admin/login');
                return;
            }
        }
    }
    
    // Zaten giriş yapmışsa login sayfasına gidemesin
    if (to.name === 'AdminLogin' && isAuthenticated.value) {
        next('/admin/dashboard');
        return;
    }
    
    next();
});

export default router;
