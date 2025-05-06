import * as React from 'react';
import IconButton from '@mui/material/IconButton';
import { useTheme } from '@mui/material/styles';
import Brightness4Icon from '@mui/icons-material/Brightness4';
import Brightness7Icon from '@mui/icons-material/Brightness7';
import { ThemeContext } from '@services/Theme/ThemeContext';

export function SwitchColorTheme(ColorModeContext) {
    const theme = useTheme();
    const colorMode = React.useContext(ThemeContext);
    return (
        <div>
            {/* {theme.palette.mode} mode */}
            <IconButton sx={{ ml: 1 }} onClick={colorMode.toggleColorMode} color="inherit">
                {theme.palette.mode === 'dark' ? <Brightness7Icon /> : <Brightness4Icon />}
            </IconButton>
        </div>
    );
}
