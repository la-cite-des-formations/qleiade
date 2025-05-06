import * as React from 'react';
import Router from '@services/router';
import { RouterProvider } from 'react-router-dom';
import localStore from '@services/localStore';

//MUI
import { ThemeProvider, createTheme } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';

import { ThemeContext } from '@services/Theme/ThemeContext';
import { getDesign } from '@services/theme/designer';

const ColorModeContext = ThemeContext;


export default function Main() {
    const [mode, setMode] = React.useState('light');

    const colorMode = React.useMemo(
        () => ({
            toggleColorMode: () => {
                setMode((prevMode) => {
                    let mode = "";
                    prevMode === 'light' ? mode = "dark" : mode = "light";
                    localStore.setLs("mode", "main", mode);
                    return mode;
                });

            },
        }),
        [],
    );

    const theme = React.useMemo(
        () =>
            createTheme(getDesign(mode)),
        [mode],
    );

    React.useEffect(() => {
        let mode = null;
        mode = localStore.getLs("mode", "main")
        if (mode) {
            setMode(mode);
        } else {
            mode = "ligth";
        }
    }, [])

    return (
        <ColorModeContext.Provider value={colorMode}>
            <ThemeProvider theme={theme}>
                <CssBaseline>
                    <RouterProvider router={Router} />
                </CssBaseline>
            </ThemeProvider>
        </ColorModeContext.Provider>
    );
}
