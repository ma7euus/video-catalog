import * as React from 'react';
import {
    FormControl, FormControlLabel, FormHelperText,
    FormLabel, Radio,
    RadioGroup, TextField,
} from "@material-ui/core";
import {useForm} from "react-hook-form";
import castMemberHttp from "../../util/http/cast-member-http";
import {useContext, useEffect} from "react";
import * as Yup from "../../util/vendor/yup";
import {useParams, useHistory} from "react-router";
import {useSnackbar} from "notistack";
import {CastMember} from "../../util/models";
import SubmitActions from "../../components/SubmitActions";
import DefaultForm from "../../components/DefaultForm";
import useSnackbarFormError from "../../hooks/useSnackbarFormError";
import LoadingContext from "../../components/Loading/LoadigContext";

const validationSchema = Yup.object().shape({
    name: Yup.string()
        .label('Nome')
        .max(255)
        .required(),
    type: Yup.string()
        .label('Tipo')
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
    } = useForm<{name, type}>({
        validationSchema
    });
    useSnackbarFormError(formState.submitCount, errors);

    const {enqueueSnackbar} = useSnackbar();
    const history = useHistory();
    const {id} = useParams();
    const [castMember, setCastMember] = React.useState<CastMember | null>(null);
    const loading = useContext(LoadingContext);
    const handleChange = event => setValue('type', parseInt(event.target.value));

    useEffect(() => {
        register({name: "type"})
    }, [register]);

    React.useEffect(() => {
        let isSubscribed = true;

        if (!id) {
            return
        }

        (async () => {
            try {
                const {data} = await castMemberHttp.get(id);
                if (isSubscribed) {
                    setCastMember(data.data);
                    reset(data.data);
                }
            } catch (error) {
                console.error(error);
                enqueueSnackbar('Não foi possível carregar as informações.', {
                    variant: 'error'
                })
            }
        })();

        return () => {
            isSubscribed = false;
        }
    }, [id, reset, enqueueSnackbar]);

    async function onSubmit(formData, event) {
        try {
            const http = !castMember ? castMemberHttp.create(formData) : castMemberHttp.update(castMember.id, formData);
            const {data} = await http;

            enqueueSnackbar('Membro de elenco salvo com sucesso!', {
                variant: 'success'
            });

            setTimeout(() => {
                event
                    ? (
                        id
                            ? history.replace(`/cast-members/${data.data.id}/edit`)
                            : history.push(`/cast-members/${data.data.id}/edit`)
                    ) : history.push('/cast-members');
            });

        } catch (error) {
            console.error(error)
            enqueueSnackbar('Não foi possível salvar o membro do elenco', {
                variant: 'error'
            });
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
            <FormControl
                margin="normal"
                error={errors.type !== undefined}
                disabled={loading}
            >
                <FormLabel component="legend">Tipo</FormLabel>
                <RadioGroup
                    defaultValue="1"
                    aria-label="type"
                    name="type"
                    onChange={handleChange}
                    value={watch('type') + ""}
                >
                    <FormControlLabel
                        value="1"
                        label="Diretor"
                        control={<Radio/>}
                    />
                    <FormControlLabel
                        value="2"
                        label="Ator"
                        control={<Radio/>}
                    />
                </RadioGroup>
                {
                    errors.type && <FormHelperText id="type-helper-text">{errors.type.message}</FormHelperText>
                }
            </FormControl>
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