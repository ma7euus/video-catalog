import {useKeycloak} from "@react-keycloak/web";
import * as React from "react";
import {Redirect, Route, RouteProps} from 'react-router-dom';
import {RouteComponentProps} from "react-router";

interface PrivateProps extends RouteProps {
    component: React.ComponentType<RouteComponentProps<any>> | React.ComponentType<any>;
};
const PrivateRoute: React.FC<PrivateProps> = (props) => {
    const {component: Component, ...rest} = props;
    const {keycloak} = useKeycloak();
    const render = React.useCallback((props) => {
        if (keycloak.authenticated) {
            return <Component {...props} />
        }
        return <Redirect to={{
            pathname: "/login",
            state: {from: props.location}
        }}/>;
    }, [keycloak]);
    return (
        <Route {...rest} render={render}/>
    );
};

export default PrivateRoute;