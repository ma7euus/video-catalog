import {RouteProps} from 'react-router-dom';
import Dashboard from "../pages/Dashboard";
import CategoryList from "../pages/category/List";

export interface AppRouteProps extends RouteProps {
    name: string;
    label: string;
}

const routes: AppRouteProps[] = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        name: 'categories.list',
        label: 'Listar Categorias',
        path: '/categories',
        component: CategoryList,
        exact: true
    }
];

export default routes;