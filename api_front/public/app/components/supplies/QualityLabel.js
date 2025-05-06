import React from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from "react-i18next";
//MUI
import { Card, CardActions, CardContent, Typography, CardMedia, Button, Box } from '@mui/material';

import { htmlDecode } from '@services/utils';

//TODO-FRONT faire suivre et voir plus
export default function QualityLabel(props) {
    const { t } = useTranslation('common');

    return (<Card sx={{ minWidth: '100%', minHeight: '100%' }} className="myCard">
        <CardMedia
            // sx={{ minHeight: '100%', minWidth: '100%', objectFit: "contain" }}
            sx={{ height: 140 }}
            component="img"
            alt={props.item.title}
            image={props.item.image}
            title={props.item.title}
        />
        <CardContent sx={{ padding: 0 }}>

            {/* <Typography sx={{ fontSize: 14 }} color="text.secondary" gutterBottom>
                    Word of the Day
                </Typography> */}
            <Box sx={{ padding: 2 }}>
                <Typography variant="h5" component="div" sx={{ marginTop: "5px" }}>
                    {props.item.title}
                </Typography>
                {/* <Typography sx={{ mb: 1.5 }} color="text.secondary">
                    adjective
                </Typography> */}
                <Typography variant="body2" component="div">
                    {htmlDecode(props.item.description)}
                </Typography>

            </Box>
        </CardContent>
        <CardActions
            sx={{
                padding: 2, alignSelf: "stretch",
                display: "flex",
                justifyContent: "flex-end",
                alignItems: "flex-start",
            }}>
            {/* {props.canSee('public_quality_labels_dashboard') ?
                <Button
                    component={Link}
                    to={`/labels/${props.item.title}`}
                    variant="contained"
                    sx={{ mt: 3, ml: 1 }}
                    color="primary"
                >
                    {t('quality_label.button.seeMore')}
                </Button>
                : <div />}
            {props.canSee('public_quality_labels_dashboard') ?
                <Button
                    component={Link}
                    to={`/labels/${props.item.title}/dashboard`}
                    variant="contained"
                    sx={{ mt: 3, ml: 1 }}
                    color="primary"
                >
                    {t('quality_label.button.follow')}
                </Button>
                : <div />} */}
            {props.canSee('public_quality_labels_audit') ?
                <Button
                    component={Link}
                    to={`/labels/${props.item.title}/audit`}
                    variant="contained"
                    sx={{ mt: 3, ml: 1 }}
                    color="primary"
                >
                    {t('quality_label.button.audit')}
                </Button>
                : <div />}
        </CardActions>
    </Card>);
}
