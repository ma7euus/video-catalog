import * as React from 'react';
import makeStyles from "@material-ui/core/styles/makeStyles";
import {createStyles, Theme, Container, Box, Typography} from "@material-ui/core";
import ExitToAppIcon from '@material-ui/icons/ExitToApp';
import {Link} from "react-router-dom";

const useStyles = makeStyles((theme: Theme) =>
    createStyles({
        paragraph: {
            display: 'flex',
            margin: theme.spacing(2),
            alignItems: 'center'
        }
    }),
);

interface NotAuthorizedProps {
};

const NotAuthorized: React.FC<NotAuthorizedProps> = (props) => {
    const classes = useStyles();
    return <Container>
        <Typography variant="h4" component="h1">
            403 - Acesso não autorizado
        </Typography>
        <Box className={classes.paragraph}>
            <ExitToAppIcon/>
            <Typography>
                Acesse o CodeFlix pelo <Link to={'/'}>endereço</Link>
            </Typography>
        </Box>
    </Container>;
};

export default NotAuthorized;