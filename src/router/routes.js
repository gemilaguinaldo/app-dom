const routes = [
    {
        path: '/',
        component: () => import( 'layouts/auth' ),
        children: [
            { 
                path: '',
                name: 'login',
                component: () => import( 'pages/auths/login' ),
                meta: {
                    requiresIfLoggedIn: true,
                }
            },
        ]
    },
    {
        path: '/dashboard/',
        component: () => import( 'layouts/dashboard' ),
        children: [
            {
                path: '',
                name: 'home',
                component: () => import( 'pages/dashboards/home' ),
                meta: {
                    requiresAuth: true,
                    pageTitle: 'My Expenses'
                }
            },
            {
                path: 'user-management/roles',
                name: 'user-roles',
                component: () => import( 'pages/dashboards/user-managements/roles' ),
                meta: {
                    requiresAuth: true,
                    requiresAdmin: true,
                    parentMenu: 'User Management',
                    pageTitle: 'Roles'
                }
            },
            {
                path: 'user-management/users',
                name: 'users',
                component: () => import( 'pages/dashboards/user-managements/users' ),
                meta: {
                    requiresAuth: true,
                    requiresAdmin: true,
                    parentMenu: 'User Management',
                    pageTitle: 'Users'
                }
            },
            {
                path: 'user-management/change-password',
                name: 'change-password',
                component: () => import( 'pages/dashboards/user-managements/change-password' ),
                meta: {
                    requiresAuth: true,
                    parentMenu: 'User Management',
                    pageTitle: 'Change Password'
                }
            },
            {
                path: 'expense-management/categories',
                name: 'expense-categories',
                component: () => import( 'pages/dashboards/expense-managements/categories' ),
                meta: {
                    requiresAuth: true,
                    requiresAdmin: true,
                    parentMenu: 'Expense Management',
                    pageTitle: 'Expense Categories'
                }
            },
            {
                path: 'expense-management/expenses',
                name: 'expenses',
                component: () => import( 'pages/dashboards/expense-managements/expenses' ),
                meta: {
                    requiresAuth: true,
                    parentMenu: 'Expense Management',
                    pageTitle: 'Expenses'
                }
            }
        ]
    }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
