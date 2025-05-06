import React, { useEffect, useState } from "react";
import { Chip } from "@mui/material";
import { green, blue, yellow, purple, red } from '@mui/material/colors';

const colors = (key) => {
    const c = {
        global: purple[300],
        formation: green[300],
        group: blue[300],
        student: yellow[300],
    }
    return c[key];
};

export default function Counter({ counter }) {
    const [tags, setTags] = useState([]);

    useEffect(() => {
        const chips = [];
        for (const [key, value] of Object.entries(counter || {})) {
            if (key !== 'undefined') {
                const color = colors(key) || red[50]; // si la clé n'est pas trouvée, définir la couleur sur rouge
                chips.push(<Chip variant="outlined" key={key} label={value > 0 ? `${key}: ${value}` : `${key}`} style={{ backgroundColor: color }} />);
            }
        }
        setTags(chips);
    }, [counter]);

    return (
        <div>
            {tags}
        </div>
    );
}
