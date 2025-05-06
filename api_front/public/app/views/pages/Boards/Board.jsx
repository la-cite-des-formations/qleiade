import React, { useEffect, useState, useContext } from "react";
import { useParams } from "react-router-dom";

import API from '@services/axios';
import { AuthContext } from '@services/middleWear/auth/context';
import { useTranslation } from "react-i18next";
import { Box, Grid } from "@mui/material";
import Graph from "@parts/board/Graph";
import MyContainer from "@components/Container";
import Filters from "@parts/board/Filters";

//TODO-FRONT mettre un avertissement quand il n'y a pas de edges doit varier selon le type
export default function Board(props) {
    const { t } = useTranslation('common');
    const { slug } = useParams();
    const { type } = useParams();
    const [items, setItems] = useState([]);
    const [choices, setChoices] = useState([]);
    const { canSee } = useContext(AuthContext);

    const defaultFilter = {
        unit: slug,
        archived: 'false',
        granularity_type: "global",
        granularity_id: null,
    };

    const [filter, setFilter] = useState(defaultFilter);

    function setItemsToRender(data) {
        var itemsToRender = {};

        return itemsToRender;
    }

    useEffect(() => {
        // const menu = [
        //     {
        //         id: "board",
        //         title: t("board.welcome.title", { type: type }),
        //         description: t("board.welcome.description"),
        //         type: "Hello",
        //         layout: {
        //             i: "board",
        //             x: 0,
        //             y: 0,
        //             h: 12,
        //             w: 3,
        //             static: true,
        //             minh: 8,
        //             minw: 3
        //         }
        //     }
        // ];
        // setItems(menu);
        const fetchData = async () => {
            API.get('/graph/board/unit', { params: filter })
                .then((result) => {
                    setItems(result.data.nodesWithEdges);
                    setChoices(result.data.filters);
                }).catch((err) => {
                    console.error("connexion error");
                });
        }
        fetchData();

    }, [slug, filter])

    function setAction(actionId) {
        if (actionId === 0) {
            setFilter(defaultFilter);
        } else {
            setFilter({
                unit: slug,
                archived: 'false',
                granularity_type: "global",
                action: actionId,
            });
        }
    }

    return (
        <Box component="main" sx={{ padding: 5, overflow: "hidden" }}>
            <Grid container spacing={2}
                direction="row"
            >
                <Grid item xs={4}>
                    <MyContainer item={{ children: <Filters choices={choices} commit={setAction} /> }} />
                </Grid>
                <Grid item xs={8}>
                    <MyContainer item={{
                        children:
                            <Box sx={{
                                width: '100%',
                                height: '80vh'
                            }}>
                                {/* <Graph wealths={wealths} /> */}
                                <Graph items={items} />
                            </Box>
                    }} />
                </Grid>
            </Grid>

        </Box>
    )
}
