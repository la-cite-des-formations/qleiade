import map from "lodash/map";
import React, { useEffect, useState } from "react";
import { List, ListItem, ListItemText, Divider, Skeleton } from "@mui/material";
import ListWithCollapsableNestedList from "./ListWithCollapsableNestedList";
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

function ClickableList(props) {
    //     {
    //         type: "ListWithCollapsableNestedList",
    //         primary: "",
    //         mapper: {
    //             name: "periods",
    //             handle_select: handleSelect,
    //             data: audit.summary.periods,
    //             itemId: "id",
    //             primaryText_key: "title",
    //             secondaryText_key: "",
    //             type: "period",
    //             clickable: true/false,
    //         }
    const { primary, mapper, loaded } = props;
    useEffect(() => { }, [props.loaded])

    return (<>
        {
            !loaded ? <Skeleton key={"sk_" + primary} width={350} height={50} />
                : <ListWithCollapsableNestedList
                    key={"ck_" + primary}
                    mapper={
                        mapper
                    } />
        }

        <Divider key={"ck_di_" + primary} />
    </>)
}

function DefaultItem(props) {
    const { t } = useTranslation('common');
    const { primary, secondary, withDivider, loaded } = props;

    useEffect(() => { }, [props.loaded])

    return (<>
        {
            !loaded ? <Skeleton key={"sk_" + primary} width={350} height={50} />
                : <>
                    <ListItem key={"di_li_" + primary}>
                        <ListItemText
                            primary={t(primary)}
                            secondary={t(secondary) || t("notprovided")
                            } />
                    </ListItem>
                    {withDivider ? <Divider key={"di_li_di_" + primary} /> : <></>}
                </>
        }
    </>
    )
}

var items = {
    defaultItem: function (props) {
        return <DefaultItem key={"di_" + props.primary} {...props} />
    },

    clickableList: function (props) {
        return <ClickableList key={"cl_" + props.primary} {...props} />
    }

    // itemList: function (props) {
    //     const { primary, mapper, loaded } = props;
    // useEffect(()=>{},[loaded])
    //     return (<>
    //         <List>
    //             <ListItem>
    //                 {
    //                     !loaded ? <Skeleton key={"sk_" + primary} width={350} height={50} />
    //                         : <ListItemText
    //                             primary={primary}
    //                         />
    //                 }
    //             </ListItem>
    //         </List>
    //     </>)
    // }
}

export default function SyntheticView(props) {
    const [list, setList] = useState([]);
    const { mapper } = props;
    useEffect(() => {
        setList(map(mapper, (item) => {
            return items[item.type](item);
        }));
    }, [props.mapper])

    return (<List key={"sv_li_summary_result"} disablePadding>{list}</List>);
}
