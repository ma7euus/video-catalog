import * as React from 'react';
import {Page} from "../../components/Page";
import {Form} from "./Form";
import {useParams} from "react-router";

const PageForm = () => {
    const {id} = useParams();

    return (
        <Page title={!id ? 'Cadastrar Membro do Elenco' : 'Editar Membro do Elenco'}>
            <Form/>
        </Page>

    );
};

export default PageForm;