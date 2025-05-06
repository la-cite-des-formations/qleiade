import React, { useState, useEffect } from 'react';
import { Navigate, useLocation } from "react-router-dom";
import { withSanctum } from 'react-sanctum';
import API from '@services/axios';

import { Backdrop, CircularProgress } from '@mui/material';


const GoogleCallback = ({ setUser, authenticated }) => {

    const [loading, setLoading] = useState(true);
    const location = useLocation();

    useEffect(() => {

        API.get(`/auth/callback/google${location.search}`, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then((response) => {
            return response.data;
        }).then((data) => {
            setLoading(false);
            setUser(data, true);
        }).catch(new Error('Something went wrong!'));
    }, []);

    if (loading) {
        return (
            <div>
                <Backdrop
                    sx={{ color: '#fff', zIndex: (theme) => theme.zIndex.drawer + 1 }}
                    open={loading}
                >
                    <CircularProgress color="inherit" />
                </Backdrop>
            </div>
        );
    } else {
        if (!loading) {
            return <Navigate to={"/home"} />
        }
    }
}

export default withSanctum(GoogleCallback);
