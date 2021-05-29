import * as React from 'react';
import {Container} from "@material-ui/core";
import Typography from "@material-ui/core/Typography";

interface WaitingProps {
};
const Waiting: React.FC<WaitingProps> = (props) => {
    return (
        <Container>
            <Typography>Aguarde...</Typography>
        </Container>
    );
};
export default Waiting;