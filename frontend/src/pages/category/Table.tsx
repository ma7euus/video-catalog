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

interface Pagination {
    page: number;
    total: number;
    per_page: number;
}

interface Order {
    sort: string | null;
    dir: string | null;
}

interface SearchState {
    search: string;
    pagination: Pagination;
    order: Order;
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

const debouncedSearchTime = 500;
const rowsPerPageOptions = [10, 25, 50];

const Table = () => {

    const initialState: SearchState = {
        search: '',
        pagination: {
            page: 1,
            total: 0,
            per_page: 10,
        },
        order: {
            sort: null,
            dir: null,
        }
    };
    const snackbar = useSnackbar();
    const subscribed = React.useRef(true);
    const [data, setData] = React.useState<Category[]>([]);
    const [loading, setLoading] = React.useState<boolean>(false);
    const [searchState, setSearchState] = React.useState<SearchState>(initialState);
    //   const {openDeleteDialog, setOpenDeleteDialog, rowsToDelete, setRowsToDelete} = useDeleteCollection();
    const tableRef = React.useRef() as React.MutableRefObject<MuiDataTableRefComponent>;

    const columns = columnsDefinition.map(column => {
        return column.name === searchState.order.sort
            ? {
                ...column,
                options: {
                    ...column.options,
                    sortDirection: searchState.order.dir as any
                }
            }
            : column;
    });

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
    }, [
        searchState.search,
        searchState.pagination.page,
        searchState.pagination.per_page,
        searchState.order,
    ]);

    async function getData() {
        setLoading(true);
        try {
            const {data} = await categoryHttp.list<ListResponse<Category>>({
                queryParams: {
                    search: cleanSearchText(searchState.search),
                    page: searchState.pagination.page,
                    per_page: searchState.pagination.per_page,
                    sort: searchState.order.sort,
                    dir: searchState.order.dir,
                }
            });
            if (subscribed.current) {
                setData(data.data);
                setSearchState((prevState => ({
                    ...prevState,
                    pagination: {
                        ...prevState.pagination,
                        total: data.meta.total,
                    }
                })));
            }
        } catch (error) {
            console.log(error);
            if (categoryHttp.isCancelledRequest(error)) {
                return;
            }
            snackbar.enqueueSnackbar(
                'Não foi possível carregar as categorias',
                {variant: 'error'}
            );
        } finally {
            setLoading(false);
        }
    }

    function cleanSearchText(text) {
        let newText = text;
        if (text && text.value !== undefined) {
            newText = text.value;
        }
        return newText;
    }
    
    return (
        <MuiThemeProvider theme={makeActionStyles(columnsDefinition.length - 1)}>
            <DefaultTable
                columns={columns}
                title=""
                data={data}
                loading={loading}
                debouncedSearchTime={debouncedSearchTime}
                ref={tableRef}
                options={{
                    serverSide: true,
                    searchText: searchState.search,
                    page: searchState.pagination.page - 1,
                    rowsPerPage: searchState.pagination.per_page,
                    count: searchState.pagination.total,
                    rowsPerPageOptions,
                    customToolbar: () => (
                        <FilterResetButton
                            handleClick={() => {
                                setSearchState({
                                        ...initialState,
                                        search: {
                                            value: initialState.search,
                                            updated: true,
                                        } as any
                                    }
                                );
                            }
                            }
                        />
                    ),
                    //onSearchChange: (value) => filterManager.changeSearch(value),
                    onSearchChange: (value) => setSearchState((
                        prevState => ({
                            ...prevState,
                            search: value,
                            pagination: {
                                ...prevState.pagination,
                                page: 1,
                            }
                        })
                    )),
                    onChangePage: (page) => setSearchState((
                        prevState => ({
                            ...prevState,
                            pagination: {
                                ...prevState.pagination,
                                page: page + 1,
                            }
                        })
                    )),
                    onChangeRowsPerPage: (perPage) => setSearchState((
                        prevState => ({
                            ...prevState,
                            pagination: {
                                ...prevState.pagination,
                                per_page: perPage,
                            }
                        })
                    )),
                    onColumnSortChange: (changedColumn: string, direction: string) => setSearchState((
                        prevState => ({
                            ...prevState,
                            order: {
                                sort: changedColumn,
                                dir: direction.includes('desc') ? 'desc' : 'asc',
                            }
                        })
                    )),
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