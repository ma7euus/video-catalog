import * as React from 'react';
import {MenuItem, TextField} from "@material-ui/core";
import {useForm} from "react-hook-form";
import genreHttp from "../../util/http/genre-http";
import {useEffect} from "react";
import categoryHttp from "../../util/http/category-http";
import * as Yup from "../../util/vendor/yup";
import {useParams, useHistory} from "react-router";
import {useSnackbar} from "notistack";
import {Category, Genre} from "../../util/models";
import SubmitActions from "../../components/SubmitActions";
import DefaultForm from "../../components/DefaultForm";
import useSnackbarFormError from "../../hooks/useSnackbarFormError";

const validationSchema = Yup.object().shape({
    name: Yup.string()
        .label('Nome')
        .required()
        .max(255),
    categories_id: Yup.array()
        .label('Categorias')
        .required(),
});

export const Form = () => {

    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch,
        triggerValidation,
        formState
    } = useForm({
        validationSchema,
        defaultValues: {
            name: '',
            categories_id: []
        }
    });
    useSnackbarFormError(formState.submitCount, errors);

    const snackbar = useSnackbar();
    const history = useHistory();
    const {id} = useParams();
    const [genre, setGenre] = React.useState<Genre | null>(null);
    const [categories, setCategories] = React.useState<Category[]>([]);
    const [loading, setLoading] = React.useState<boolean>(false);

    const handleChange = event => setValue('categories_id', event.target.value);

    useEffect(() => {
        register({name: "categories_id"});
    }, [register]);

    React.useEffect(() => {
        let isSubscribed = true;

        (async () => {
            setLoading(true);
            const promises = [categoryHttp.list({queryParams: {all: ''}})];
            if (id) {
                promises.push(genreHttp.get(id));
            }

            try {
                const [categoryResponse, genreResponse] = await Promise.all(promises)
                if (isSubscribed) {
                    setCategories(categoryResponse.data.data);

                    if (id) {
                        setGenre(genreResponse.data.data);
                        reset({
                            ...genreResponse.data.data,
                            categories_id: genreResponse.data.data.categories.map(category => category.id)
                        });
                    }
                }
            } catch (error) {
                console.error(error);
                snackbar.enqueueSnackbar('Não foi possível carregar as categorias', {
                    variant: 'error'
                });
            } finally {
                setLoading(false);
            }
        })();

        return () => {
            isSubscribed = false;
        }
    }, [id, reset, snackbar]);

    async function onSubmit(formData, event) {
        setLoading(true);

        try {
            const http = !genre ? genreHttp.create(formData) : genreHttp.update(genre.id, formData)
            const {data} = await http;

            snackbar.enqueueSnackbar('Gênero salvo com sucesso!', {
                variant: 'success'
            });

            setTimeout(() => {
                event
                    ? (
                        id
                            ? history.replace(`/genres/${data.data.id}/edit`)
                            : history.push(`/genres/${data.data.id}/edit`)
                    ) : history.push('/genres');
            })
        } catch (error) {
            console.error(error);
            snackbar.enqueueSnackbar('Não foi possível salvar o gênero', {
                variant: 'error'
            })
        } finally {
            setLoading(false);
        }
    }

    return (
        <DefaultForm
            GridItemProps={{xs: 12, md: 6}}
            onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                label="Nome"
                fullWidth
                variant="outlined"
                disabled={loading}
                inputRef={register}
                error={errors.name !== undefined}
                helperText={errors.name && errors.name.message}
                InputLabelProps={{shrink: true}}
            />

            <TextField
                name="categories_id"
                label="Categorias"
                select
                SelectProps={{
                    multiple: true
                }}
                value={watch('categories_id')}
                fullWidth
                variant="outlined"
                margin="normal"
                onChange={handleChange}
                disabled={loading}
                error={errors.categories_id !== undefined}
                helperText={errors.categories_id && (errors.categories_id as any).messages}
                InputLabelProps={{shrink: true}}
            >
                {
                    categories.map((category, index) => (
                        <MenuItem key={index} value={category.id}>{category.name}</MenuItem>
                    ))
                }
            </TextField>
            <SubmitActions disabledButtons={loading}
                           handleSave={() =>
                               triggerValidation().then(isValid => {
                                   isValid && onSubmit(getValues(), null)
                               })
                           }
            />
        </DefaultForm>
    );
};