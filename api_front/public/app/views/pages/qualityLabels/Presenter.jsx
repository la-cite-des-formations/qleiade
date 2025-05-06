import React, { useEffect, useState, useContext } from "react";
import { useParams } from "react-router-dom";

import Tiles from '@mypackages/react-tiles-layout-factory/Tiles';
import API from '@services/axios';
import { AuthContext } from '@services/middleWear/auth/context';
import { useTranslation } from "react-i18next";


export default function Presenter(props) {
    const { t } = useTranslation('common');
    const { label } = useParams();
    const [items, setItems] = useState([]);
    const { canSee } = useContext(AuthContext);

    useEffect(() => {
        const menu = [
            {
                id: "labelpresenter",
                title: t("quality_label.welcome.title", { "label": label }),
                description: t("quality_label.welcome.description", { "label": label }),
                type: "Hello",
                layout: {
                    i: "labelpresenter",
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
        setItems(menu);
    }, [])
    return (
        <main className="py-4 bg-grey">
            <Tiles items={items} page="presenter" canSee={canSee} />
        </main>
    )
}
