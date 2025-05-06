import React from "react";
import { SnackbarProvider, closeSnackbar } from 'notistack';
import { IconButton } from "@mui/material";
import CloseIcon from '@mui/icons-material/Close';


export default function Toaster({ children }) {
    return (
        <SnackbarProvider
            autoHideDuration={4000}
            // preventDuplicate
            action={(snackbarId) => (
                <IconButton onClick={() => closeSnackbar(snackbarId)}>
                    <CloseIcon />
                </IconButton>
            )}
            anchorOrigin={{
                vertical: 'top',
                horizontal: 'right',
            }}
            maxSnack={5}
        >
            {children}
        </SnackbarProvider>
    );
}
