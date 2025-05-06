import React, { useCallback, useContext, useEffect, useState } from "react";
import { Typography, Stack, Divider, Box, Button } from "@mui/material";
import GridSelector from "./GridSelector"
import { StepContext } from "./Context";
import API from "@services/axios";
import { useTranslation } from "react-i18next";
import { useSnackbar } from "notistack";

export default function ThirdStep() {
    const { t } = useTranslation('common');
    const { enqueueSnackbar } = useSnackbar();
    const [formationSelection, setFormationSelection] = useState([]);
    const [groupList, setGroupList] = useState([]);
    const [groupIsEmpty, setGroupIsEmpty] = useState(false);

    const {
        formValues,
        handleChange,
        handleBack,
        handleNext,
    } = useContext(StepContext);

    const { previousAudit, formations, groups, since, until } = formValues;

    const isError = useCallback(
        () =>
            Object.keys({ groups }).some(
                (name) =>
                    (formValues[name].required && !formValues[name].value) ||
                    formValues[name].error
            ),
        [formValues, groups]
    );

    useEffect(() => {
        if (formations.value.length > 0) {
            setFormationSelection(formations.value);
            // setGroupIsActive(true);

            getGroups(formations.value).then(response => {
                var res = response.data.data;

                if (res.length > 0) {
                    setGroupList(res);
                } else {
                    enqueueSnackbar(t('no groups found'), { variant: 'error' });
                    setGroupIsEmpty(true);
                }
            });
        }
    }, [formations.value]);

    function getGroups(formations) {
        // si j'ai pas de group ypareo ne répond pas mouline !!!!!!!
        return API.get('/groups', { params: { 'formations': formations, 'since': since.value, 'until': until.value } });
    }

    function handleSelect(event) {
        handleChange(event);
    }

    const groupListHeader = [
        { field: 'id', headerName: t('group_list.header.id'), width: 160 },
        { field: 'title', headerName: t('group_list.header.name'), width: 300 },
        { field: 'formation', headerName: t('group_list.header.formation'), width: 300 }
    ];

    const content = {
        name: "groups",
        question: t("group_list.question"),
        info: t('group_list.info'),
        replace: t('group_list.replace')
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
                    {t("quality_label.audit.third_step.subtitle")}
                </Typography>
                <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                    {t("quality_label.audit.third_step.description")}
                </Typography>
                <Divider />
                <GridSelector
                    empty={groupIsEmpty}
                    header={groupListHeader}
                    rows={groupList}
                    handler={handleSelect}
                    isActive={true}
                    selected={groups.value}
                    content={content}
                    checkboxSelection={true}
                />
            </Stack>
            <Box sx={{ display: "flex", justifyContent: "flex-end", mt: 3 }}>
                <Button onClick={handleBack} sx={{ mr: 1 }}>
                    {t("quality_label.audit.back")}
                </Button>
                <Button
                    variant="contained"
                    disabled={isError()}
                    color="primary"
                    onClick={!isError() ? handleNext : () => null}
                >
                    {t("quality_label.audit.next")}
                </Button>
            </Box>
        </>
    );
}
