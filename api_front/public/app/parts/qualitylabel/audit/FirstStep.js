import React, { useCallback, useContext, useEffect, useState } from "react";
import { Button, FormControlLabel, Checkbox, FormHelperText, Typography, Stack, Box, TextField, Autocomplete } from "@mui/material";
import GridSelector from "./GridSelector";
import { StepContext } from "./Context";
import API from "@services/axios";
import _ from "lodash";
import { useTranslation } from "react-i18next";
import { useSnackbar } from "notistack";

export default function FirstStep() {
    const { t } = useTranslation('common');
    const { enqueueSnackbar } = useSnackbar();
    const [auditList, setAuditList] = useState([]);
    const { formValues, handleChange, handleNext, variant, margin } = useContext(
        StepContext
    );
    const { since, extern, previousAudit, until, type, qualityLabel } = formValues;
    const [periods, setPeriods] = useState([]);
    const [empty, setEmpty] = useState(false);

    // Check if all values are not empty and if there are some errors
    const isError = useCallback(
        () =>
            Object.keys({ since, extern, previousAudit, until }).some(
                (name) =>
                    (formValues[name].required && !formValues[name].value) ||
                    formValues[name].error
            ),
        [formValues, extern, previousAudit, since, until, type]
    );

    useEffect(() => {
        const fetchAudits = async (label) => {
            const data = { "quality_label": label };
            try {
                API.get('/audits', { params: data }).then((result) => {
                    var data = result.data.data
                    if (data?.length > 0) {
                        setAuditList(data);
                    } else {
                        setEmpty(true);
                    }
                    return true
                })
            } catch (error) {
                //toast error
                enqueueSnackbar(t('no audits found'), { variant: 'error' });
                setEmpty(true);
            }
        }
        if (qualityLabel.value != "") {
            fetchAudits(qualityLabel.value);
        }
    }, [qualityLabel])

    const handleTypeChange = (event, value) => {
        event.stopPropagation();
        var e = {
            target: {
                type: "select",
                value: value,
                name: 'type'
            }
        }
        handleChange(e);
    }

    const handlePeriodsLoad = (e) => {

        handleChange(e);

        let puntil = until.value;
        let psince = since.value;

        if (e.target.name === 'since') {
            psince = e.target.value;
        } else {
            puntil = e.target.value;
        }

        API.get('/periods', { params: { 'since': psince, 'until': puntil } })
            .then((response) => {
                let data = response.data.data;
                let p = _.map(data, function (element) {
                    return <TextField
                        disabled
                        id="outlined-disabled"
                        label={t("period")}
                        fullWidth
                        defaultValue={element.name}
                        key={element.id} />
                });
                setPeriods(p);
            }).catch((error) => {
                enqueueSnackbar(t('no periods found'), { variant: 'error' });
            });

    };

    const auditListHeader = [
        { field: 'id', headerName: t('quality_label.audit.audit_list.header.id'), width: 70 },
        { field: 'audit_type', headerName: t('quality_label.audit.audit_list.header.type'), width: 80 },
        { field: 'name', headerName: t('quality_label.audit.audit_list.header.name'), width: 410 },
        { field: 'date', headerName: t('quality_label.audit.audit_list.header.date'), width: 200 }
    ];

    const content = {
        name: "audits",
        question: t("quality_label.audit.audit_list.question"),
        info: t('quality_label.audit.audit_list.info'),
        replace: t('quality_label.audit.audit_list.replace')
    };

    const setPreviousData = (name, value) => {
        var e = {
            target: {
                type: "select",
                value: value,
                name: name
            }
        }
        handleChange(e);
    }

    const handleAuditSelect = (event) => {
        const audit = event.target.value[0];
        setPreviousData("formations", audit.sample.formations);
        setPreviousData("groups", audit.sample.groups);
        setPreviousData("students", audit.sample.students);
    }

    return (
        <Stack spacing={2}>
            <Typography variant="h5" align="center" sx={{ mt: 2 }}>
                {t("quality_label.audit.first_step.subtitle")}
            </Typography>
            <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                {t("quality_label.audit.first_step.description")}
            </Typography>
            <Stack>
                <FormControlLabel
                    control={
                        <Checkbox
                            checked={extern.value}
                            onChange={handleChange}
                            name="extern"
                            color="info"
                            required={extern.required}
                        />
                    }
                    label={t("quality_label.audit.auditor")}
                />
                <FormHelperText error={!!extern.error}>
                    {extern.error}
                </FormHelperText>
            </Stack>
            <Stack>
                <Autocomplete
                    name="type"
                    value={type.value}
                    sx={{ width: '100%', marginTop: '16px', marginBottom: '8px' }}
                    fullWidth={true}
                    getOptionLabel={(option) => option.label}
                    isOptionEqualToValue={(option, value) => option.label === value.label}
                    onChange={handleTypeChange}
                    autoComplete={false}
                    // disablePortal
                    id="combo-box-audit-extern"
                    options={[{ id: 'prep', label: t("quality_label.audit.first_step.preparation") }, { id: 'int', label: t("quality_label.audit.first_step.interne") }, { id: "ext", label: t("quality_label.audit.first_step.externe") }]}
                    renderInput={
                        (params) => <TextField {...params}
                            label={t("quality_label.audit.first_step.audit_type")} />
                    }
                />
            </Stack>
            <Stack direction={'row'} spacing={2}>
                <TextField
                    // variant={variant}
                    // margin={margin}
                    fullWidth
                    InputLabelProps={{
                        shrink: true
                    }}
                    label={t("quality_label.audit.since")}
                    name="since"
                    type="date"
                    defaultValue={since.value}
                    onChange={handlePeriodsLoad}
                    required={since.required}
                    helperText={since.error}
                />
                <TextField
                    // variant={variant}
                    // margin={margin}
                    fullWidth
                    InputLabelProps={{
                        shrink: true
                    }}
                    label={t("quality_label.audit.until")}
                    name="until"
                    type="date"
                    defaultValue={until.value}
                    onChange={handlePeriodsLoad}
                    required={until.required}
                    error={!!until.error}
                    helperText={until.error}
                />
            </Stack>
            <Stack direction={'row'} spacing={2}>
                {periods}
            </Stack>
            <Stack>
                <GridSelector
                    empty={empty}
                    checkboxSelection={false}
                    header={auditListHeader}
                    rows={auditList ?? []}
                    handler={handleAuditSelect}
                    isActive={true}
                    selected={[]}
                    content={content}
                />
            </Stack>
            <Box sx={{ display: "flex", justifyContent: "flex-end" }}>
                <Button
                    variant="contained"
                    sx={{ mt: 3, ml: 1 }}
                    disabled={isError()}
                    color="primary"
                    onClick={!isError() ? handleNext : () => null}
                >
                    {t("quality_label.audit.next")}
                </Button>
            </Box>
        </Stack>
    );
}
