import React, { useEffect, useState, useContext, useCallback } from 'react';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    TextField,
    Box,
    Grid,
    Autocomplete,
    Tooltip,
    Typography,
    Divider,
    Stack
} from '@mui/material';
import { useTranslation } from 'react-i18next';
import SyntheticView from '@components/SyntheticView';
import _ from 'lodash';
import API from "@services/axios";
import { useSnackbar } from 'notistack';
import { useNavigate } from 'react-router-dom';
import { ResultContext } from "@parts/qualitylabel/result/Context";
import CheckIcon from '@mui/icons-material/Check';
import OffIcon from '@mui/icons-material/Close';

const initialFormData = {
    // id: "id donnée par le back lors de l'enregistrement de l'audit",
    quality_label: "",
    name: "",
    date: "",
    auditor: "",
    audit_type: null,
    comment: "",
    sample: {
        type: "",
        formations: [],
        groups: [],
        students: [],
        periods: []
    },
    validation: "",
    wealths: []
};

function ValidateAudit(props) {
    const { handleChange } = useContext(ResultContext);
    const { t } = useTranslation('common')
    const [formData, setFormData] = useState(initialFormData);
    const [errors, setErrors] = useState({});
    const [synthticViewMapper, setSynthticViewMapper] = useState([]);
    const { label, data, onClose } = props;
    const { enqueueSnackbar } = useSnackbar();
    const nav = useNavigate();
    const [buttonColor, setButtonColor] = useState('error'); // 'error' represents red, 'success' represents green
    const [buttonLabel, setButtonLabel] = useState('Non validé'); // Initial label
    const [buttonValue, setButtonValue] = useState(false); // Initial label
    const [buttonIcon, setButtonIcon] = useState(<OffIcon />); // Initial icon


    // Check if there are some errors
    const isError = useCallback(
        () => Object.keys(formData).some(
            (name) => {
                return name === "wealths" ? formData.wealths.length === 0 : errors.hasOwnProperty(name);
                // if (name != "wealths") {
                //     return errors.hasOwnProperty(name) //if exist not if empty
                // } else {
                //     return formData.wealths.length === 0
                // }
            }
        ),
        [formData, errors]
    );

    function setInitalFormData(key, truc) {
        setFormData((prevState) => ({ ...prevState, [key]: truc }));
    }

    useEffect(() => {
        setSynthticViewMapper([
            {
                type: "defaultItem",
                primary: "quality_label.result.summary.extern",
                secondary: data.summary?.extern ? "Auditeur externe" : "Auditeur interne",
                loaded: data.summary,
                withDivider: true
            },
            {
                type: "clickableList",
                primary: "wealths",
                loaded: data.validatedWealths,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.wealths",
                    data: data.validatedWealths,
                    itemId: "id",
                    primary: { key: "title" },
                    secondary: { text: "quality_label.result.validating.wealths.details", params: { indicator: "indicators", granularity: "granularity" } },
                    type: "wealth",
                    clickable: false
                }
            },
            {
                type: "clickableList",
                primary: "periods",
                loaded: data.summary,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.periods",
                    data: data.summary?.periods,
                    itemId: "id",
                    primary: { key: "title" },
                    type: "period",
                    clickable: false
                }
            },
            {
                type: "clickableList",
                primary: "formations",
                loaded: data.summary,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.formations",
                    data: data.summary?.formations,
                    itemId: "id",
                    primary: { key: "title" },
                    type: "formation",
                    clickable: false
                }
            },
            {
                type: "clickableList",
                primary: "groups",
                loaded: data.summary,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.groups",
                    data: data.summary?.groups,
                    itemId: "id",
                    primary: { key: "title" },
                    secondary: { key: "formation" },
                    type: "group",
                    clickable: false
                }
            },
            {
                type: "clickableList",
                primary: "students",
                loaded: data.summary,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.students",
                    data: data.summary?.students,
                    itemId: "id",
                    primary: { key: "fullName" },
                    secondary: { key: "ine" },
                    type: "student",
                    clickable: false
                }
            }
        ]);
        setInitalFormData('quality_label', label);
        setInitalFormData('wealths', data.validatedWealths);
        var sample = {
            audit_type: data.summary?.audit_type,
            formations: data.summary?.formations,
            groups: data.summary?.groups,
            students: data.summary?.students,
            periods: data.summary?.periods
        };
        setInitalFormData('sample', sample);
        setInitalFormData('comment', data.comment);
        setInitalFormData("validation", buttonValue);
        setInitalFormData("audit_type", data.summary?.audit_type);

    }, [props.open])

    const postForm = async (data, updating = false) => {
        API.post("/audit/validate", data)
            .then((result) => {
                setInitalFormData("audit_id", result.data.audit_id);
                //mettre l'id dans le context général de result pour le rafraichissement de la page
                handleChange({ type: "SET", name: "audit", who: 'id', value: result.data.audit_id });
                const message = updating ? t("quality_label.result.messages.success.save_audit") : t("quality_label.result.messages.success.save_close_audit");
                enqueueSnackbar(message, { variant: 'success' });

                if (!updating) {
                    let route = "/labels/" + label;
                    nav(route);
                } else {

                    onClose();
                }

                return true;
            })
            .catch(
                (error) => {
                    if (error.response.status === 422) {
                        setErrors(error.response.data.errors);
                        for (const prop in error.response.data.errors) {
                            const er = error.response.data.errors[prop];
                            er.forEach(msg => {
                                //DOC FRONT SECURITY: toast errors
                                enqueueSnackbar(t(msg), { variant: 'error' });
                            });

                        }
                    } else {
                        enqueueSnackbar(error.response.data.message, { variant: 'error' });
                    }
                }
            );
        return false;
    }

    const handleSave = () => {
        // enregistrer et continuer l'audit -> result
        postForm(formData, true);

    };

    const handleValidate = () => {
        //enregistrer et terminer l'audit -> retour au label qualité
        postForm(formData, false);
    };

    const handleInputChange = (event, nvalue) => {
        const { name, value } = event.target;
        const hname = nvalue ? "type" : name;
        const hvalue = nvalue ? nvalue : value;

        setFormData((prevState) => ({ ...prevState, [hname]: hvalue }));
        if (hvalue !== "") {
            setErrors((prevState) => {
                const newErrors = { ...prevState };
                delete newErrors[hname];
                return newErrors;
            });
        } else {
            setErrors((prevState) => ({ ...prevState, [hname]: t("quality_label.result.messages.error.required_filed") }));
        }
    };

    const handleButtonClick = (e) => {
        setButtonColor((prevColor) => (prevColor === 'error' ? 'success' : 'error'));
        setButtonLabel((prevLabel) => (prevLabel === 'Non validé' ? 'Validé !' : 'Non validé'));
        setButtonValue((prevValue) => (prevValue === false ? true : false));
        setButtonIcon((prevIcon) => (prevIcon.type === OffIcon ? <CheckIcon /> : <OffIcon />));
        handleInputChange(e)
    };

    return (
        <div>
            <Dialog
                open={props.open}
                onClose={(e, reason) => { reason != "backdropClick" ? props.onClose(e) : null }}
                // BackdropProps={{ invisible: false }}
                disableEscapeKeyDown={true}
                fullScreen={true}
            >
                <DialogTitle>{t("quality_label.result.validating.title", { label: label })}</DialogTitle>
                <DialogContent>
                    <Grid container spacing={2}
                        direction="row"
                    >
                        <Grid item xs={4}>
                            <Box >
                                <SyntheticView title={"Détails de l'audit"} mapper={synthticViewMapper} />
                            </Box>
                        </Grid>
                        <Grid item xs={8}>
                            <Box>
                                <TextField
                                    value={formData.date}
                                    margin={"normal"}
                                    fullWidth
                                    InputLabelProps={{
                                        shrink: true
                                    }}
                                    label={t("quality_label.result.validating.date")}
                                    name="date"
                                    type="date"
                                    onChange={(e) => handleInputChange(e)}
                                    required={true}
                                    error={errors?.hasOwnProperty("date")}
                                    helperText={t(errors?.date)}
                                />

                                <TextField
                                    autoFocus
                                    margin={"normal"}
                                    label={t("quality_label.result.validating.name")}
                                    name="name"
                                    fullWidth
                                    value={formData.name}
                                    onChange={(e) => handleInputChange(e)}
                                    required={true}
                                    error={errors?.hasOwnProperty("name")}
                                    helperText={t(errors?.name)}
                                />
                                <TextField
                                    value={formData.auditor}
                                    margin={"normal"}
                                    label={t("quality_label.result.validating.auditor")}
                                    name="auditor"
                                    fullWidth
                                    onChange={(e) => handleInputChange(e)}
                                    error={errors?.hasOwnProperty("auditor")}
                                    helperText={t(errors?.auditor)}
                                />
                                <Autocomplete
                                    name="audit_type"
                                    // defaultValue={data.summary?.audit_type}
                                    value={formData.audit_type}
                                    sx={{ width: '100%', marginTop: '16px', marginBottom: '8px' }}
                                    fullWidth={true}
                                    getOptionLabel={(option) => { return option.label }}
                                    isOptionEqualToValue={(option, value) => { return option.label === value.label || option.id === value.id }}
                                    onChange={(event, value) => handleInputChange(event, value)}
                                    autoComplete={false}
                                    disablePortal
                                    id="combo-box-audit-type"
                                    options={[{ id: 'prep', label: t("quality_label.audit.first_step.preparation") }, { id: 'int', label: t("quality_label.audit.first_step.interne") }, { id: "ext", label: t("quality_label.audit.first_step.externe") }]}
                                    renderInput={
                                        (params) => <TextField {...params}
                                            label={t("quality_label.result.validating.audit_type")}
                                            error={errors?.hasOwnProperty("audit_type")}
                                            helperText={t(errors?.audit_type)} />
                                    }
                                />
                                <Divider />
                                <Stack spacing={2}>
                                    <Typography variant="h6" sx={{ mt: 2 }}>
                                        {t("quality_label.result.validating.validate")}
                                    </Typography>
                                    <Button
                                        name="validation"
                                        value={!buttonValue}
                                        variant="outlined"
                                        color={buttonColor}
                                        onClick={handleButtonClick}
                                        // required={true}
                                        startIcon={buttonIcon}
                                    >
                                        {buttonLabel}
                                    </Button>
                                </Stack>
                            </Box>
                        </Grid>
                    </Grid>
                </DialogContent>
                <Tooltip title={t("quality_label.result.validating.tooltips.validate")} placement="top-end" >
                    <DialogActions>
                        <Button variant="outlined" onClick={props.onClose}>{t("Annuler")}</Button>
                        <Button variant="outlined" color="success" onClick={handleSave} disabled={isError()}>{t("Enregistrer")}</Button>
                        <Button variant="contained" color="success" onClick={handleValidate} disabled={isError()}>{t("quality_label.result.validating.closeButton")}</Button>
                    </DialogActions>
                </Tooltip>
            </Dialog>
        </div>
    );
}

export default ValidateAudit;
