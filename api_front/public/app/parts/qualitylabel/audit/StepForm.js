import React, { useContext, useEffect } from "react";
import { Box, Stepper, Step, StepLabel, Typography } from "@mui/material";
import FirstStep from "./FirstStep";
import SecondStep from "./SecondStep";
import ThirdStep from "./ThirdStep";
import FourthStep from "./FourthStep";
import Confirm from "./Confirm";
import Success from "./Success";
import { StepContext } from "./Context";
import { useTranslation } from "react-i18next";

export default function StepForm({ label }) {
    const { t } = useTranslation("common");
    const { activeStep, handleChange } = useContext(StepContext);

    const labels = [t("quality_label.audit.step.one"), t("quality_label.audit.step.two"), t("quality_label.audit.step.three"), t("quality_label.audit.step.four"), t("quality_label.audit.step.five")];

    useEffect(() => {
        var e = {
            target: {
                type: "text",
                value: label,
                name: 'qualityLabel'
            }
        };
        handleChange(e);
    }, [label])

    const handleSteps = (step) => {
        switch (step) {
            case 0:
                return <FirstStep />;
            case 1:
                return <SecondStep />;
            case 2:
                return <ThirdStep />;
            case 3:
                return <FourthStep />;
            case 4:
                return <Confirm />;
            default:
                throw new Error(t("error.unknownStep"));
        }
    };
    return (
        <>
            {activeStep === labels.length ? (
                <Success label={label} />
            ) : (
                <>
                    <Box sx={{ my: 5 }}>
                        <Typography variant="h4" align="center">
                            {t("quality_label.audit.title", { label: label })}
                        </Typography>
                        <Typography variant="subtitle2" align="center" sx={{ mt: 2 }}>
                            {t("quality_label.audit.description")}
                        </Typography>
                    </Box>
                    <Stepper activeStep={activeStep} sx={{ py: 3 }} alternativeLabel>
                        {labels.map((label) => (
                            <Step key={label}>
                                <StepLabel>{label}</StepLabel>
                            </Step>
                        ))}
                    </Stepper>

                    {handleSteps(activeStep)}
                </>
            )}
        </>
    );
}
