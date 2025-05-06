import React from "react";
import { Box, styled } from "@mui/material";


const StyledBox = styled(Box)(({ theme }) => ({
    marginBottom: 16,
    padding: 10,
    background: theme.palette.background.stepform,
    borderRadius: theme.shape.borderRadius,
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
    boxShadow: "2px 2px 4px 1px rgba(0, 0, 0, 0.1)",
}));


export default function Container(props) {
    return (
        <StyledBox {...props}>
            {props.item.children}
        </StyledBox>
    );
}
