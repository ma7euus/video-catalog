import {useKeycloak} from "@react-keycloak/web";
import * as React from 'react';
import {Button} from "@material-ui/core";

interface LoginButtonProps {
};
const LoginButton: React.FC<LoginButtonProps> = (props) => {
    const {keycloak, initialized} = useKeycloak();

    if (!initialized || keycloak.authenticated) {
        return null;
    }

    return (
        <Button color="inherit">Login</Button>
    );
};

export default LoginButton;