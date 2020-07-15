import axios from 'axios';
import {useSnackbar} from 'notistack';
import {useCallback} from "react";

const useHttpHandled = () => {
    const {enqueueSnackbar} = useSnackbar();

    return useCallback(async (request: Promise<any>) => {
        try {
            return await request;
        } catch (e) {
            if (!axios.isCancel(e)) {
                enqueueSnackbar('Não foi possível carregar as informações.', {variant: 'error'});
            }
            throw e;
        }
    }, [enqueueSnackbar]);
};

export default useHttpHandled;
