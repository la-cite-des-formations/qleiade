import React, { useEffect, useState, useContext } from "react";
import { Box, Grid } from "@mui/material";
import Summary from "@parts/qualityLabel/result/Summary";
import Presenter from "@parts/qualitylabel/result/Presenter";
import MyContainer from "@components/Container";
import API from "@services/axios.js";
import { ResultContext } from "./Context";

export default function Layout(props) {
    const { resultValues, handleChange } = useContext(ResultContext);
    const { wrapper } = resultValues;
    const { state } = props;

    useEffect(() => {
        const fetchData = async () => {
            try {
                const sum = await API.post("/audit", {
                    params: state
                });

                handleChange({ type: "SET", who: "summary", name: "audit", value: sum.data });

                const result = await API.post("/audit/result", {
                    params: {
                        quality_label: props.label,
                        granularity_type: "global",
                        granularity_id: null,
                        archived: 'false'
                    }
                });

                handleChange({ type: "SET", who: "current", name: "wrapper", value: result.data.wrapper });
                handleChange({ type: "SET", who: "init", name: "wrapper", value: result.data.wrapper });
                handleChange({ type: "SET", who: "global", name: "wealths", value: result.data.wealths });
                handleChange({ type: "SET", who: "indicator", name: "filters_options", value: result.data.wrapper });

            } catch (error) {
                throw new Error('Something went wrong with audit!');
            }
        };

        fetchData();
    }, [props.label]);

    return (
        <Box component="main" sx={{ padding: 5, overflow: "hidden" }}>
            <Grid container spacing={2}
                direction="row"
            >
                <Grid item xs={4}>
                    <MyContainer item={{ children: <Summary label={props.label} /> }} />
                </Grid>

                <Grid item xs={8}>
                    <MyContainer item={{ children: <Presenter /> }} />
                </Grid>
            </Grid>
        </Box>
    );
}
