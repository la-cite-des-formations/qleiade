// import { initialNodes, initialEdges } from './nodes-edges.js';
import elk from '@services/elk';
import Grapher from '@services/providers/Grapher';
import React, { useEffect } from 'react';
import ReactFlow, {
    useNodesState,
    useEdgesState,
    useReactFlow,
    MiniMap,
    Controls,
    Panel
} from 'reactflow';
import 'reactflow/dist/style.css';
import Node from './Node';


function applyLayout(layoutedGraph) {
    var layoutedNodes = [];
    const layoutedEdges = layoutedGraph.edges;

    layoutedGraph.children.forEach((node) => {
        layoutedNodes.push({
            ...node,
            // React Flow expects a position property on the node instead of `x` and `y` fields.
            position: { x: node.x, y: node.y },
        });
    });
    return {
        layoutedNodes: layoutedNodes,
        layoutedEdges: layoutedEdges,
    }

}

// Elk has a *huge* amount of options to configure. To see everything you can
// tweak check out:
//
// - https://www.eclipse.org/elk/reference/algorithms.html
// - https://www.eclipse.org/elk/reference/options.html
const getLayoutedElements = (nodes, edges) => {
    //layout description
    const graph = {
        id: 'root',
        layoutOptions: {
            'elk.algorithm': 'layered',
            'elk.layered.spacing.nodeNodeBetweenLayers': 200,//espacement entre les colonnes
            'elk.crossingMinimization.semiInteractive': true,
            'elk.nodePlacement.strategy': 'NETWORK_SIMPLEX',
            'elk.spacing.nodeNode': 25, //espacement entre les nodes
            'elk.spacing.nodeNodeBetweenLayers': 50,
            'elk.spacing.edgeNode': 25,
            'elk.spacing.edgeNodeBetweenLayers': 40,
            'elk.spacing.edgeEdge': 20,
            'elk.spacing.edgeEdgeBetweenLayers': 15
        },
        children: nodes.map((node) => {
            return {
                ...node,
                // Adjust the target and source handle positions based on the layout direction.
                // Hardcode a width and height for ELK to use when layouting.
                width: 300,
                height: 100,
            };
        }),
        edges: edges,
    };

    //complete node with new calculated position
    return elk
        .layout(graph)
        .then((layoutedGraph) => {
            const { layoutedNodes, layoutedEdges } = applyLayout(layoutedGraph);

            return {
                nodes: layoutedNodes,
                edges: layoutedEdges,
            };
        })
        .catch(console.error);
};

//reactFlow attr , doit être en dehors du composant
const nodeTypes = {
    Wealth: Node,
    Action: Node,
    Indicator: Node,
}
const proOptions = { hideAttribution: true };
const defaultViewport = { x: 0, y: 0, zoom: 0.75 };


function LayoutFlow(props) {
    const { items } = props;
    const [nodes, setNodes] = useNodesState([]);
    const [edges, setEdges] = useEdgesState([]);
    const { fitView } = useReactFlow();

    useEffect(() => {
        getLayoutedElements(items.nodes ?? [], items.edges ?? []).then(({ nodes: layoutedNodes, edges: layoutedEdges }) => {
            setNodes(layoutedNodes);
            setEdges(layoutedEdges);

            // window.requestAnimationFrame(() => fitView());
        });

    }, [items])

    return (
        <ReactFlow
            nodes={nodes}
            edges={edges}
            fitView={nodes.length < 10 ? true : false}
            // fitView
            nodeTypes={nodeTypes}
            proOptions={proOptions}
            elevateEdgesOnSelect={true}
            defaultViewport={defaultViewport}
            panOnScroll={true}
        >
            <Controls
                showInteractive={false}
                position={'top-right'} />
            <MiniMap
            // position='bottom-left'
            />
        </ReactFlow>
    );
}

export default function Graph(props) {
    const { items } = props;
    return (
        <Grapher>
            <LayoutFlow items={items} />
        </Grapher>
    );
};

