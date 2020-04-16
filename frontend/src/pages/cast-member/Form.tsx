import * as React from 'react';
import {
    Box,
    Button,
    ButtonProps,
    FormControl, FormControlLabel, FormHelperText,
    FormLabel, Radio,
    RadioGroup,
    TextField,
    Theme
} from "@material-ui/core";
import {makeStyles} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import castMemberHttp from "../../util/http/cast-member-http";
import {useEffect} from "react";
import * as Yup from "../../util/vendor/yup";
import {useParams, useHistory} from "react-router";
import {useSnackbar} from "notistack";
import {CastMember} from "../../util/models";

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
    type: Yup.string()
        .label('Tipo')
        .required(),
});

export const Form = () => {
    const classes = useStyles();

    const {register, handleSubmit, getValues, setValue, errors, reset, watch} = useForm({
        validationSchema
    });

    const snackbar = useSnackbar();
    const history = useHistory();
    const {id} = useParams();
    const [castMember, setCastMember] = React.useState<CastMember | null>(null);
    const [loading, setLoading] = React.useState<boolean>(false);
    const handleChange = event => setValue('type', parseInt(event.target.value));

    const buttonProps: ButtonProps = {
        className: classes.submit,
        color: "secondary",
        variant: "contained",
        disabled: loading,
    };

    useEffect(() => {
        register({name: "type"})
    }, [register]);

    React.useEffect(() => {
        let isSubscribed = true;

        if (!id) {
            return
        }

        (async () => {
            setLoading(true);

            try {
                const {data} = await castMemberHttp.get(id);
                if (isSubscribed) {
                    setCastMember(data.data);
                    reset(data.data);
                }
            } catch (error) {
                console.error(error);
                snackbar.enqueueSnackbar('Não foi possível carregar as informações.', {
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
            const http = !castMember ? castMemberHttp.create(formData) : castMemberHttp.update(castMember.id, formData);
            const {data} = await http;

            snackbar.enqueueSnackbar('Membro de elenco salvo com sucesso!', {
                variant: 'success'
            })

            setTimeout(() => {
                event
                    ? (
                        id
                            ? history.replace(`/cast-members/${data.data.id}/edit`)
                            : history.push(`/cast-members/${data.data.id}/edit`)
                    ) : history.push('/cast-members');
            })

        } catch (error) {
            console.error(error)
            snackbar.enqueueSnackbar('Não foi possível salvar o membro do elenco', {
                variant: 'error'
            })
        } finally {
            setLoading(false);
        }
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
            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
                <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};