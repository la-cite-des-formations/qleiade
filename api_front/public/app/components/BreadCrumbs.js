import * as React from 'react';
import Breadcrumbs from '@mui/material/Breadcrumbs';
import { NavLink } from 'react-router-dom';
import { useMatches } from 'react-router-dom';
import { emphasize, styled } from '@mui/material/styles';
import _ from 'lodash';
import { amber, grey } from '@mui/material/colors';
import { useTranslation } from "react-i18next";

function handleClick(event) {
    event.preventDefault();
}

const StyledBreadcrumb = styled(NavLink)(({ theme }) => {
    return {
        height: theme.spacing(3),
        color: grey[500],
        textDecoration: 'none',
        fontWeight: theme.typography.fontWeightRegular,
        '&:hover, &:focus': {
            color: grey[50]
        },
        '&:active': {
            color: grey[50],
            textDecoration: 'underline',
        },
    };
});


export default function BreadCrumbs() {
    const { t } = useTranslation('common');
    const matches = useMatches();
    let path = matches[matches.length - 1].pathname;
    let a = path.split('/');
    let url = "";
    const links = _.map(a, (item, key) => {
        if (item !== '') {
            url += '/' + item;
            if (item === 'home') {
                return null;
            }
            return (

                <StyledBreadcrumb
                    key={key}
                    to={url}
                    style={{ color: key === a.length - 1 ? grey[50] : grey[500] }}
                >
                    {item.toUpperCase()}
                </StyledBreadcrumb>
            )
        } else {
            return (
                <StyledBreadcrumb
                    key={key}
                    to={`/home`}
                >
                    {t('breadcrumbs.home')}
                </StyledBreadcrumb>)
        }
    })




    return (
        <div role="presentation" onClick={handleClick}>
            <Breadcrumbs
                aria-label="breadcrumb"
                separator={<div style={{ color: grey[500] }}>/</div>}
            >
                {links}
            </Breadcrumbs>
        </div>
    );
}
