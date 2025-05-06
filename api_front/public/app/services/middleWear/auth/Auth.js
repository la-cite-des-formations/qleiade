import React, { useEffect, useState } from "react";
import { Navigate, Outlet } from "react-router-dom";
import { withSanctum } from "react-sanctum";
import MyAppBar from "@parts/MyAppBar";
import API from "@services/axios"
import { AuthProvider } from "./context";

const Auth = ({ user }) => {
    let [page, setPage] = useState(false);

    useEffect(() => {
        if (!user) {
            API.get("/user")
                .then((response) => {
                    if (response.status === 200) {
                        setPage(
                            <AuthProvider user={response.data}>
                                <MyAppBar />
                                <Outlet />
                            </AuthProvider>
                        )
                    }
                })
                .catch(() => {
                    setPage(<Navigate to={'/login'} />);
                });
        } else {
            setPage(
                <AuthProvider user={user}>
                    <MyAppBar />
                    <Outlet />
                </AuthProvider>
            )
        }
    }, [user]);

    return page;
}

export default withSanctum(Auth);
