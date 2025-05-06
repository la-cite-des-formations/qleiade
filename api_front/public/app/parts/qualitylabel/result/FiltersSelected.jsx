import React, { useContext } from "react";
import ChipsPresenter from "@components/ChipsPresenter";
import { Button, Grid, Stack } from "@mui/material";
import { useTranslation } from "react-i18next";
import map from "lodash/map";
import { ResultContext } from "@parts/qualitylabel/result/Context";

export default function FiltersSelected(props) {
    const { t } = useTranslation('common')
    const { resultValues, handleChange } = useContext(ResultContext);
    const { filters_selected, wrapper } = resultValues
    const { handleCommit, mapper } = props;//in context

    function handleDelete(value) {
        handleChange({ type: "DELETE_ONE", name: "filters_selected", who: value.type, value: value });
        if (value.type === "indicator") {
            if (filters_selected[value.type].length <= 1) {
                handleChange({ type: "SET", name: "wrapper", who: "current", value: wrapper.init });
            }
        } else {
            handleChange({ type: "DELETE_ALL", name: "wealths", who: value.type });
        }
    }

    function makePresenters(mapper, data) {
        const p = map(mapper, (key) => {
            const position = (data[key] || []).length > 0 ? (data[key] || []).length > 1 ? "right" : "left" : null;
            return (
                <ChipsPresenter key={key} items={(data[key] || [])} handleDelete={handleDelete} title={key + "s"} position={position} />
            )
        })
        return p;
    }

    return (
        <Grid container spacing={2} direction="row">
            <Grid item xs={2}>
                <Button
                    variant="contained" onClick={() => handleCommit(filters_selected)} disabled={mapper.filter((item) => { return (filters_selected[item] || []).length > 0 }).length === 0}>
                    {t('filter')}
                </Button>
            </Grid>
            <Grid item xs={10}>
                <Stack direction={'row'} spacing={2}>
                    {makePresenters(mapper, filters_selected)}
                </Stack>
            </Grid>
        </Grid>
    );
}
