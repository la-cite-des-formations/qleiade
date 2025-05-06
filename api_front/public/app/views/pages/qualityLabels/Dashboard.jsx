
import React, { useContext, useEffect, useState } from 'react';
import Tiles from '@mypackages/react-tiles-layout-factory/Tiles';
import API from '@services/axios';
import { Navigate, useParams } from 'react-router-dom';
import { AuthContext } from '@services/middleWear/auth/context';

export default function Dashboard() {
    const { label } = useParams();
    const { type } = useParams();
    const [qualityLabel, setQualitylabel] = useState([]);
    const { canSee } = useContext(AuthContext);

    useEffect(() => { }, [label]);

    return (
        <>
            {
                canSee("public_quality_labels_dashboard") ?
                    <main className="py-4 bg-grey">
                        {"dashboard for " + label + "   add link to internal audit"}
                    </main>
                    :
                    <Navigate to={"/home"} />
            }
        </>
    );
};
