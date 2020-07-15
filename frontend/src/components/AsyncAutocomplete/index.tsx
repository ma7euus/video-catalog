import React, {useEffect, useState} from 'react';
import {CircularProgress, TextField, TextFieldProps} from '@material-ui/core';
import {Autocomplete, AutocompleteProps, UseAutocompleteProps} from '@material-ui/lab';
import {useDebounce} from 'use-debounce';

interface AsyncAutocompleteProps extends React.RefAttributes<AsyncAutocompleteComponent> {
    fetchOptions: (searchText) => Promise<any>;
    debounceTime?: number;
    TextFieldProps?: TextFieldProps;
    AutocompleteProps?: Omit<Omit<AutocompleteProps<any, any, any, any> & UseAutocompleteProps<any, any, any, any>, 'renderInput'>,
        'options'>;
}

export interface AsyncAutocompleteComponent {
    clear: () => void;
}

const AsyncAutocomplete = React.forwardRef<AsyncAutocompleteComponent, AsyncAutocompleteProps>((props, ref) => {
    const {AutocompleteProps, TextFieldProps, debounceTime, fetchOptions} = props;
    const {freeSolo = false, onOpen, onClose, onInputChange} = AutocompleteProps as any;
    const [open, setOpen] = useState(false);
    const [searchText, setSearchText] = useState('');
    const [debouncedSearchText] = useDebounce(searchText, debounceTime || 300);
    const [loading, setLoading] = useState(false);
    const [options, setOptions] = useState([]);

    const textFieldProps: TextFieldProps = {
        margin: 'normal',
        variant: 'outlined',
        fullWidth: true,
        InputLabelProps: {shrink: true},
        ...(TextFieldProps && {...TextFieldProps}),
    };

    const autoCompleteProps: AutocompleteProps<any, any, any, any> = {
        loadingText: 'Carregando...',
        noOptionsText: 'Nenhum item encontrado',
        ...(AutocompleteProps && {...AutocompleteProps}),
        open,
        options,
        loading,
        inputValue: searchText,
        onOpen() {
            setOpen(true);
            onOpen && onOpen();
        },
        onClose() {
            setOpen(false);
            onClose && onClose();
        },
        onInputChange(event, value) {
            setSearchText(value);
            onInputChange && onInputChange();
        },
        renderInput: (params) => (
            <TextField
                {...params}
                {...textFieldProps}
                InputProps={{
                    ...params.InputProps,
                    endAdornment: (
                        <>
                            {loading && <CircularProgress color="inherit" size={20}/>}
                            {params.InputProps.endAdornment}
                        </>
                    ),
                }}
            />
        ),
    };

    useEffect(() => {
        if (!open && !freeSolo) {
            setOptions([]);
        }
    }, [open, freeSolo]);

    useEffect(() => {
        if (!open || (searchText === '' && freeSolo)) return;

        let isSubscribed = true;

        (async () => {
            setLoading(true);

            try {
                const data = await fetchOptions(debouncedSearchText);

                if (isSubscribed) {
                    setOptions(data);
                }
            } finally {
                setLoading(false);
            }
        })();

        return () => {
            isSubscribed = false;
        };
    }, [freeSolo, debouncedSearchText, open, fetchOptions])

    React.useImperativeHandle(ref, () => ({
        clear: () => {
            setSearchText("")
            setOptions([])
        }
    }))

    return <Autocomplete {...autoCompleteProps} />;
});

export default AsyncAutocomplete;
