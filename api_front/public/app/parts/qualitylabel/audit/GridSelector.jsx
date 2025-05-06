import React, { useEffect, useState } from "react";
import { FormControlLabel, Stack, Zoom, Typography } from '@mui/material';
import { AndroidSwitch } from "@components/AndroidSwitch";
import DesignedDataGrid from "@components/DesignedDataGrid";
import { useTranslation } from "react-i18next";


export default function GridSelector(props) {

    const { t } = useTranslation('common');
    const [checked, setChecked] = useState(false);
    const { header, rows, handler, isActive, selected, content } = props;


    useEffect(() => {
        if (selected.length > 0) {
            setChecked(true);
        }
    }, [])

    const handleCheck = (event) => {
        setChecked((prev) => !prev);
    };

    const handleCommit = (event) => {
        handler(event);
    };

    return (
        <>
            {
                isActive ? (
                    <Stack>
                        <Stack direction={"row"} spacing={2} >
                            <Typography variant="h6" align="center">
                                {content.question}
                            </Typography>
                            <Stack direction="row" spacing={1} alignItems="center">
                                <Zoom in={!checked} >
                                    <Typography>{t("no")}</Typography>
                                </Zoom>
                                <FormControlLabel
                                    value="groups"
                                    control={<AndroidSwitch onChange={handleCheck} checked={checked} />}
                                />
                                <Zoom in={checked} >
                                    <Typography>{t("yes")}</Typography>
                                </Zoom>
                            </Stack>

                        </Stack>
                        {checked ? <DesignedDataGrid {...props} name={content.name} rows={rows} header={header} setData={handleCommit} selected={selected} /> : <Typography>{content.info} </Typography>}
                    </Stack>) : (
                    <Typography variant="h6" align="center">
                        {content.replace}
                    </Typography>
                )
            }
        </>
    )
};
