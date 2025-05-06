import { Grid, Stack, TextField, Autocomplete, Typography, Skeleton } from "@mui/material";
import React, { useContext, useEffect } from "react";
import { useTranslation } from "react-i18next";
import _ from "lodash";
import { ResultContext } from "./Context";

function compare(option, value) {
    return Object.keys(option).length > 0 && option.label === value.label;
}

export default function Filters(props) {
    const { mapper } = props;
    const { resultValues, handleChange } = useContext(ResultContext);
    const { filters_options } = resultValues
    const { t } = useTranslation('common');

    useEffect(() => { }, [filters_options]);

    function getLabel(option) {
        return Object.keys(option).length > 0 ? t("indicator_label", { id: (option.number), label: (option.label) }) : t("indicator");
    }

    const handleSelectIndicator = (event, value) => {
        event.stopPropagation();
        const toto = value;
        toto.type = 'indicator';
        handleChange({ type: "ADD", who: "indicator", name: "filters_selected", value: toto })
    };

    function makeFilters(mapper) {
        const filters = _.map(mapper, (filter) => {
            return (
                filters_options[filter].length < 1 ?
                    <Skeleton key="filter1" width={800} height={90} /> :
                    <Grid key={filter} item xs={9}>
                        <Autocomplete
                            sx={{ width: '100%' }}
                            fullWidth={true}
                            getOptionLabel={getLabel}
                            isOptionEqualToValue={compare}
                            onChange={(event, value) => handleSelectIndicator(event, value)}
                            autoComplete={true}
                            disablePortal
                            id="combo-box-indicators-filter"
                            options={(filters_options[filter] || [])}
                            renderInput={(params) => <TextField {...params} label={t(filter)} />}
                        />
                    </Grid>

            )

        });

        return filters;
    }

    return (
        <Grid container spacing={2}
            direction="row"
        >
            <Grid item xs={2}>
                <Stack spacing={2}>
                    <Typography >
                        {t("quality_label.result.filters.title")}
                    </Typography>
                </Stack>
            </Grid>
            {makeFilters(mapper)}
            <Grid item xs={1}>
            </Grid>
        </Grid>
    )
}
