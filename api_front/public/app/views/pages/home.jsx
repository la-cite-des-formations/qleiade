
import React, { useContext, useEffect, useState } from 'react';
import Tiles from '@mypackages/react-tiles-layout-factory/Tiles';
import { AuthContext } from '@services/middleWear/auth/context';
import { useTranslation } from "react-i18next";
import {
    Typography, List, ListItem, ListItemButton, Tooltip
} from '@mui/material';
import MyLink from '@components/MyLink';

function QuickMenu() {
    const { t } = useTranslation('common');
    return (
        <>
            <Typography variant="h5" component="div" sx={{ margin: '2rem' }}>
                {t("Accès rapide")}
                {/* {t("home.menu.title")} */}
            </Typography>
            <List key={"quickMenulist"} disablePadding>
                <ListItem >
                    <Tooltip title={t('home.administer.description')} placement="top-end" enterDelay={2000}>
                        <ListItemButton sx={{ boxShadow: '0px 0px 0.5px 0.5px grey' }}>
                            <MyLink href='/admin'>
                                {t("home.administer.title")}
                            </MyLink>
                        </ListItemButton>
                    </Tooltip>
                </ListItem>
                <ListItem>
                    <Tooltip title={t("home.overview.description")} placement='top-end' enterDelay={2000}>
                        <ListItemButton sx={{ boxShadow: '0px 0px 0.5px 0.5px grey' }}>
                            <MyLink href='/labels'>
                                {t("home.overview.title")}
                            </MyLink>
                        </ListItemButton>
                    </Tooltip>
                </ListItem>
                <ListItem>
                    {/* <Tooltip title={t("home.dashboard.description")} placement='top-end' enterDelay='2000'>
                        <ListItemButton sx={{ boxShadow: '0px 0px 0.5px 0.5px grey' }}>
                            <MyLink href='/dashboards'>
                                {t("home.dashboard.title")}
                            </MyLink>
                        </ListItemButton>
                    </Tooltip> */}
                </ListItem>
            </List>

        </>

    );
}

export default function Home() {
    const { t, i18n } = useTranslation('common');
    const [items, setItems] = useState([]);
    const auth = useContext(AuthContext);

    //TODO-FRONT ajouter l'appel pour récupérer le contenu de l'edito
    //TODO-FRONT ajouter le feed

    useEffect(() => {

        const menu = [
            {
                id: "menuAction",
                title: t("home.menu.title"),
                description: t("home.menu.description"),
                type: "Container",
                children: <QuickMenu />,
                layout: {
                    i: "menuAction",
                    x: 0,
                    y: 0,
                    h: 25,
                    w: 2,
                    static: true,
                    minh: 13,
                    minw: 3
                }
            },

            {
                id: "edito",
                title: t("home.edito.title"),
                description: t("home.edito.description"),
                type: "Hello",
                layout: {
                    i: "edito",
                    x: 2,
                    y: 0,
                    h: 25,
                    w: 10,
                    static: true,
                    minh: 25,
                    minw: 6
                }
            },
            // {
            //     id: "feed",
            //     title: t("home.feed.title"),
            //     description: t("home.feed.description"),
            //     type: "Hello",
            //     layout: {
            //         i: "hellofeed",
            //         x: 9,
            //         y: 0,
            //         h: 25,
            //         w: 4,
            //         static: true,
            //         minh: 13,
            //         minw: 3
            //     }
            // },
        ];
        var all = menu;
        setItems(all);

    }, [])

    return (
        <main className="py-4 bg-grey">
            <Tiles items={items} page="home" canSee={auth.canSee} />
        </main>
    );

};
