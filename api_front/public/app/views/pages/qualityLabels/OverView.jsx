
import React, { useContext, useEffect, useState } from 'react';
import Tiles from '@mypackages/react-tiles-layout-factory/Tiles';
import API from '@services/axios';
import { AuthContext } from '@services/middleWear/auth/context';
import { useTranslation } from "react-i18next";

export default function Home() {
    const { t, i18n } = useTranslation('common');
    const [items, setItems] = useState([]);
    const { canSee } = useContext(AuthContext);

    useEffect(() => {
        API.get(`/qualityLabel`, {
            params: { q: "all" }
        })
            .then((response) => {
                const labels = response.data.data;

                const menu = [
                    {
                        id: "home",
                        title: t("home.welcome.title"),
                        description: t("home.welcome.description"),
                        type: "Hello",
                        layout: {
                            i: "Hellohome",
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
                var all = menu.concat(labels);
                setItems(all);
            })


    }, [])

    return (
        <main className="py-4 bg-grey">
            <Tiles items={items} page="qualityLabelOverview" canSee={canSee} />
        </main>
    );

};
