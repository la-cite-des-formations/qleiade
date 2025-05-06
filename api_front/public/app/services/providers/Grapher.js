import React from "react";
import { ReactFlowProvider } from "reactflow";


export default function Grapher({ children }) {
    return (
        <ReactFlowProvider>
            {children}
        </ReactFlowProvider>
    )
}
