import {useKeycloak} from "@react-keycloak/web";
import * as React from "react";
import {Redirect, Route, RouteProps} from 'react-router-dom';
import {RouteComponentProps} from "react-router";
import {userHasRealmRole} from "../hooks/useHasRole";
import NotAuthorized from "../pages/NotAuthorized";

interface PrivateProps extends RouteProps {
    component: React.ComponentType<RouteComponentProps<any>> | React.ComponentType<any>;
};
const PrivateRoute: React.FC<PrivateProps> = (props) => {
    const {component: Component, ...rest} = props;
    const {keycloak} = useKeycloak();
    const hasVideoCatalogAdmin = userHasRealmRole(process.env.REACT_APP_ADMIN_ROLE);
    const render = React.useCallback((props) => {
        if (keycloak.authenticated) {
            return hasCatalogAdmin ? <Component {...props} /> : <NotAuthorized/>;
        }
        return <Redirect to={{
            pathname: "/login",
            state: {from: props.location}
        }}/>;
    }, [keycloak, hasVideoCatalogAdmin]);
    return (
        <Route {...rest} render={render}/>
    );
};

export default PrivateRoute;