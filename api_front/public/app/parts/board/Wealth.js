import React from "react";
import { Box, Card, Typography, CardContent } from "@mui/material";

export default function Wealth(props) {
    const { item } = props;
    return <Card sx={{ ...item.size, border: 'solid 1px blue' }} >
        <CardContent>
            <Typography sx={{ fontSize: 12 }} color="text.secondary" gutterBottom>
                {"Preuve"}
            </Typography>
            <Typography sx={{ fontSize: 15 }} variant="h5" component="div">
                {item.name}
            </Typography>
            <Typography sx={{ fontSize: 12 }} component="p">
                {item.description}
            </Typography>
        </CardContent>
    </Card>
}
