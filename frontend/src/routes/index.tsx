import {RouteProps} from 'react-router-dom';
import Dashboard from "../pages/Dashboard";
import CategoryList from "../pages/category/PageList";
import CategoryForm from "../pages/category/PageForm";
import GenreList from "../pages/genre/PageList";
import GenreForm from "../pages/genre/PageForm";
import CastMembersList from "../pages/cast-member/PageList";
import CastMembersForm from "../pages/cast-member/PageForm";
import VideoList from "../pages/video/PageList";
import VideoForm from "../pages/video/PageForm";
import Uploads from "../pages/uploads";
import Login from "../pages/Login";

export interface AppRouteProps extends RouteProps {
    name: string;
    label: string;
    auth?: boolean;
}

const routes: AppRouteProps[] = [
    {
        name: 'login',
        label: 'Login',
        path: '/login',
        component: Login,
        exact: true,
        auth: false,
    },
    {
        name: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true,
        auth: true,
    },
    {
        name: 'categories.list',
        label: 'Listar Categorias',
        path: '/categories',
        component: CategoryList,
        exact: true,
        auth: true,
    },
    {
        name: 'categories.create',
        label: 'Cadastrar Categoria',
        path: '/categories/create',
        component: CategoryForm,
        exact: true,
        auth: true,
    },
    {
        name: 'categories.edit',
        label: 'Editar Categoria',
        path: '/categories/:id/edit',
        component: CategoryForm,
        exact: true,
        auth: true,
    },
    {
        name: 'genres.list',
        label: 'Listar Gêneros',
        path: '/genres',
        component: GenreList,
        exact: true,
        auth: true,
    },
    {
        name: 'genres.create',
        label: 'Cadastrar Gênero',
        path: '/genres/create',
        component: GenreForm,
        exact: true,
        auth: true,
    },
    {
        name: 'genres.edit',
        label: 'Editar Gênero',
        path: '/genres/:id/edit',
        component: GenreForm,
        exact: true,
        auth: true,
    },
    {
        name: 'cast_members.list',
        label: 'Listar Membros do Elenco',
        path: '/cast-members',
        component: CastMembersList,
        exact: true,
        auth: true,
    },
    {
        name: 'cast_members.create',
        label: 'Cadastrar Membros do Elenco',
        path: '/cast-members/create',
        component: CastMembersForm,
        exact: true,
        auth: true,
    },
    {
        name: 'cast_members.edit',
        label: 'Editar Membros do Elenco',
        path: '/cast-members/:id/edit',
        component: CastMembersForm,
        exact: true,
        auth: true,
    },
    {
        name: 'videos.list',
        label: 'Listar vídeos',
        path: '/videos',
        component: VideoList,
        exact: true,
        auth: true,
    },
    {
        name: 'videos.create',
        label: 'Criar vídeo',
        path: '/videos/create',
        component: VideoForm,
        exact: true,
        auth: true,

    },
    {
        name: 'videos.edit',
        label: 'Editar vídeo',
        path: '/videos/:id/edit',
        component: VideoForm,
        exact: true,
        auth: true,
    },
    {
        name: 'uploads.list',
        label: 'Uploads',
        path: '/uploads',
        component: Uploads,
        exact: true,
        auth: true,
    }
];

export default routes;