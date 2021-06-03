import {useKeycloak} from "@react-keycloak/web";
import {useMemo} from "react";

const useHasRealmRole = (role: string) => {
    const {keycloak, initialized} = useKeycloak();

    return useMemo(() => {
        if (!initialized || !keycloak.authenticated) {
            return false;
        }
        return keycloak.hasRealmRole(role);
    }, [initialized, keycloak, role]);
}

export default useHasRealmRole;