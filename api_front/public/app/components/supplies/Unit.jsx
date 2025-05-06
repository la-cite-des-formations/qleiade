import React from 'react';
import { useTranslation } from "react-i18next";
import { Link } from 'react-router-dom';
//MUI
import { Card, CardActions, CardContent, Typography, CardMedia, Button } from '@mui/material';

//generic card
export default function Unit(props) {
    const { t } = useTranslation('common');


    var slug = props.item.name ? "/" + props.item.name : "";
    var button = <Button
        component={Link}
        to={"/dashboards/unit" + slug}
        variant="contained"
        sx={{ mt: 3, ml: 1 }}
        color="primary"
    >{"Suivre"}</Button>;

    return (<Card sx={{ minWidth: '100%', minHeight: '100%' }} className="unit">
        <CardContent>
            <Typography variant="h5" component="div">
                {props.item.label}
            </Typography>
            <Typography variant="body2">
                {props.item.name}
            </Typography>
        </CardContent>
        <CardActions>
            {button}
        </CardActions>
    </Card>);
}
