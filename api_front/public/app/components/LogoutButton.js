import React from "react";
import { withSanctum } from "react-sanctum";
import { useNavigate } from "react-router-dom";
import { Button } from "@mui/material";
import { useTranslation } from "react-i18next";

const LogoutButton = ({ authenticated, user, signOut }) => {
    const { t } = useTranslation("common");
    const nav = useNavigate();
    const handleLogout = () => {

        signOut()
            .then(() => {
                window.alert("Signed out!");
                nav("/login");
            }
            )
            .catch(() => window.alert("not signed out"));
    };
    return <Button variant="outlined" onClick={handleLogout}>{t("signout")}</Button>;
};

export default withSanctum(LogoutButton);
