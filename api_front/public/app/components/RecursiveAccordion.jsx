import React, { useEffect, useState } from "react";
import { useTranslation } from "react-i18next";

import WealthContent from "@parts/wealth/WealthContent";
import ValidationIcon from "@components/ValidationIcon";

import { Typography, Tooltip, Accordion, AccordionDetails, AccordionSummary, Skeleton, Stack, Box, Button } from '@mui/material';
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import TitleIcon from "@components/ItemIcon";
import Counter from "@components/Counter";
import _ from 'lodash';

function makeDetails(pdetails, plevel, pexpanded, phandlers) {
    return _.map(pdetails, (pdetail) => (
        <RecursiveAccordion
            key={pdetail.item.id}
            item={pdetail.item}
            details={pdetail.details}
            level={plevel}
            expanded={pexpanded}
            handlers={phandlers}
        />
    ));
}

function countData(item, data) {
    if (!Array.isArray(data) || data.length === 0) {
        let gt = {};
        gt[item.granularity?.type] = 0;
        return gt;
    }

    return data.reduce((acc, curr) => {
        const granularityType = curr.item?.granularity?.type;
        if (granularityType) {
            acc[granularityType] = (acc[granularityType] || 0) + 1;
        }
        return acc;
    }, {});
}

export default function RecursiveAccordion(props) {
    const { item, details, level, expanded, handlers } = props;
    const { t } = useTranslation('common');
    const [subExpanded, setSubExpanded] = useState(false);
    const [counter, setCounter] = useState({});

    const handleSubChange = (panel, isExpanded) => {
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

    // Loading state
    if (item === null) {
        if (Array.isArray(details) && details.length > 0) {
            return <>{makeDetails(details, level, expanded, handlers)}</>;
        }
        return (
            <Stack spacing={0.5} sx={{ width: "100%" }}>
                {Array.from({ length: 5 }).map((_, i) => (
                    <Skeleton key={i} variant="rounded" width="100%" height={45} />
                ))}
            </Stack>
        );
    }

    const panelName = `panel${item.id}${level}`;

    return (
        <Accordion
            TransitionProps={{ unmountOnExit: true }}
            expanded={expanded === panelName}
            onChange={handleChange(panelName)}
            sx={{
                width: '100%',
                bgcolor: (() => {
                    const isExpanded = expanded === panelName;

                    // Indicateur
                    if (item.hasOwnProperty('criteria')) {
                        return isExpanded ? '#f1f8e9' : 'background.paper';
                    }

                    // Preuve
                    if (item.hasOwnProperty('wealth_type')) {
                    const matchingIndicator = item.indicators.find((ind) => ind.id === item.currentWrapper.id);
                    const isEssential = matchingIndicator?.pivot?.is_essential;

                        if (isEssential) {
                            return isExpanded ? '#e3f2fd' : 'background.paper';
                        }
                        else {
                            return isExpanded ? '#d0e0f0 ' : '#eeeeee';
                        }
                    }

                    // Autre (fallback)
                    return isExpanded ? '#fffde7' : 'background.paper';
                })(),
                transition: 'background-color 0.3s ease-in-out',
            }}
        >
            <AccordionSummary
                expandIcon={<ExpandMoreIcon />}
                aria-controls={`${panelName}-content`}
                id={`${panelName}-header`}
                sx={{
                    '& .MuiAccordionSummary-content': {
                        display: 'flex',
                        alignItems: 'center',
                        overflow: 'hidden',
                        textOverflow: 'ellipsis',
                        whiteSpace: 'nowrap',
                        gap: '0.5rem',
                    },
                }}
            >
                <Tooltip title={item.label || item.name || t("no_description")}>
                    <Box sx={{ display: 'flex', alignItems: 'center', flex: 1, minWidth: 0, gap: 1 }}>
                        {!item.hasOwnProperty('criteria') && (
                            <ValidationIcon item={item} handlers={handlers} validated={item.currentWrapper?.validated} />
                        )}
                        <TitleIcon item={item} />
                        <Typography noWrap sx={{ flex: 1 }}>
                            {item.hasOwnProperty('criteria') ? `${item.criteria.order}.${item.number} – ${item.label}` : item.name}
                        </Typography>
                    </Box>
                </Tooltip>
            </AccordionSummary>
            <AccordionDetails>
                {/* Niveau indicateur : séparer essentiels vs complémentaires */}
                {level === 0 && Array.isArray(details) ? (
                    (() => {
                        const essential     = details.filter(w => w.item.currentWrapper?.is_essential);
                        const complementary = details.filter(w => !w.item.currentWrapper?.is_essential);
                        const isOpen        = handlers.openComplementary?.[item.id];

                        return (
                            <>
                                {essential.length > 0 && makeDetails(essential, level+1, subExpanded, { expand: handleSubChange, validateItem: handlers.validateItem })}

                                {complementary.length > 0 && (
                                    <Box textAlign="center" my={2}>
                                        <Button size="small" onClick={() => handlers.toggleComplementary(item.id)}>
                                            {isOpen ? "Voir moins…" : "Voir plus…"}
                                        </Button>
                                    </Box>
                                )}

                                {isOpen && complementary.length > 0 && makeDetails(complementary, level+1, subExpanded, { expand: handleSubChange, validateItem: handlers.validateItem })}
                            </>
                        );
                    })()
                ) : Array.isArray(details) ? (
                    makeDetails(details, level+1, subExpanded, { expand: handleSubChange, validateItem: handlers.validateItem })
                ) : (
                    <WealthContent wealth={item} />
                )}
            </AccordionDetails>
        </Accordion>
    );
}
