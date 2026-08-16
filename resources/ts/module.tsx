import type {RouteObject} from 'react-router-dom';
import type {MenuItem} from '@/types/Menu';
import type {ReactModule} from '@/types/Module';

import {BookOpen} from 'lucide-react';

import DashboardPage from './pages/Dashboard/DashboardPage';

const routes: RouteObject[] = [
    {
        path: 'migraine-diary',
        element: <DashboardPage/>,
        handle: {
            title: 'migrainediary.widget_title',
        },
    },
];

const navigation: MenuItem[] = [
    {
        id: 'migraine-diary',
        title: 'migrainediary.widget_title',
        route: '/migraine-diary',
        section: 'modules',
        order: 100,
        active: true,
        icon: 'migrainediary:book',
        roles: ['admin', 'user'],
    },
];

const icons = {
    book: BookOpen,
};

const moduleDefinition: ReactModule = {
    id: 'migrainediary',
    name: 'MigraineDiary',
    routes,
    navigation,
    icons,
};

export default moduleDefinition;