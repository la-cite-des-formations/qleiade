import React from "react";
import { Box, Card, Grid, CardContent, CardHeader, Stack, Typography } from "@mui/material";
import StatusChip from "./StatusChip";
import EmptyIcon from '@mui/icons-material/SignalCellularConnectedNoInternet0BarOutlined';
import FullIcon from '@mui/icons-material/SignalCellular4BarOutlined';
import DoneIcon from "@mui/icons-material/Done";

function iconForStatus(relations) {
    var status = 'No data';
    if (relations > 0) {
        status = 'completed';
    }
    switch (status) {
        case "completed":
            return <FullIcon style={{ color: "green" }} />;
        case "in progress":
            return <></>;
        case "No data":
            return <EmptyIcon style={{ color: "red" }} />;
        default:
            return grey;
    }
}

//TODO-FRONT signaler si les actions ont des preuves le status est géré par le nombre de relations pour l'instant.
export default function Action(props) {
    const { item } = props;
    return (<Card sx={{ ...item.size, border: 'solid 1px green' }} >
        <CardContent>
            <Stack >
                <Stack direction="row" spacing={18}>
                    {/* faire une box */}
                    <Typography sx={{ fontSize: 12 }} color="text.secondary" gutterBottom>
                        {item.order ? "Activité " + item.order : "Activité"}
                    </Typography>
                    {iconForStatus(item.relations)}
                </Stack>

                <Typography sx={{ fontSize: 15 }} variant="h5" component="div">
                    {item.label}
                </Typography>
            </Stack>
        </CardContent>
    </Card>)
}
