import React, { useCallback, useContext, useEffect, useState } from "react";
import { Typography, Stack, Box, Button } from "@mui/material";
import { StepContext } from "./Context";
import API from "@services/axios";
import GridSelector from "./GridSelector";
import { useTranslation } from "react-i18next";
import { useSnackbar } from "notistack";

export default function FourthStep() {
    const { t } = useTranslation('common');
    const { enqueueSnackbar } = useSnackbar();
    const [studentsIsEmpty, setStudentsIsEmpty] = useState(false);
    const {
        formValues,
        handleChange,
        handleBack,
        handleNext,
    } = useContext(StepContext);

    const { students, previousAudit, formations, groups, since, until } = formValues;
    const [studentList, setStudentList] = useState([]);

    useEffect(() => {
        if (formations.value.length > 0) {
            getStudents().then(response => {
                var res = response.data.data;
                if (res.length > 0) {
                    setStudentList(res);
                } else {
                    enqueueSnackbar(t('no audits found'), { variant: 'error' });
                    setStudentsIsEmpty(true);
                }
            });
        }
    }, []);

    function getStudents() {
        return API.get('/students', { params: { 'formations': formations.value, 'groups': groups.value, 'since': since.value, 'until': until.value } })
    }

    const isError = useCallback(
        () =>
            Object.keys({ students, formations, groups }).some(
                (name) =>
                    (formValues[name].required && !formValues[name].value) ||
                    formValues[name].error
            ),
        [formValues, students]
    );

    function handleSelect(event) {
        handleChange(event);
    }

    const header = [
        { field: 'id', headerName: t("student_list.header.id"), width: 100 },
        { field: 'firstName', headerName: t("student_list.header.firstname"), width: 200 },
        { field: 'lastName', headerName: t("student_list.header.lastname"), width: 200 },
        {
            field: 'birthDate',
            headerName: t("student_list.header.birthdate"),
            type: 'number',
            width: 150,
        }
    ];

    const content = {
        name: "students",
        question: t("student_list.content.question"),
        info: t("student_list.content.info"),
        replace: t("student_list.content.replace")
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
                    {t("quality_label.audit.fourth_step.subtitle")}
                </Typography>
                <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                    {t("quality_label.audit.fourth_step.description")}
                </Typography>
                <GridSelector
                    header={header}
                    handler={handleSelect}
                    isActive={true}
                    rows={studentList}
                    selected={students.value}
                    content={content}
                    checkboxSelection={true}
                />
            </Stack>

            <Box sx={{ display: "flex", justifyContent: "flex-end", mt: 3 }}>
                <Button onClick={handleBack} sx={{ mr: 1 }}>
                    {t("back")}
                </Button>
                <Button
                    variant="contained"
                    disabled={isError()}
                    color="primary"
                    onClick={!isError() ? handleNext : () => null}
                >
                    {t("next")}
                </Button>
            </Box>
        </>
    );
}
