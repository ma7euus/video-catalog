import {useKeycloak} from "@react-keycloak/web";
import {useMemo} from "react";

export const useHasRealmRole = (role: string) => {
    const {keycloak, initialized} = useKeycloak();

    return useMemo(() => {
        if (!initialized || !keycloak.authenticated) {
            return false;
        }
        return keycloak.hasRealmRole(role);
    }, [initialized, keycloak, role]);
}

export const useHasClient = (clientName: string) => {
    const {keycloak, initialized} = useKeycloak();

    return useMemo(() => {
        if (!initialized || !keycloak.authenticated) {
            return false;
        }
        const countRoles = (keycloak as any).resourceAccess?.[clientName]?.roles?.length > 0;
        return countRoles;
    }, [initialized, keycloak, clientName]);
}