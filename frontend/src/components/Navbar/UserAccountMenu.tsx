import * as React from 'react';
import {
    Divider,
    IconButton, Link,
    Menu as MuiMenu,
    MenuItem
} from "@material-ui/core";
import {AccountBox} from "@material-ui/icons";
import {useHasClient, useHasRealmRole} from "../../hooks/useHasRole";
import {keycloakLinks} from "../../util/auth";

const UserAccountMenu = () => {
    const hasVideoCatalogAdmin = useHasRealmRole('video-catalog-admin');
    const hasAdminRealm = useHasClient('realm-management');
    const [anchorEl, setAnchorEl] = React.useState(null);
    const open = Boolean(anchorEl);

    const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
    const handleClose = () => setAnchorEl(null);

    if (!hasVideoCatalogAdmin) {
        return null;
    }

    return (
        <React.Fragment>
            <IconButton
                edge="end"
                color="inherit"
                aria-label="open drawer"
                aria-controls="menu-user-account"
                aria-haspopup="true"
                onClick={handleOpen}
            >
                <AccountBox/>
            </IconButton>
            <MuiMenu
                id="menu-user-account"
                open={open}
                anchorEl={anchorEl}
                onClose={handleClose}
                anchorOrigin={{vertical: 'bottom', horizontal: 'center'}}
                transformOrigin={{vertical: 'top', horizontal: 'center'}}
                getContentAnchorEl={null}
            >
                <MenuItem disabled={true}>Mateus</MenuItem>
                <Divider/>
                {
                    hasAdminRealm &&
                    (<MenuItem
                        component={Link}
                        href={keycloakLinks.adminConsole}
                        target="_blank"
                        rel="noopener"
                        onClick={handleClose}
                        color="textPrimary"
                    >
                        Admin
                    </MenuItem>)
                }
                <MenuItem
                    component={Link}
                    href={keycloakLinks.accountConsole}
                    target="_blank"
                    rel="noopener"
                    onClick={handleClose}
                    color="textPrimary"
                >
                    Minha Conta
                </MenuItem>
                <MenuItem>Logout</MenuItem>
            </MuiMenu>
        </React.Fragment>
    );
};
export default UserAccountMenu;