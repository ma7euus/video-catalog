import * as React from 'react';
import {Checkbox, FormControlLabel, TextField} from "@material-ui/core";
import {useForm} from "react-hook-form";
import categoryHttp from "../../util/http/category-http";
import * as Yup from '../../util/vendor/yup';
import {useEffect, useState} from "react";
import {useParams, useHistory} from "react-router";
import {useSnackbar} from "notistack";
import {Category, GetResponse} from "../../util/models";
import SubmitActions from "../../components/SubmitActions";
import DefaultForm from "../../components/DefaultForm";

const validationSchema = Yup.object().shape({
    name: Yup.string()
        .label('Nome')
        .max(255)
        .required(),
});

export const Form: React.FC = () => {
    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch,
        triggerValidation,
    } = useForm({
        validationSchema,
        defaultValues: {
            name: '',
            is_active: true
        }
    });

    const snackbar = useSnackbar();
    const history = useHistory();
    const {id} = useParams();
    const [category, setCategory] = useState<Category | null>(null);
    const [loading, setLoading] = useState<boolean>(false);

    useEffect(() => {
        register({name: "is_active"})
    }, [register]);

    useEffect(() => {
        let isSubscribed = true;

        if (!id) {
            return;
        }

        (async () => {
            setLoading(true);
            try {
                const {data} = await categoryHttp.get<GetResponse<Category>>(id);
                if (isSubscribed) {
                    setCategory(data.data);
                    reset(data.data);
                }
            } catch (error) {
                console.error(error);
                snackbar.enqueueSnackbar('Não foi possível carregar a categoria', {
                    variant: 'error'
                })
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
            const http = !category ? categoryHttp.create(formData) : categoryHttp.update(category.id, formData);
            const {data} = await http;


            snackbar.enqueueSnackbar(
                "Categoria salva com sucesso!",
                {variant: 'success'}
            );

            setTimeout(() => {
                event
                    ? id
                    ? history.replace(`/categories/${data.data.id}/edit`)
                    : history.push(`/categories/${data.data.id}/edit`)
                    : history.push('/categories');
            });
        } catch (error) {
            console.log(error);
            snackbar.enqueueSnackbar(
                "Não foi possível salvar a categoria",
                {variant: 'error'}
            );
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
                variant={"outlined"}
                inputRef={register}
                disabled={loading}
                error={errors.name !== undefined}
                helperText={errors.name && errors.name.message}
                InputLabelProps={{shrink: true}}
            />
            <TextField
                name="description"
                label="Descrição"
                multiline
                rows="4"
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                inputRef={register}
                disabled={loading}
                InputLabelProps={{shrink: true}}
            />
            <FormControlLabel
                disabled={loading}
                control={
                    <Checkbox
                        name="is_active"
                        color={"primary"}
                        onChange={
                            () => setValue('is_active', !getValues()['is_active'])
                        }
                        checked={watch('is_active')}
                    />
                }
                label={'Ativo?'}
                labelPlacement={'end'}
            />
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