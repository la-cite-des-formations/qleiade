import React, { useState, useEffect } from "react";
import { withSanctum } from "react-sanctum";
import { useNavigate } from "react-router-dom";
// MUI
import GoogleIcon from '@mui/icons-material/Google';
import { Divider, Button, Typography, Box } from "@mui/material";
import API from '@services/axios';

const LoginWithGoogle = (authenticated, user, setUser) => {

    const [loginUrl, setLoginUrl] = useState(null);

    useEffect(() => {
        API.get('/redirect/google', {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then((response) => {
                if (response.status === 200) {
                    return response.data;
                }
                throw new Error('Something went wrong!');
            })
            .then((data) => setLoginUrl(data.url))
            .catch((error) => { throw new Error('Something went wrong!') });
    }, []);

    return (
        <div>
            {loginUrl != null ?
                <Button href={loginUrl} startIcon={<GoogleIcon />}> accès page Google </Button> : <div />
            }
        </div>

    );
}

export default withSanctum(LoginWithGoogle);
