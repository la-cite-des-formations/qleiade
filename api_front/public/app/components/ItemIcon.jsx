import React, { useState, useEffect } from "react";
import AutoStoriesOutlinedIcon from '@mui/icons-material/AutoStoriesOutlined';
import LinkSharpIcon from '@mui/icons-material/LinkSharp';
import FactorySharpIcon from '@mui/icons-material/FactorySharp';
import EqualizerIcon from '@mui/icons-material/Equalizer';

export default function TitleIcon({ item }) {
    const [icon, setIcon] = useState(<div />);
    const type = item.hasOwnProperty('criteria') ? 'indicator' :
        item.hasOwnProperty('wealth_type') ? 'wealth' :
            '';

    useEffect(() => {
        if (type === 'indicator') {
            setIcon(<EqualizerIcon />);
        }
        if (type === 'wealth') {
            let picon;
            for (const key in item.attachment) {
                switch (key) {
                    case 'file':
                        picon = <AutoStoriesOutlinedIcon />;
                        break;
                    case 'link':
                        picon = <LinkSharpIcon />;
                        break;
                    case 'ypareo':
                        picon = <FactorySharpIcon />;
                        break;
                    default:
                        picon = <></>
                        break;
                }
            }
            setIcon(picon);
        }
    }, [item, type]);

    return <>{icon}</>;
}
