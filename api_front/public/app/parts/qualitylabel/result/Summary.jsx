import React, { useContext, useEffect, useState, forwardRef } from "react";
import { Box, Typography, Skeleton, IconButton, Tooltip } from "@mui/material";
import { useTranslation } from "react-i18next";
import { ResultContext } from "@parts/qualitylabel/result/Context";
import DriveFileRenameOutlineIcon from '@mui/icons-material/DriveFileRenameOutline';
import ThumbUpOffAltIcon from '@mui/icons-material/ThumbUpOffAlt';
import ValidateAudit from "./ValidateAudit";
import BlocComment from "@components/BlocComment";
import SyntheticView from "@components/SyntheticView";

const Title = ({ label }) => {
    const { t } = useTranslation('common');
    useEffect(() => { }, [label])
    return (
        <Typography variant="h4" align="center">
            {
                !label ? <Skeleton variant="h4" width={350} height={50} />
                    : t("quality_label.result.summary.title", { label: label })
            }
        </Typography>
    );
}

const Actions = (props) => {
    return (
        <>
            <Comments {...props} />
            <Validate {...props} />
        </>
    )
}

const Comments = (props) => {
    const { handleChange } = useContext(ResultContext);
    const [isBlocCommentOpen, setIsBlocCommentOpen] = useState(false);
    const { t } = useTranslation('common');

    const handleOpenBlocComment = () => {
        setIsBlocCommentOpen((prevOpen) => (!prevOpen));
    };

    const handleBlocCommentClose = (text) => {
        // setComment(text);
        handleChange({ type: "SET", who: "comment", name: "audit", value: text })
        setIsBlocCommentOpen(false);
    };

    return (
        <>
            <Tooltip title={t("quality_label.result.tooltips.synthese")} placement="right" >
                <IconButton onClick={handleOpenBlocComment}>
                    <DriveFileRenameOutlineIcon />
                </IconButton>
            </Tooltip>
            <BlocComment isOpen={isBlocCommentOpen} onClose={handleBlocCommentClose} />
        </>

    )
}

const Validate = (props) => {
    const { label } = props;
    const { resultValues, handleChange } = useContext(ResultContext);
    // const { toast } = useContext(SnackbarContext);
    const [open, setOpen] = useState(false);
    const { t } = useTranslation('common');

    const handleOpen = () => {
        setOpen((prevOpen) => (!prevOpen));
    };

    const handleClose = () => {
        setOpen(false);
    };

    return (
        <div>
            <Tooltip title={t("quality_label.result.tooltips.validate_audit")} placement="right" >
                <IconButton onClick={handleOpen}>
                    <ThumbUpOffAltIcon />
                </IconButton>
            </Tooltip>
            <ValidateAudit label={label} data={resultValues.audit} open={open} onClose={handleClose} />
        </div>
    );
}

export default function Summary(props) {
    const { handleChange, resultValues } = useContext(ResultContext);
    const [synthticViewMapper, setSynthticViewMapper] = useState([])
    const { t } = useTranslation("common");
    const { audit } = resultValues;
    const { label } = props;

    useEffect(() => {
        setSynthticViewMapper([
            {
                type: "defaultItem",
                primary: "quality_label.result.summary.extern",
                secondary: audit.summary?.extern ? "Auditeur externe" : "Auditeur interne",
                loaded: audit.summary,
                withDivider: true
            },
            {
                type: "defaultItem",
                primary: "quality_label.result.summary.audit_type",
                secondary: audit.summary?.audit_type,
                loaded: audit.summary,
                withDivider: true
            },
            {
                type: "clickableList",
                primary: "periods",
                loaded: audit.summary,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.periods",
                    handle_select: handleSelect,
                    data: audit.summary?.periods,
                    itemId: "id",
                    primary: { key: "title" },
                    type: "period",
                    clickable: true
                }
            },
            {
                type: "clickableList",
                primary: "formations",
                loaded: audit.summary,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.formations",
                    handle_select: handleSelect,
                    data: audit.summary?.formations,
                    itemId: "id",
                    primary: { key: "title" },
                    secondary: { key: "" },
                    type: "formation",
                    clickable: true
                }
            },
            {
                type: "clickableList",
                primary: "groups",
                loaded: audit.summary,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.groups",
                    handle_select: handleSelect,
                    data: audit.summary?.groups,
                    itemId: "id",
                    primary: { key: "title" },
                    secondary: { key: "formation" },
                    type: "group",
                    clickable: true
                }
            },
            {
                type: "clickableList",
                primary: "students",
                loaded: audit.summary,
                withDivider: true,
                mapper: {
                    name: "quality_label.result.summary.students",
                    handle_select: handleSelect,
                    data: audit.summary?.students,
                    itemId: "id",
                    primary: { key: "fullName" },
                    secondary: { key: "ine" },
                    type: "student",
                    clickable: true
                }
            }

        ]);
    }, [audit.summary])

    function handleSelect(item) {
        handleChange({ type: "SET", name: "filters_selected", who: item.type, value: [item] });
    }

    return (
        <>
            <Box sx={{
                // backgroundColor: "rgba(0, 0, 0, 0.12)",
                // position: "absolute",
                // left: "50px",
                borderRadius: "5px",
                width: "100%",
                height: "3em"
            }} >
                <Actions label={label} />
            </Box>
            <Box sx={{ my: 5 }}>
                <Title label={label} />
                <Typography variant="subtitle2" align="center" sx={{ mt: 2 }}>
                    {
                        !audit.summary ? <Skeleton variant="subtitle2" width={350} />
                            : t("quality_label.result.summary.subtitle")
                    }
                </Typography>
            </Box>
            <Box>
                <SyntheticView key={"sv_summary_result"} mapper={synthticViewMapper} />
            </Box>
        </>)
}
