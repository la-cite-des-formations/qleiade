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
                    <Box key={file.id} sx={{ display: 'flex', justifyContent: 'flex-end', mt: 1 }}>
                        <Button color="primary" variant="contained" sx={{ '&:hover': {color: 'white'}}}
                                href={file.gdrive_shared_link} target="_blank" rel="noopener noreferrer">
                            {"Ouvrir"}
                        </Button>
                    </Box>
                ))}
            </>;
        }
    }
    if (wealth?.wealth_type?.name === 'link') {
        content =
            <Box sx={{ display: 'flex', justifyContent: 'flex-end', mt: 1 }}>
                <Button color="primary" variant="contained" sx={{ '&:hover': {color: 'white'}}}
                        href={wealth.attachment.link.url} target="_blank" rel="noopener noreferrer">
                    {"Ouvrir"}
                </Button>
            </Box>;
    }

    if (wealth?.wealth_type?.name === 'ypareo') {
        content = <Box>
            <h6 style={{ fontWeight: 'bold' }}>
                {"Process ypareo"}
            </h6>
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
        <Box className="col-md-12 my-2">
            {/* <Typography variant="h4" color="textSecondary" fontWeight="light">
                <span className="ms-3 text-grey">{t('wealth.content.title')}</span>
            </Typography> */}
            <Typography component="div">
                <h5 style={{ fontWeight: 'bold' }}>{"Service " + wealth.unit_label}</h5>
                {htmlDecode(wealth.description)}
                {getContent(wealth)}
            </Typography>
        </Box>
    );
}

export default WealthContent;
