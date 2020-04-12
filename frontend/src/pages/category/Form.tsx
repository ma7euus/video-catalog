import * as React from 'react';
import {Box, Button, ButtonProps, Checkbox, FormControlLabel, TextField, Theme} from "@material-ui/core";
import {makeStyles} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import categoryHttp from "../../util/http/category-http";
import * as Yup from '../../util/vendor/yup';
import {useEffect, useState} from "react";
import {useParams, useHistory} from "react-router";
import {useSnackbar} from "notistack";

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const validationSchema = Yup.object().shape({
    name: Yup.string()
        .label('Nome')
        .max(255)
        .required(),
});

export const Form: React.FC = () => {
    const classes = useStyles();

    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch
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
    const [category, setCategory] = useState<{ id: string } | null>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        color: "secondary",
        variant: "contained",
        disabled: loading,
    };

    useEffect(() => {
        register({name: "is_active"})
    }, [register]);

    useEffect(() => {
        if (!id) {
            return;
        }
        setLoading(true);
        categoryHttp
            .get(id)
            .then(({data}) => {
                setCategory(data.data);
                reset(data.data);
            })
            .finally(() => setLoading(false));
    }, [id, reset]);

    function onSubmit(formData, event) {
        setLoading(true);
        const http = !category
            ? categoryHttp.create(formData)
            : categoryHttp.update(category.id, formData);
        http.then(
            (response) => {
                snackbar.enqueueSnackbar(
                    "Categoria salva com sucesso!",
                    {variant: 'success'}
                );
                setTimeout(() => {
                    event
                        ? id
                        ? history.replace(`/categories/${response.data.data.id}/edit`)
                        : history.push(`/categories/${response.data.data.id}/edit`)
                        : history.push('/categories');
                });
            })
            .catch((error) => {
                console.log(error);
                snackbar.enqueueSnackbar(
                    "Não foi possível salvar a categoria",
                    {variant: 'error'}
                );
            })
            .finally(() => setLoading(false));
        ;
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
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
            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
                <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};