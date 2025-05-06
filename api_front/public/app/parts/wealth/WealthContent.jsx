import React, { useEffect } from 'react';
import { Typography, Box, Icon, Button } from '@mui/material';
import { useTranslation } from 'react-i18next';
import { htmlDecode } from '@services/utils';

//TODO-FRONT ajouter les traductions pour gérer les différents cas file - ypareo - link
function getContent(wealth) {
    var content = "no content";
    if (wealth.wealth_type.name === 'file') {
        if (wealth?.files?.length > 0) {
            content = <>
                {wealth.files.map((file) => (
                    <Box key={file.id}>
                        <Typography noWrap sx={{ color: 'text.secondary' }}>{"Voici le lien du fichier gdrive"}</Typography>

                        <a href={file.gdrive_shared_link} target="_blank" rel="noopener noreferrer" className="text-u-l">
                            {file.original_name}
                        </a>
                    </Box>
                ))}
            </>;
        }
    }
    if (wealth?.wealth_type?.name === 'link') {
        content = <Box>
            <Typography noWrap sx={{ color: 'text.secondary' }}>
                {`Voici le lien ${wealth.attachment.link.type} permettant d'accéder à cette preuve`}
            </Typography>
            <a href={wealth.attachment.link.url} target="_blank" rel="noopener noreferrer" className="text-u-l">
                {wealth?.attachment?.link.url}
            </a>
        </Box>;
    }

    if (wealth?.wealth_type?.name === 'ypareo') {
        content = <Box>
            <Typography noWrap sx={{ color: 'text.secondary' }}>
                {"Voici le process ypareo permettant d'accéder à cette preuve"}
            </Typography>
            {/* <div dangerouslySetInnerHTML={{ __html: wealth?.attachment?.ypareo?.process }} /> */}
            {htmlDecode(wealth?.attachment?.ypareo?.process)}
        </Box>;
    }

    return content;
}

function WealthContent(props) {
    const { wealth } = props;
    const { t } = useTranslation('common');
    useEffect(() => {
    }, [props.wealth]);

    return (
        <Box className="col-md-6 my-2">
            {/* <Typography variant="h4" color="textSecondary" fontWeight="light">
                <span className="ms-3 text-grey">{t('wealth.content.title')}</span>
            </Typography> */}
            <Typography component="div" className="ms-md-5 ps-md-1">
                {
                    getContent(wealth)}
            </Typography>
        </Box>
    );
}

export default WealthContent;
