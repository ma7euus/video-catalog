import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {useEffect, useState} from "react";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import categoryHttp from "../../util/http/category-http";
import {BadgeNo, BadgeYes} from "../../components/Badge";
import {Category, ListResponse} from "../../util/models";
import DefaultTable, {MuiDataTableRefComponent} from "../../components/DefaultTable";
import {useSnackbar} from "notistack";
import {FilterResetButton} from "../../components/DefaultTable/FilterResetButton";

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "is_active",
        label: "Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue): any {
                return value ? <BadgeYes/> : <BadgeNo/>;
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

const debounceTime = 300
const debouncedSearchTime = 300
const rowsPerPage = 15
const rowsPerPageOptions = [10, 25, 50]

const Table = () => {

    const snackbar = useSnackbar();
    const subscribed = React.useRef(true);
    const [data, setData] = React.useState<Category[]>([]);
    //  const loading = React.useContext(LoadingContext);
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
        let isSubscribed = true;
        (async () => {
            const {data} = await categoryHttp.list<ListResponse<Category>>();
            if (isSubscribed) {
                setData(data.data);
            }
        })();

        return () => {
            isSubscribed = false;
        }
    }, []);

    return (
        <DefaultTable
            columns={columnsDefinition}
            title=""
            data={data}
            //loading={loading}
            debouncedSearchTime={debouncedSearchTime}
            ref={tableRef}
            options={{
                serverSide: true,
                //  searchText: filterState.search as any,
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
                //onChangePage:(page) => filterManager.changePage(page),
                //onChangeRowsPerPage:(perPage) => filterManager.changeRowsPerPage(perPage),
                //onColumnSortChange: (changedColumn: string, direction: string) => filterManager.changeColumnSort(changedColumn, direction),
                onRowsDelete: (rowsDeleted: any[]) => {
                    //    setRowsToDelete(rowsDeleted as any)
                    return false
                }
            }}
        />
    );
};

export default Table;