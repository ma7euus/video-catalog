import * as React from 'react';
import {Dialog, DialogTitle, DialogContent, DialogContentText, DialogActions, Button} from '@material-ui/core';

interface DeleteDialogProps {
    open: boolean;
    handleClose: (confirmed: boolean) => void;
    numRows?: number;
}

const DeleteDialog: React.FC<DeleteDialogProps> = (props) => {
    const {open, handleClose, numRows = 0} = props;

    return (
        <Dialog
            open={open}
            onClose={() => handleClose(false)}
        >
            <DialogTitle>Exclusão de registros</DialogTitle>
            <DialogContent>
                <DialogContentText>
                    Deseja realmente excluir esse(s)
                    {numRows! > 0 ? ` ${numRows}` : ''} registro(s)?
                </DialogContentText>
            </DialogContent>
            <DialogActions>
                <Button onClick={() => handleClose(false)} color="secondary">Cancelar</Button>
                <Button onClick={() => handleClose(true)} color="primary" autoFocus>Excluir</Button>
            </DialogActions>
        </Dialog>
    )
}

export default DeleteDialog