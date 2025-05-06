
import React, { useContext, useEffect, useState } from 'react';
import Tiles from '@mypackages/react-tiles-layout-factory/Tiles';
import API from '@services/axios';
import { AuthContext } from '@services/middleWear/auth/context';
import { useTranslation } from "react-i18next";
import { useNavigate } from 'react-router-dom';


export default function OverView() {
    const { t, i18n } = useTranslation('common');
    const [items, setItems] = useState([]);
    const auth = useContext(AuthContext);
    const nav = useNavigate();

    useEffect(() => {
        const unit = auth.getUnit();
        if (unit.length === 1) {
            var route = "/dashboards/unit/" + unit[0].name;
            nav(route)
        }
        const menu = [
            {
                id: "boardsOverview",
                title: t("boardsOverview.welcome.title"),
                description: t("boardsOverview.welcome.description"),
                type: "Hello",
                layout: {
                    i: "boardsOverview",
                    x: 0,
                    y: 0,
                    h: 12,
                    w: 3,
                    static: true,
                    minh: 8,
                    minw: 3
                }
            }
        ];
        var all = menu.concat(unit);
        setItems(all);
    }, [])

    return (
        <main className="py-4 bg-grey">
            <Tiles items={items} page="boardsoverview" canSee={auth.canSee} />
        </main>
    );

};
