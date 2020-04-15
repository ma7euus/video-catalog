import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {useEffect, useState} from "react";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import genreHttp from "../../util/http/genre-http";
import {Badge as CustomBadge} from "../../components/Badge";

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "categories",
        label: "Categorias",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                let key = 0;
                return value.map(
                    value => <CustomBadge key={key++} label={value.name}/>
                );
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
        let isSubscribed = true;
        (async () => {
            const {data} = await genreHttp.list();
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
            title="Listagem de Gêneros"
            columns={columnsDefinition}
            data={data}
        />
    );
};

export default Table;