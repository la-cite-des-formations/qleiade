import React, { useEffect, useState, useContext } from "react";
import { Container, Box, ListItemSecondaryAction, styled } from '@mui/material';
import StepForm from "@parts/qualityLabel/audit/StepForm";
import { StepsProvider } from "@parts/qualityLabel/audit/Context";
import { Navigate, useParams } from 'react-router-dom';
import { AuthContext } from '@services/middleWear/auth/context';


const StyledStepper = styled(Box)(({ theme }) => ({
    paddingTop: 5,
    paddingLeft: 2,
    paddingRight: 2,
    background: theme.palette.background.stepform,
    borderRadius: theme.shape.borderRadius,
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
    boxShadow: "2px 2px 4px 1px rgba(0, 0, 0, 0.1)",
    minHeight: "50em",
    Width: "50em",
}));

export default function AuditForm() {
    let { label } = useParams();
    const { canSee } = useContext(AuthContext);

    useEffect(() => {
    }, []);

    return (
        <>
            {
                canSee('public_quality_labels_audit') ? (< Container component="main" maxWidth="md" sx={{ paddingTop: 5 }
                }>
                    <StyledStepper>
                        <StepsProvider >
                            <StepForm label={label} sx={{ width: '100em' }} />
                        </StepsProvider>
                    </StyledStepper>
                </Container >) : (<Navigate to={'/home'} />)
            }
        </>
    )
}
