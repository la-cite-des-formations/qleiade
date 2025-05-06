import React from "react";
import { styled, Badge, IconButton } from "@mui/material"

const ExpandMore = styled((params) => {
    const { expand, ...other } = params;
    return (
        <Badge badgeContent={params.badgecontent || 0} color="secondary"
            anchorOrigin={{
                vertical: 'top',
                horizontal: 'left',
            }}>
            <IconButton {...other} />
        </Badge>
    );
})(({ theme, expand }) => ({
    transform: !expand ? 'rotate(0deg)' : 'rotate(180deg)',
    marginLeft: 'auto',
    transition: theme.transitions.create('transform', {
        duration: theme.transitions.duration.shortest,
    }),
}));

export default ExpandMore;
