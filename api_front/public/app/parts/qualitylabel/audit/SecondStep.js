import React, { useCallback, useContext, useEffect, useState, useRef } from "react";
import { Typography, Stack, Autocomplete, TextField, Box, Button } from "@mui/material";
import { StepContext } from "./Context";
import API from "@services/axios";
import { useTranslation } from "react-i18next";
import { useSnackbar } from "notistack";

export default function SecondStep() {
    const { t } = useTranslation('common');
    const { enqueueSnackbar } = useSnackbar();
    const [formationsSelectOptions, setFormationsSelectOptions] = useState([]);
    const [formationSelection, setFormationSelection] = useState([]);
    const formationSelectionRef = useRef(formationSelection);
    const [isMounted, setIsMounted] = useState(true);

    const {
        formValues,
        handleChange,
        handleBack,
        handleNext,
    } = useContext(StepContext);

    const { previousAudit, formations, since, until } = formValues;

    const isError = useCallback(
        () =>
            Object.keys({ formations }).some(
                (name) =>
                    (formValues[name].required && !formValues[name].value) ||
                    formValues[name].error
            ),
        [formValues, formations]
    );

    useEffect(() => {
        setIsMounted(true);
        if (formations.value.length > 0) {
            setFormationSelection(formations.value);
            formationSelectionRef.current = formations.value;
        }

        if (formationsSelectOptions.length <= 0) {
            API.get(`/formations`, {
                // params: { q: label }
            }).then(response => {
                setFormationsSelectOptions(response.data.data);
            }).catch((error) => {
                enqueueSnackbar(t('no formations found'), { variant: 'error' });
            })
        }
        return () => {
            if (isMounted) {
                formationCommit(formationSelectionRef.current)
                setIsMounted(false);
            }
        }
    }, [isMounted]);

    const handleFormationSelect = (event, value, reason) => {
        setFormationSelection(value);
        formationSelectionRef.current = value;
    };

    const formationCommit = (formations) => {
        var e = {
            target: {
                type: "select",
                value: formations,
                name: 'formations'
            }
        }
        handleChange(e);
    };

    return (
        <>
            {previousAudit.value ?
                <Typography align="center" >
                    {t('quality_label.audit.previous_audit.info')}
                </Typography> :
                <div />
            }
            <Stack spacing={2}>
                <Typography variant="h5" align="center" sx={{ mt: 2 }}>
                    {t("quality_label.audit.second_step.subtitle")}
                </Typography>
                <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                    {t("quality_label.audit.second_step.description")}
                </Typography>
                <Stack>
                    <Autocomplete
                        // sx={{
                        //     width: "100%"
                        // }}
                        fullWidth={false}
                        value={formationSelection ?? []}
                        getOptionLabel={(option) => option.label}
                        isOptionEqualToValue={(option, value) => option.label === value.label}
                        multiple={true}
                        onChange={handleFormationSelect}
                        autoComplete={true}
                        disablePortal
                        id="combo-box-filter"
                        options={formationsSelectOptions}
                        renderInput={(params) => <TextField {...params} label={"Formation"} />}
                    />
                </Stack>
            </Stack>
            <Box sx={{ display: "flex", justifyContent: "flex-end", mt: 3 }}>
                <Button onClick={handleBack} sx={{ mr: 1 }}>
                    {t("quality_label.audit.back")}
                </Button>
                <Button
                    variant="contained"
                    disabled={formationSelection.length <= 0}
                    color="primary"
                    onClick={formationSelection.length > 0 ? handleNext : () => null}
                >
                    {t("quality_label.audit.next")}
                </Button>
            </Box>
        </>
    );
}
