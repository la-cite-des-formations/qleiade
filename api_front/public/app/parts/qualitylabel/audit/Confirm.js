import React, { useContext, useEffect, useState } from "react";
import { StepContext } from "./Context";
import { Box, Button, Typography } from "@mui/material";
import { useTranslation } from "react-i18next";
import SyntheticView from "@components/SyntheticView";

export default function Confirm() {
    const { t } = useTranslation("common");
    const { formValues, handleBack, handleNext } = useContext(StepContext);
    const [syntheticViewMapper, setSyntheticViewMapper] = useState([]);
    const { since, until, students, formations, groups, extern, qualityLabel } = formValues;

    const handleSubmit = () => {
        handleNext();
    };

    useEffect(() => {
        setSyntheticViewMapper([
            {
                type: "defaultItem",
                primary: "quality_label.audit.confirm.audit_type",
                secondary: extern.value ? "Auditeur externe" : "Auditeur interne",
                loaded: formValues,
                withDivider: true
            },
            {
                type: "defaultItem",
                primary: "quality_label.audit.since",
                secondary: since.value,
                loaded: formValues,
                withDivider: true
            },
            {
                type: "defaultItem",
                primary: "quality_label.audit.until",
                secondary: until.value,
                loaded: formValues,
                withDivider: true
            },
            {
                type: "defaultItem",
                primary: "quality_label.audit.confirm.formations",
                secondary: formations.value.length,
                loaded: formValues,
                withDivider: true
            },
            {
                type: "defaultItem",
                primary: "quality_label.audit.confirm.groups",
                secondary: groups.value.length,
                loaded: formValues,
                withDivider: true
            },
            {
                type: "defaultItem",
                primary: "quality_label.audit.confirm.students",
                secondary: students.value.length,
                loaded: formValues,
                withDivider: true
            }
        ]);
    }, [formValues])

    return (
        <>
            <Typography variant="h5" align="center" sx={{ mt: 2 }}>
                {t("quality_label.audit.confirm.subtitle")}
            </Typography>
            <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                {t("quality_label.audit.confirm.description")}
            </Typography>
            <SyntheticView mapper={syntheticViewMapper} />
            <Box sx={{ display: "flex", justifyContent: "flex-end", mt: 3 }}>
                <Button sx={{ mr: 1 }} onClick={handleBack}>
                    {t('back')}
                </Button>
                <Button variant="contained" color="success" onClick={handleSubmit}>
                    {t('quality_label.audit.confirm.continue')}
                </Button>
            </Box>
        </>
    );
}
