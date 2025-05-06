import React from "react";
import { Box, Card, CardContent, Typography } from "@mui/material";

export default function Indicator(props) {
    const { item } = props;
    return (<Card sx={{ ...item.size, border: 'solid 1px orange' }} >
        <CardContent>
            <Typography sx={{ fontSize: 12 }} color="text.secondary" gutterBottom>
                {item.qualityLabel}
            </Typography>
            <Typography sx={{ fontSize: 15 }} variant="h5" component="div">
                {item.name + ":" + item.label}
            </Typography>
        </CardContent>
    </Card>)
}
