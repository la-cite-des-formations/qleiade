import React from "react";
import { Chip } from "@mui/material";
import DoneIcon from "@mui/icons-material/Done";
import ReportProblemIcon from '@mui/icons-material/ReportProblem';
import SentimentVeryDissatisfiedIcon from '@mui/icons-material/SentimentVeryDissatisfied';
import EmptyIcon from '@mui/icons-material/SignalCellularConnectedNoInternet0BarOutlined';
import { green, blue, grey, red } from '@mui/material/colors';


function colorForStatus(status) {
    switch (status) {
        case "completed":
            return green;
        case "in progress":
            return blue;
        case "No data":
            return red;
        default:
            return grey;
    }
}

function iconForStatus(status) {
    switch (status) {
        case "completed":
            return <DoneIcon style={{ color: "white" }} />;
        case "in progress":
            return <></>;
        case "No data":
            return <EmptyIcon style={{ color: "white" }} />;
        default:
            return grey;
    }
}

function StatusChip({ status }) {
    return (
        <Chip
            // label={status}
            icon={iconForStatus(status)}
            style={{ backgroundColor: colorForStatus(status)[300], color: "white" }}
        />
    );
}

export default StatusChip;
