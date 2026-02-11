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
                        const userData = response.data;
                        if (userData.permissions && userData.permissions['public_home']) {
                            setPage(
                                <AuthProvider user={userData}>
                                    <MyAppBar />
                                    <Outlet />
                                </AuthProvider>
                            )
                        } else {
                            // Pas d'accès à l'interface publique -> redirection vers la page Blade 403
                            window.location.href = "/access-denied";
                        }
                    }
                })
                .catch(() => {
                    window.location.href = "/login";
                });
        } else {
            // Si l'utilisateur est chargé, on vérifie s'il a accès à l'interface publique
            if (user.permissions && user.permissions['public_home']) {
                setPage(
                    <AuthProvider user={user}>
                        <MyAppBar />
                        <Outlet />
                    </AuthProvider>
                )
            } else {
                // Pas d'accès à l'interface publique -> redirection vers la page Blade 403
                window.location.href = "/access-denied";
            }
        }
    }, [user]);

    return page;
}

export default withSanctum(Auth);
