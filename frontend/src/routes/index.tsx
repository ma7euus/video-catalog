import {RouteProps} from 'react-router-dom';
import Dashboard from "../pages/Dashboard";
import CategoryList from "../pages/category/PageList";
import CategoryForm from "../pages/category/PageForm";
import GenreList from "../pages/genre/PageList";
import GenreForm from "../pages/genre/PageForm";
import CastMembersList from "../pages/cast-member/PageList";
import CastMembersForm from "../pages/cast-member/PageForm";

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
    },
    {
        name: 'categories.create',
        label: 'Cadastrar Categoria',
        path: '/categories/create',
        component: CategoryForm,
        exact: true
    },
    {
        name: 'categories.edit',
        label: 'Editar Categoria',
        path: '/categories/:id/edit',
        component: CategoryForm,
        exact: true
    },
    {
        name: 'genres.list',
        label: 'Listar Gêneros',
        path: '/genres',
        component: GenreList,
        exact: true
    },
    {
        name: 'genres.create',
        label: 'Cadastrar Gênero',
        path: '/genres/create',
        component: GenreForm,
        exact: true
    },
    {
        name: 'genres.edit',
        label: 'Editar Gênero',
        path: '/genres/:id/edit',
        component: GenreForm,
        exact: true
    },
    {
        name: 'cast_members.list',
        label: 'Listar Membros do Elenco',
        path: '/cast_members',
        component: CastMembersList,
        exact: true
    },
    {
        name: 'cast_members.create',
        label: 'Cadastrar Membros do Elenco',
        path: '/cast_members/create',
        component: CastMembersForm,
        exact: true
    },
    {
        name: 'cast_members.edit',
        label: 'Editar Membros do Elenco',
        path: '/cast_members/:id/edit',
        component: CastMembersForm,
        exact: true
    }
];

export default routes;