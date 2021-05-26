import React from 'react';
import './App.css';
import {Navbar} from "./components/Navbar";
import {Box, CssBaseline, MuiThemeProvider} from "@material-ui/core";
import {BrowserRouter} from "react-router-dom";
import AppRouter from "./routes/AppRouter";
import theme from "./theme";
import {SnackbarProvider} from "./components/SnackbarProvider";
import Breadcrumbs from "./components/Breadcrumbs";
import Spinner from "./components/Spinner";
import {LoadingProvider} from "./components/Loading/LoadingProvider";
import {ReactKeycloakProvider} from "@react-keycloak/web";
import {keycloak, keycloakConfig} from "./util/auth";

function App() {
    return (
        <ReactKeycloakProvider authClient={keycloak} initOptions={keycloakConfig}>
            <LoadingProvider>
                <MuiThemeProvider theme={theme}>
                    <SnackbarProvider>
                        <CssBaseline/>
                        <BrowserRouter basename="/admin">
                            <Spinner/>
                            <Navbar/>
                            <Box paddingTop={'70px'}>
                                <Breadcrumbs/>
                                <AppRouter/>
                            </Box>
                        </BrowserRouter>
                    </SnackbarProvider>
                </MuiThemeProvider>
            </LoadingProvider>
        </ReactKeycloakProvider>
    );
}

export default App;
