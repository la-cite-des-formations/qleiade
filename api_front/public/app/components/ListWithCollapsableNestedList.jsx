import React, { useEffect, useState } from "react";
import { List, ListItem, ListItemText, ListItemButton, Collapse } from "@mui/material";
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import ExpandMore from "@components/ExpandMore";
import map from "lodash/map";
import { useTranslation } from "react-i18next";

function mapArrayAccordingToKeys(item, arr) {
    var params = {};
    for (const prop in arr) {
        const value = arr[prop];
        params[prop] = item[value] ? item[value] : value;
        params[prop] = Array.isArray(params[prop]) ? formatArray(params[prop]) : params[prop];
    }
    return params
}

function formatArray(arr) {
    const tmp = _.map(arr, (ind) => {
        return ind.name;
    })
    const joined = _.join(tmp);
    return joined;
}

const ClickableItem = (props) => {
    const { item, mapper, handleClick, selected } = props;
    const { t } = useTranslation('common');
    useEffect(() => {
    }, [item, mapper])
    return (<ListItem key={"ci_" + item[mapper.itemId]}>
        <ListItemButton
            onClick={() => handleClick(item)}
            selected={selected[item[mapper.itemId]]}
        // disabled={disabled[item[mapper.itemId]]} j'ai un problème ici
        >
            <ListItemText
                primary={item[mapper.primary?.key]}
                secondary={mapper.secondary?.key ? item[mapper.secondary?.key] || "" : mapper.secondary ? t(mapper.secondary?.text, mapArrayAccordingToKeys(item, mapper.secondary.params)) : ""}
                sx={{ pl: 4 }}
            />
        </ListItemButton>
    </ListItem>);
}

const UnclickableItem = (props) => {
    const { item, mapper } = props;
    const { t } = useTranslation('common');
    useEffect(() => { }, [item, mapper])

    return (
        <ListItem key={"uci_" + item[mapper.itemId]}>
            <ListItemText
                primary={item[mapper.primary?.key]}
                secondary={mapper.secondary?.key ? item[mapper.secondary?.key] || "" : mapper.secondary ? t(mapper.secondary?.text, mapArrayAccordingToKeys(item, mapper.secondary.params)) : ""}
                sx={{ pl: 4 }}
            />
        </ListItem>
    );
}

const ListWithCollapsableNestedList = (props) => {
    const { t } = useTranslation('common')
    const { mapper } = props;

    const [expanded, setExpanded] = useState({});
    const [selected, setSelected] = useState({});
    // const [disabled, setDisabled] = useState({});

    const handleExpandClick = (e) => {
        const name = e.currentTarget.name;
        setExpanded((prevExpanded) => ({ ...prevExpanded, [name]: !prevExpanded[name] }));
    };

    const setDisabledCallback = (prevDisabled, item) => {
        return { ...prevDisabled, [item[mapper.itemId]]: prevDisabled[item[mapper.itemId]] };
    }

    const handleClick = (item) => {
        mapper.handle_select(item);
        setSelected((prevSelected) => ({ [item[mapper.itemId]]: !prevSelected[item[mapper.itemId]] }));
        // setDisabled((prevDisabled) => (setDisabledCallback(prevDisabled, item)));
    };

    useEffect(() => { }, [mapper]);

    return <>
        <ListItem key={"lwc_" + mapper.name}>
            <ListItemText
                primary={t(mapper.name)}
            />
            <ExpandMore
                expand={expanded[mapper.name]}
                onClick={handleExpandClick}
                aria-expanded={expanded[mapper.name]}
                aria-label={mapper.name}
                name={mapper.name}
                badgecontent={mapper.data.length || 0}
            >
                <ExpandMoreIcon />
            </ExpandMore>

        </ListItem>
        <Collapse in={expanded[mapper.name]} timeout="auto" unmountOnExit>
            <List key={"lcil_" + mapper.name} component="div" disablePadding>
                {
                    map(mapper.data, (item) => {
                        if (item) {
                            return (
                                mapper.clickable ? <ClickableItem key={"lcil_ci_" + mapper.name + item.id} item={item} mapper={mapper} handleClick={handleClick} selected={selected} /> : <UnclickableItem key={"lcil_ui_" + mapper.name + item.id} item={item} mapper={mapper} />
                            );
                        }
                    })
                }
            </List>
        </Collapse>
    </>
}

export default ListWithCollapsableNestedList;
