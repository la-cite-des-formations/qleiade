import React, { useState, useEffect } from "react";
import TaskAltIcon from '@mui/icons-material/TaskAlt';

export default function ValidationIcon(props) {
    const { item, handlers, validated } = props;
    //warning item mutation
    const [icon, setIcon] = useState(<div />);
    const [valide, setValide] = useState(validated)//remove make a component

    function validate(event) {
        event.stopPropagation();
        handlers.validateItem(item);
        setValide(!validated);
    }

    useEffect(() => {
        if (valide) {
            setIcon(<TaskAltIcon color="success" onClick={validate} />);
        } else {
            setIcon(<TaskAltIcon color="disabled" onClick={validate} />)
        }
    }, [item, validated])

    return <>{icon}</>
}
