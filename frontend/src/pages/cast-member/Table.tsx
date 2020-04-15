import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {useEffect, useState} from "react";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import castMemberHttp from "../../util/http/cast-member-http";

const CastMembersTypeMap = {
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
            customBodyRender(value, tableMeta, updateValue) {
                return <span>{CastMembersTypeMap[value]}</span>;
            }
        }
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>;
            }
        }
    },
];

type Props = {};
const Table = (props: Props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        let isSubscribed = true;
        (async () => {
            const {data} = await castMemberHttp.list();
            if (isSubscribed) {
                setData(data.data);
            }
        })();
        return () => {
            isSubscribed = false;
        }
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