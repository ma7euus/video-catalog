import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {useEffect, useState} from "react";
import {httpVideo} from "../../util/http";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";

const CastMembersTypeMap: any = {
    1: 'Diretor',
    2: 'Ator'
};

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "type",
        label: "Tipo",
        options: {
            customBodyRender(value, tableMeta, updateValue): any {
                return <span>{CastMembersTypeMap[value]}</span>;
            }
        }
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue): any {
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>;
            }
        }
    },
];

type Props = {};
const Table = (props: Props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        httpVideo.get('cast_members').then(
            response => setData(response.data.data)
        );
    }, []);

    return (
        <MUIDataTable
            title="Listagem de Membros do Elenco"
            columns={columnsDefinition}
            data={data}
        />
    );
};

export default Table;