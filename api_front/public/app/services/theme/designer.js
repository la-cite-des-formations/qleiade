import { amber, blueGrey, deepOrange, grey, blue } from '@mui/material/colors';

//change color
export function getDesign(mode) {
    return {
        palette: {
            mode,
            // primary: {
            //     ...amber
            // },
            ...(mode === 'dark' && {
                background: {
                    // default: deepOrange[900],
                    // paper: deepOrange[900],
                    stepform: grey[900],
                    textArea: grey[500]
                },
            }),
            ...(mode === 'light' && {
                background: {
                    default: '#edeef0',
                    stepform: grey[50]
                },
            }),
            text: {
                ...(mode === 'light'
                    ? {
                        primary: grey[900],
                        secondary: grey[800],
                        hover: grey[900],
                        active: blue[900],
                    }
                    : {
                        primary: '#fff',
                        secondary: grey[500],
                        hover: blue[500],
                        active: blue[900],
                    }),
            },
        },
        shape: {
            borderRadius: 5,
        }
    };
}
