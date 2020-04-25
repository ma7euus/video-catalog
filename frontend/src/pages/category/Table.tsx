import * as React from 'react';
import {useEffect, useState} from "react";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import categoryHttp from "../../util/http/category-http";
import {BadgeNo, BadgeYes} from "../../components/Badge";
import EditIcon from '@material-ui/icons/Edit';
import {Category, ListResponse} from "../../util/models";
import DefaultTable, {makeActionStyles, MuiDataTableRefComponent, TableColumn} from "../../components/DefaultTable";
import {useSnackbar} from "notistack";
import {FilterResetButton} from "../../components/DefaultTable/FilterResetButton";
import {IconButton, MuiThemeProvider} from "@material-ui/core";
import {Link} from "react-router-dom";

interface SearchState {
    search: string
}

const columnsDefinition: TableColumn[] = [
    {
        name: "id",
        label: "ID",
        width: '30%',
        options: {
            sort: false
        }
    },
    {
        name: 'name',
        label: 'Nome',
        width: '43%'
    },
    {
        name: 'is_active',
        label: 'Ativo?',
        width: '4%',
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value ? <BadgeYes/> : <BadgeNo/>
            }
        }
    },
    {
        name: 'created_at',
        label: 'Criado em',
        width: '10%',
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>
            }
        }
    },
    {
        name: 'actions',
        label: 'Ações',
        width: '13%',
        options: {
            sort: false,
            customBodyRender: (value, tableMeta) => {
                return (
                    <IconButton
                        color={'secondary'}
                        component={Link}
                        to={`/categories/${tableMeta.rowData[0]}/edit`}
                    >
                        <EditIcon/>
                    </IconButton>
                )
            }
        }
    }
];

const debounceTime = 300
const debouncedSearchTime = 300
const rowsPerPage = 15
const rowsPerPageOptions = [10, 25, 50]

const Table = () => {

    const snackbar = useSnackbar();
    const subscribed = React.useRef(true);
    const [data, setData] = React.useState<Category[]>([]);
    const [loading, setLoading] = React.useState<boolean>(false);
    const [searchState, setSearchState] = React.useState<SearchState>({search: ''});
    //   const {openDeleteDialog, setOpenDeleteDialog, rowsToDelete, setRowsToDelete} = useDeleteCollection();
    const tableRef = React.useRef() as React.MutableRefObject<MuiDataTableRefComponent>;

    /* const {
         columns,
         filterManager,
         filterState,
         debouncedFilterState,
         dispatch,
         totalRecords,
         setTotalRecords} = useFilter({
         columns: columnsDefinition,
         debounceTime: debounceTime,
         rowsPerPage,
         rowsPerPageOptions,
         tableRef
     });*/

    useEffect(() => {
        subscribed.current = true;
        getData();
        return () => {
            subscribed.current = false;
        }
    }, [searchState]);

    async function getData() {
        setLoading(true);
        try {
            const {data} = await categoryHttp.list<ListResponse<Category>>({
                queryParams: {
                    search: searchState.search
                }
            });
            if (subscribed.current) {
                setData(data.data);
            }
        } catch (error) {
            console.log(error);
            snackbar.enqueueSnackbar(
                'Não foi possível carregar as categorias',
                {variant: 'error'}
            );
        } finally {
            setLoading(false);
        }
    }

    return (
        <MuiThemeProvider theme={makeActionStyles(columnsDefinition.length - 1)}>
            <DefaultTable
                columns={columnsDefinition}
                title=""
                data={data}
                loading={loading}
                debouncedSearchTime={debouncedSearchTime}
                ref={tableRef}
                options={{
                    serverSide: true,
                    searchText: searchState.search,
                    //  page: filterState.pagination.page - 1,
                    //  rowsPerPage: filterState.pagination.per_page,
                    //  count: totalRecords,
                    rowsPerPageOptions,
                    customToolbar: () => (
                        <FilterResetButton
                            handleClick={() => {
                                //  filterManager.resetFilter()
                            }}
                        />
                    ),
                    //onSearchChange: (value) => filterManager.changeSearch(value),
                    onSearchChange: (value) => setSearchState({search: value}),
                    //onChangePage:(page) => filterManager.changePage(page),
                    //onChangeRowsPerPage:(perPage) => filterManager.changeRowsPerPage(perPage),
                    //onColumnSortChange: (changedColumn: string, direction: string) => filterManager.changeColumnSort(changedColumn, direction),
                    onRowsDelete: (rowsDeleted: any[]) => {
                        //    setRowsToDelete(rowsDeleted as any)
                        return false
                    }
                }}
            />
        </MuiThemeProvider>
    );
};

export default Table;