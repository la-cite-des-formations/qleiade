import React from "react";
import { Box, styled } from "@mui/material";


const StyledBox = styled(Box)(({ theme }) => ({
    paddingTop: 5,
    paddingLeft: 2,
    paddingRight: 2,
    background: theme.palette.background.stepform,
    borderRadius: theme.shape.borderRadius,
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
    boxShadow: "2px 2px 4px 1px rgba(0, 0, 0, 0.1)",
    width: "100%",
    height: "100%"
}));


export default function Container(props) {
    return (
        <StyledBox>
            {props.item.children}
        </StyledBox>
    );
}
