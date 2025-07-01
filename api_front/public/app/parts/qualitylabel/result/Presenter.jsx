import { map, filter, concat, groupBy, sortBy } from "lodash";
import React, { useEffect, useState, useContext, useCallback } from "react";
import { Tabs, Tab, Box, Tooltip } from "@mui/material";
// import { useTranslation } from "react-i18next";
import RecursiveAccordion from "@components/RecursiveAccordion";
import { ResultContext } from "./Context";

export default function Presenter(props) {
    const { resultValues, handleChange } = useContext(ResultContext);
    const { wrapper, wealths, audit } = resultValues;
    const [list, setList] = useState([]);
    const [expanded, setExpanded] = useState(false);
    const [stepperFilter, setStepperFilter] = useState(0);
    const [activeTab, setActiveTab] = useState(0);
    const [tabsData, setTabsData] = useState([]);


    useEffect(() => {
        setStepperFilter(0);
        const grouped = makeList(wrapper.current, setData(wealths));
        setTabsData(grouped);
    }, [wrapper.current, wealths, audit]);

    function handleExpand(panel, isExpanded) {
        setExpanded(isExpanded ? panel : false);
    };

    const handleTabChange = (event, newValue) => {
        setActiveTab(newValue);
    };

    const validateWealth = useCallback((wealth) => {
        //la liste du context
        var contextValidatedWealths = audit.validatedWealths;

        //la liste  à set
        var listToSet = [];

        //la preuve est elle dans le context?
        var filteredWealthInContext = filter(contextValidatedWealths, (w) => w.id === wealth.id);
        if (filteredWealthInContext.length === 0) {
            // création de la preuve à stocker dans la list

            //formatage de la preuve
            var validatedWealth = {
                id: wealth.id,
                title: wealth.title,
                granularity: wealth.granularity,
                indicators: [wealth.currentWrapper]
            }
            //ajout de la preuve
            listToSet = [
                ...contextValidatedWealths,
                validatedWealth
            ];

        } else {
            //mise à jour de la preuve du context avec le nouvel indicateur
            var updatedIndicators = [];
            var filteredIndicatorsInContext = filter(filteredWealthInContext[0].indicators, (ind) => ind.id === wealth.currentWrapper.id);
            if (filteredIndicatorsInContext.length === 0) {
                //ajout du nouvel indicateur
                updatedIndicators = [
                    ...filteredWealthInContext[0].indicators,
                    wealth.currentWrapper
                ];
            } else {
                // je purge
                updatedIndicators = filter(filteredWealthInContext[0].indicators, (ind) => ind.id !== wealth.currentWrapper.id);
            }


            // formatage de la preuve mise à jour
            var updatedValidatedWealth = {
                ...filteredWealthInContext[0],
                indicators: updatedIndicators
            };

            if (updatedValidatedWealth.indicators.length > 0) {
                listToSet = [
                    ...filter(contextValidatedWealths, (w) => w.id !== wealth.id),
                    updatedValidatedWealth
                ]
            } else {
                listToSet = filter(contextValidatedWealths, (w) => w.id !== wealth.id);
            }
        }


        handleChange({ type: "SET", name: "audit", who: "validatedWealths", value: listToSet });

    })


    function setData(data) {
        return Object.values(data).reduce((acc, val) => acc.concat(val), []);
    }

    //vérification de la validation de l'item dans makeList
    function isValid(item, indicator) {
        var itemIn = filter(audit.validatedWealths, (w) => w.id === item.id);
        return itemIn.length > 0 && filter(itemIn[0].indicators, (ind) => ind.id === indicator.id).length > 0;
    }

    const makeList = (wrapper, data) => {
        const grouped = groupBy(wrapper, (indicator) => indicator.criteria.id);

        const listByCriteria = map(grouped, (indicators, criteriaId) => {
            const criterion = indicators[0].criteria; // tous les indicateurs partagent le même critère

            const list = map(indicators, (indicator) => {
                const wealthList = filter(data, (wealth) => {
                    return wealth.indicators.some((element) => element.id === indicator.id);
                });

                const wlist = map(wealthList, (element) => {
                    element.step = stepperFilter;
                    return {
                        item: {
                            ...element,
                            currentWrapper: {
                                id: indicator.id,
                                name: indicator.name,
                                label: indicator.label,
                                validated: isValid(element, indicator)
                            }
                        },
                        details: element.content || element.description
                    };
                });

                return {
                    item: indicator,
                    details: wlist,
                };
            });

            return {
                id: criterion.id,
                label: criterion.label,
                description: criterion.description,
                order: criterion.order ?? 0, // fallback si absent
                list: sortBy(list, ['item.number']),
            };
        });

        setStepperFilter(prev => prev + 1);

        // Tri final par ordre croissant des critères
        return sortBy(listByCriteria, ['order']);
    }

    return (
        <>
            <Box sx={{ borderBottom: 1, borderColor: 'divider', mb: 2 }}>
                <Tabs value={activeTab} onChange={handleTabChange} variant="scrollable" scrollButtons="auto">
                    {tabsData.map((tab, index) => (
                        <Tooltip key={tab.id} title={tab.description || ""} arrow>
                            <Tab label={tab.label} />
                        </Tooltip>
                    ))}
                </Tabs>
            </Box>

            {tabsData.map((tab, index) => (
                <Box
                    key={tab.id}
                    sx={{ width: "100%", display: activeTab === index ? "block" : "none" }}
                >
                    <RecursiveAccordion
                        item={null}
                        details={tab.list}
                        level={0}
                        expanded={expanded}
                        handlers={{ expand: handleExpand, validateItem: validateWealth }}
                    />
                </Box>
            ))}
        </>
    );
}
