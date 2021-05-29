import * as React from 'react';
import {Chip, createMuiTheme, MuiThemeProvider, PropTypes} from '@material-ui/core';
import theme from '../../theme';

const localTheme = createMuiTheme({
    palette: {
        primary: theme.palette.success,
        secondary: theme.palette.error,
    },
});

export const BadgeYes = () => (
    <MuiThemeProvider theme={localTheme}>
        <Chip label="Sim" color="primary"/>
    </MuiThemeProvider>
);

export const BadgeNo = () => (
    <MuiThemeProvider theme={localTheme}>
        <Chip label="Não" color="secondary"/>
    </MuiThemeProvider>
);


type BadgeProps = {
    label: string,
    color?: Exclude<PropTypes.Color, 'inherit'>
};

export const Badge = (props: BadgeProps) => (
    <MuiThemeProvider
        theme={theme}
    >
        <Chip
            label={props.label}
            color={'color' in props ? props.color : "default"}
        />
    </MuiThemeProvider>
);