import { map, filter, concat, groupBy, sortBy } from "lodash";
import React, { useEffect, useState, useContext, useCallback } from "react";
import { Tabs, Tab, Box, Tooltip } from "@mui/material";
// import { useTranslation } from "react-i18next";
import RecursiveAccordion from "@components/RecursiveAccordion";
import { ResultContext } from "./Context";

export default function Presenter(props) {
    const { resultValues, handleChange } = useContext(ResultContext);
    const { wrapper, wealths, audit } = resultValues;
    const [expanded, setExpanded] = useState(false);
    const [activeTab, setActiveTab] = useState(0);
    const [tabsData, setTabsData] = useState([]);

    // État pour gérer l'affichage des preuves complémentaires par indicateur
    const [openComplementary, setOpenComplementary] = useState({});
    const toggleComplementary = (indicatorId) => {
        setOpenComplementary(prev => ({
            ...prev,
            [indicatorId]: !prev[indicatorId],
        }));
    };

    useEffect(() => {
        const grouped = makeList(wrapper.current, flatten(wealths));
        setTabsData(grouped);
    }, [wrapper.current, wealths, audit]);

    const handleExpand = (panel, isExpanded) => {
        setExpanded(isExpanded ? panel : false);
    };

    const handleTabChange = (event, newValue) => {
        setActiveTab(newValue);
    };

    const validateWealth = useCallback((wealth) => {
        // logique existante...
    }, [audit, handleChange]);

    const flatten = (data) => Object.values(data).reduce((acc, val) => acc.concat(val), []);

    const isValid = (item, indicator) => {
        const itemIn = filter(audit.validatedWealths, w => w.id === item.id);
        return itemIn.length > 0 && filter(itemIn[0].indicators, ind => ind.id === indicator.id).length > 0;
    };

    const makeList = (wrapperItems, data) => {
        const grouped = groupBy(wrapperItems, indicator => indicator.criteria.id);
        return map(grouped, indicators => {
            const criterion = indicators[0].criteria;
            const list = map(indicators, indicator => {
                const related = filter(data, w => w.indicators.some(el => el.id === indicator.id));
                const wlist = map(related, element => ({
                    item: {
                        ...element,
                        currentWrapper: {
                            id: indicator.id,
                            name: indicator.name,
                            label: indicator.label,
                            validated: isValid(element, indicator),
                            is_essential: element.indicators?.find(i => i.id === indicator.id)?.pivot?.is_essential ?? false
                        }
                    },
                    details: []
                }));
                return {
                    item: {
                        ...indicator,
                        detailsGroups: true
                    },
                    details: wlist
                };
            });
            return {
                id: criterion.id,
                label: criterion.label,
                description: criterion.description,
                list: sortBy(list, ['item.number'])
            };
        });
    };

    return (
        <>
            <Box sx={{ borderBottom: 1, borderColor: 'divider', mb: 2 }}>
                <Tabs value={activeTab} onChange={handleTabChange} variant="scrollable" scrollButtons="auto">
                    {tabsData.map((tab, idx) => (
                        <Tooltip key={tab.id} title={tab.description || ''} arrow>
                            <Tab label={tab.label} />
                        </Tooltip>
                    ))}
                </Tabs>
            </Box>
            {tabsData.map((tab, idx) => (
                <Box
                    key={tab.id}
                    sx={{ width: "100%", display: idx === activeTab ? 'block' : 'none' }}
                >
                    {tab.list.map(({ item, details }) => (
                        <RecursiveAccordion
                            key={item.id}
                            item={item}
                            details={details}
                            level={0}
                            expanded={expanded}
                            handlers={{
                                expand: handleExpand,
                                validateItem: validateWealth,
                                toggleComplementary,
                                openComplementary,
                            }}
                        />
                    ))}
                </Box>
            ))}
        </>
    );
}
