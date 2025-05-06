import React, { useEffect, useState, useContext } from "react";
import { Box, Grid } from "@mui/material";
import Summary from "@parts/qualityLabel/result/Summary";
import Presenter from "@parts/qualitylabel/result/Presenter";
import MyContainer from "@components/Container";
import Filters from "@parts/qualitylabel/result/Filters";
import FiltersSelected from "@parts/qualitylabel/result/FiltersSelected";
import API from "@services/axios.js";
import { ResultContext } from "./Context";

export default function Layout(props) {
    const { resultValues, handleChange } = useContext(ResultContext);
    const { wrapper } = resultValues;
    const { state } = props;

    const defaultFilter = {
        quality_label: props.label,
        granularity_type: "global",
        granularity_id: null,
        archived: 'false'
    };

    const [filter, setFilter] = useState(defaultFilter);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const sum = await API.post("/audit", {
                    params: state
                });

                handleChange({ type: "SET", who: "summary", name: "audit", value: sum.data });

                const result = await API.post("/audit/result", {
                    params: filter
                });

                handleChange({ type: "SET", who: filter.granularity_type, name: "wealths", value: result.data.wealths });

                if (wrapper.current.length > 0) {
                    handleChange({ type: "SET", who: "current", name: "wrapper", value: wrapper.current });
                } else {
                    handleChange({ type: "SET", who: "current", name: "wrapper", value: result.data.wrapper });
                }
                handleChange({ type: "SET", who: "init", name: "wrapper", value: result.data.wrapper });
                handleChange({ type: "SET", who: "indicator", name: "filters_options", value: result.data.wrapper });

            } catch (error) {
                throw new Error('Something went wrong with audit!');
            }
        };

        fetchData();
    }, [props.label, filter]);

    function filtersCommit(filters) {
        const filterHandlers = {
            indicator: () => {
                if (filters.indicator.length > 0) {
                    handleChange({
                        type: "SET",
                        name: "wrapper",
                        who: "current",
                        value: filters.indicator,
                    });
                } else {
                    handleChange({
                        type: "SET",
                        name: "wrapper",
                        who: "current",
                        value: wrapper.init,
                    });
                }
            },
            // Add more filter handlers here
        };

        for (const key in filters) {
            if (filterHandlers[key]) {
                filterHandlers[key]();
            } else if (filters[key].length === 1) {
                const filter = {
                    quality_label: props.label,
                    granularity_type: key,
                    granularity_id: filters[key][0].id,
                    archived: 'false'
                };
                setFilter(filter);
            }
        }
    }

    return (
        <Box component="main" sx={{ padding: 5, overflow: "hidden" }}>
            <Grid container spacing={2}
                direction="row"
            >
                <Grid item xs={4}>
                    <MyContainer item={{ children: <Summary label={props.label} /> }} />
                </Grid>

                <Grid item xs={8}>
                    <MyContainer item={{ children: <Filters mapper={["indicator"]} /> }} />
                    <MyContainer item={{ children: <FiltersSelected mapper={["formation", "group", "indicator", "student"]} handleCommit={filtersCommit} /> }} />
                    <MyContainer item={{ children: <Presenter /> }} />
                </Grid>
            </Grid>
        </Box>
    );


}
