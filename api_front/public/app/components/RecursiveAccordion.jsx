import React, { useEffect, useState } from "react";
import { useTranslation } from "react-i18next";

import WealthContent from "@parts/wealth/WealthContent";
import ValidationIcon from "@components/ValidationIcon";

import { Typography, Tooltip, Accordion, AccordionDetails, AccordionSummary, Skeleton, Stack, Box } from '@mui/material';
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import TitleIcon from "@components/ItemIcon";
import Counter from "@components/Counter";


function makeDetails(pdetails, plevel, pexpanded, phandlers) {
    let d = _.map(pdetails, (pdetail) => {
        //si j'ai des indicateurs ou des preuves j'envoie les indicateurs ou les preuves
        return <RecursiveAccordion key={pdetail.item.id} item={pdetail.item} details={pdetail.details} level={plevel} expanded={pexpanded} handlers={phandlers} />;
    })
    return d;
}

function countData(item, data) {
    if (!Array.isArray(data) || data.length === 0) {
        let gt = {};
        gt[item.granularity?.type] = 0
        return gt;
    }

    const occurrences = data.reduce(function (acc, curr) {
        const granularityType = curr.item?.granularity?.type;
        if (granularityType) {
            acc[granularityType] = (acc[granularityType] || 0) + 1;
        }
        return acc;
    }, {});
    return occurrences;
}

//NOTE: expanded set for three recursion
export default function RecursiveAccordion(props) {
    const { item, details, level, expanded, handlers } = props;
    const { t } = useTranslation('common');
    const [subExpanded, setSubExpanded] = useState(false);
    const [counter, setCounter] = useState({});

    function handleSubChange(panel, isExpanded) {
        setSubExpanded(isExpanded ? panel : false);
    };

    const handleChange = (panel) => (event, isExpanded) => {
        handlers.expand(panel, isExpanded);
    };

    useEffect(() => {
        if (item != null && details != null) {
            setCounter(countData(item, details));
        }

    }, [item, details]);

    var accordion = {};
    if (item === null) {
        if (Array.isArray(details) && details.length > 0) {
            let level = 0
            accordion = makeDetails(details, level, expanded, handlers);
        } else {
            const skeleton = () => {
                const s = [];
                for (let i = 0; i < 10; i++) {
                    s.push(<Skeleton key={'toto' + i} variant="rounded" width={'100%'} height={45} />)
                }

                return s
            }
            accordion =
                <Stack spacing={0.5} sx={{ width: "100%" }}>
                    {skeleton()}
                </Stack>;
        }
    }
    else {
        const panelName = "panel" + item.id + (level);
        accordion =
            <Accordion
                TransitionProps={{ unmountOnExit: true }}
                key={item.id + level}
                expanded={expanded === panelName}
                onChange={handleChange(panelName)}
                sx={{ width: '100%' }}
            >
                <AccordionSummary
                    expandIcon={<ExpandMoreIcon />}
                    aria-controls={panelName + "bh-content"}
                    id={panelName + "bh-header"}
                >
                    <Box sx={{ width: '33%', flexShrink: 0 }}>
                        <Stack direction={"row"} spacing={2} >
                            {!Array.isArray(details) ? <ValidationIcon item={item} handlers={handlers} validated={item.currentWrapper.validated} /> : <></>}
                            <TitleIcon item={item} />
                            <Typography >
                                {item.hasOwnProperty('criteria') ? t("quality_label.result.presenter.indicator", { id: item.number }) : item.hasOwnProperty('wealth_type') ? t("quality_label.result.presenter.wealth", { id: item.id }) : item.name}
                            </Typography>
                        </Stack>
                    </Box>
                    <Tooltip title={item.description || t("no_description")}>
                        <div style={{ overflow: "hidden", textOverflow: "ellipsis", width: '20rem' }}>
                            <Typography noWrap sx={{ color: 'text.secondary' }}>{item.label || item.name}</Typography>
                        </div>
                    </Tooltip>
                    <Stack direction={"row"}>
                        {counter ? <Counter counter={counter} /> : <div />}
                    </Stack>
                </AccordionSummary>
                <AccordionDetails>
                    {
                        Array.isArray(details) ?
                            makeDetails(details, (level + 1), subExpanded, { expand: handleSubChange, validateItem: handlers.validateItem })
                            : <WealthContent wealth={item} />
                    }
                </AccordionDetails>
            </Accordion>;
    }
    return (<>
        {accordion}
    </>)
}
