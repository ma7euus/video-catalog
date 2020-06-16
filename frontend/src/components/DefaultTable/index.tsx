import * as React from 'react';
import MUIDataTable, {MUIDataTableProps, MUIDataTableColumn, MUIDataTableOptions} from 'mui-datatables';
import {merge, omit, cloneDeep} from 'lodash';
import {useTheme, Theme, MuiThemeProvider, useMediaQuery} from '@material-ui/core';
import DebouncedTableSearch from './DebouncedTableSearch';
import HttpResource from "../../util/http/http-resource";
import useDeleteCollection from "../../hooks/useDeleteCollection";
import DeleteDialog from "../DeleteDialog";
import {useSnackbar} from "notistack";

export interface TableColumn extends MUIDataTableColumn {
    width?: string;
}

export interface MuiDataTableRefComponent {
    changePage: (page: number) => void;
    changeRowsPerPage: (rowsPerPage: number) => void;
}

export interface TableProps extends MUIDataTableProps, React.RefAttributes<MuiDataTableRefComponent> {
    columns: TableColumn[];
    loading?: boolean;
    debouncedSearchTime?: number;
    options?: TableOptionsProps;
}

export interface TableOptionsProps extends MUIDataTableOptions {
    deleteOptions?: DeleteOptionsProps;
}

export interface DeleteOptionsProps {
    resource?: HttpResource | any;
    prepare?: (a: any[]) => void;
}

const makeDefaultOptions = (defaultProps?: TableProps | null): MUIDataTableOptions => ({
    print: false,
    download: false,
    textLabels: {
        body: {
            noMatch: "Nenhum registro encontrado",
            toolTip: "Classificar"
        },
        pagination: {
            next: "Próxima página",
            previous: "Página anterior",
            rowsPerPage: "Por página:",
            displayRows: "de"
        },
        toolbar: {
            search: "Busca",
            downloadCsv: "Download CSV",
            print: "Imprimir",
            viewColumns: "Ver Colunas",
            filterTable: "Filtrar Tabelas"
        },
        filter: {
            all: "Todos",
            title: "FILTROS",
            reset: "LIMPAR"
        },
        viewColumns: {
            title: "Ver Colunas",
            titleAria: "Ver/Esconder Colunas da Tabela"
        },
        selectedRows: {
            text: "registro(s) selecionados",
            delete: "Excluir",
            deleteAria: "Excluir registros selecionados"
        }
    },
    customSearchRender: (searchText: string,
                         handleSearch: any,
                         hideSearch: any,
                         options: any) => {
        return <DebouncedTableSearch
            searchText={searchText}
            onSearch={handleSearch}
            onHide={hideSearch}
            options={options}
            debounceTime={defaultProps!.debouncedSearchTime}
        />
    },
    ...(defaultProps!.options!.deleteOptions && {
        onRowsDelete: (rowsDeleted: any[]) => {
            defaultProps!
                .options!
                .deleteOptions!
                .prepare!(rowsDeleted as any)
            return false
        }
    }),
});

const DefaultTable = React.forwardRef<MuiDataTableRefComponent, TableProps>((props, ref) => {
    const useDelete = props.options?.deleteOptions ? useDeleteCollection() : null;
    props.options!.deleteOptions!.prepare = useDelete!.setRowsToDelete as any;

    function extractMuiDataTableColumns(columns: TableColumn[]): MUIDataTableColumn[] {
        setColumnsWitdh(columns);
        return columns.map(column => omit(column, 'width'));
    }

    function setColumnsWitdh(columns: TableColumn[]) {
        columns.forEach((column, key) => {
            if (column.width) {
                const overrides = theme.overrides as any;
                overrides.MUIDataTableHeadCell.fixedHeaderCommon[`&:nth-child(${key + 2})`] = {
                    width: column.width
                }
            }
        })
    }

    function applyLoading() {
        const textLabels = (newProps.options as any).textLabels;

        textLabels.body.noMatch = newProps.loading === true ? 'Caregando...' : textLabels.body.noMatch;
    }

    function applyResponsive() {
        newProps.options.responsive = isSmOrDown ? 'scrollMaxHeight' : 'stacked';
    }

    function getOriginalMuiDataTableProps() {
        return {
            ...omit(newProps, 'loading'),
            ref
        }
    }

    const theme = cloneDeep<Theme>(useTheme());
    const isSmOrDown = useMediaQuery(theme.breakpoints.down('sm'));

    const defaultOptions = makeDefaultOptions(props);

    const newProps = merge(
        {options: cloneDeep(defaultOptions)},
        props,
        {columns: extractMuiDataTableColumns(props.columns)}
    );

    applyLoading();
    applyResponsive();

    const originalProps = getOriginalMuiDataTableProps();


    const snackbar = useSnackbar();

    function deleteRows(confirmed: boolean) {
        if (!confirmed) {
            useDelete!.setOpenDeleteDialog(false);
            return;
        }

        const ids = useDelete!.rowsToDelete!
            .data
            .map(value => (props.data[value.index] as any).id)
            .join(',');

        props.options!.deleteOptions!.resource!
            .deleteCollection({ids})
            .then(response => {
                snackbar.enqueueSnackbar('Registros excluidos com sucesso!', {
                    variant: 'success'
                });
                /*if (useDelete!.rowsToDelete.data.length === filterState.pagination.per_page && filterState.pagination.page > 1) {
                    const page = filterState.pagination.page - 2;
                    filterManager.changePage(page);
                } else {
                    getData();
                }*/
            })
            .catch(error => {
                console.error(error)
                snackbar.enqueueSnackbar('Não foi possível excluir os registros', {
                    variant: 'error'
                });
            });
    }

    return (
        <MuiThemeProvider theme={theme}>
            {
                props.options!.deleteOptions
                    && (<DeleteDialog
                    numRows={useDelete!.rowsToDelete.data.length}
                    open={useDelete!.openDeleteDialog}
                    handleClose={deleteRows}/>)
            }
            <MUIDataTable {...originalProps}/>
        </MuiThemeProvider>
    )
});

export default DefaultTable;

export function makeActionStyles(column) {
    return theme => {
        const copyTheme = cloneDeep<Theme>(theme);
        const selector = `&[data-testid^="MuiDataTableBodyCell-${column}"]`;
        (copyTheme.overrides as any).MUIDataTableBodyCell.root[selector] = {
            paddingTop: '0px',
            paddingBottom: '0px',
        };
        return copyTheme;
    }
}