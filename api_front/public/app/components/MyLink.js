
import * as React from 'react';

import { styled } from '@mui/material/styles';
import {
    Link
} from '@mui/material';
import { grey, red } from '@mui/material/colors';


const StyledLink = styled(Link)(({ theme }) => {
    return {
        height: theme.spacing(3),
        color: theme.palette.text.primary,
        textDecoration: 'none',
        fontWeight: theme.typography.fontWeightRegular,
        '&:hover, &:focus': {
            color: theme.palette.text.hover
        },
        '&:active': {
            color: red[900],
            textDecoration: 'underline',
        },
    };

}, []);

export default function MyLink(props) {
    return (
        <StyledLink {...props} />
    )
}
