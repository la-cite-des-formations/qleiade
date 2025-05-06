import React, { useEffect, useContext } from "react";
import Typography from "@mui/material/Typography";
import { useNavigate } from "react-router-dom";
import { StepContext } from "./Context";
import { useTranslation } from "react-i18next";

export default function Success({ label }) {
    const { t } = useTranslation('common');
    const nav = useNavigate();
    const { formValues } = useContext(StepContext);

    useEffect(() => {
        let form = {};
        Object.keys(formValues).map((name) => {
            form = {
                ...form,
                [name]: formValues[name].value
            };
            return form;
        });
        // Do whatever with the values
        let route = "/labels/" + label + "/audit/result"
        nav(route, { state: form });

    }, []);

    return (
        <>
            <Typography variant="h2" align="center" sx={{ py: 4 }}>
                {t("quality_label.audit.success.thankyou")}
            </Typography>
            <Typography component="p" align="center">
                {t("quality_label.audit.success.info")}
            </Typography>
        </>
    );
}
