import React, { useEffect, useContext } from "react";
import { Navigate, useLocation, useParams } from "react-router-dom";
import { ResultProvider } from "@parts/qualitylabel/result/Context";
import Layout from "@parts/qualitylabel/result/Layout";
import { AuthContext } from '@services/middleWear/auth/context';


export default function result(props) {
    const { label } = useParams();
    const { state } = useLocation();
    const { canSee } = useContext(AuthContext);

    useEffect(() => {
    }, [])

    return (
        <>
            {
                canSee('public_quality_labels_audit') ?
                    <ResultProvider>
                        <Layout label={label} state={state} />
                    </ResultProvider>
                    :
                    <Navigate to={"/home"} />
            }

        </>
    );
}
