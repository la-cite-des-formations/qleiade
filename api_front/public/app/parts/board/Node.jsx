import React, { useEffect, useState, lazy, Suspense } from 'react';
import { useStoreState, useStoreActions, useStore, Handle, Position, useUpdateNodeInternals } from 'reactflow';
import { Box, Card } from '@mui/material';
// import Action from './Action';
// import Indicator from './Indicator';
// import Wealth from './Wealth';
// import { getComponent } from '@services/utils';

function getComponent(name, properties, canSee) {
    //pass absolute path to resource
    var compo = "";
    switch (name) {
        case 'Action':
            compo = "Action"
            break;
        case 'Wealth':
            compo = "Wealth"
            break;
        case 'Indicator':
            compo = "Indicator"
            break;

        default:
            break;
    }
    var Component = lazy(() => import("@parts/board/" + compo));
    var el = <Suspense fallback={<div>Hi, This component is Loading...</div>}>
        <Component item={properties} canSee={canSee} />
    </Suspense>;

    return el;
}

const Node = (props) => {
    const { id, data, selected } = props;
    // const selectedElements = useStoreState((state) => state.selectedElements);
    // const setSelectedElements = useStoreActions((actions) => actions.setSelectedElements);
    const [ports, setPorts] = useState([]);
    const [component, setComponent] = useState(<></>);
    const updateNodeInternals = useUpdateNodeInternals();
    const node = useStore((s) => {
        return s.nodeInternals.get(id);
    })

    const makePorts = (data) => {
        var ports = [];
        if (data.ports?.length > 0) {
            data.ports.forEach((port, index) => {
                const handleStyle = { top: (node.height / (data.ports.length + 1)) * (index + 1) };
                ports.push(<Handle key={"port-" + index + "-" + port.type + "-" + port.id} type={port.type} id={port.id} position={Position[port.position]} style={handleStyle} isConnectable={port.isConnectable} />)
            });
        }
        return ports
    }

    useEffect(() => {
        const ps = makePorts(data)
        setPorts(ps);
        data.item.size = size;
        const comp = getComponent(data.item.type, { ...data.item, "relations": data.relations }, true);
        setComponent(comp)

    }, [data])

    useEffect(() => {
        updateNodeInternals(id);
    }, [ports])

    const handleClick = () => {
        // Vérifie si l'élément actuel est déjà sélectionné
        // const isSelected = selectedElements.some((element) => element.id === id);
        if (selected) {
            // L'élément est déjà sélectionné, désélectionne-le
            // setSelectedElements([]);
        } else {
            // L'élément n'est pas sélectionné, sélectionne-le et active l'animation du bord
            // setSelectedElements([{ id, type: 'edge' }]);
        }
    };
    const size = useStore((s) => {
        const node = s.nodeInternals.get(id);

        return {
            width: node.width,
            height: node.height,
        };
    });

    return (
        <Card onClick={handleClick} className="node-container" sx={{ borderColor: selected ? 'red' : 'black' }}>
            {component}
            {/* Rendu de votre node personnalisée */}
            {/* {ports.length > 0 ? ports :
                <div>{`vous n'avez pas de preuves renseigné pour cette ${data.item.type}`}</div>} */}
            {ports}
        </Card>
    );
};

export default Node;
