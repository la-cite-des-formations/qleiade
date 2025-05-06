import React from "react";
import { createBrowserRouter } from "react-router-dom";

//views
import Home from "@views/pages/home";
import ErrorPage from "@views/error-page";
import { SignIn } from "@views/pages";
import { Audit, Result, Presenter as LabelPresenter, OverView as LabelsOverview } from "@views/pages/qualityLabels";
// import { OverView as BoardsOverView, Board } from "@views/pages/Boards";
import { Board } from "@views/pages/Boards";

import { Auth, GoogleCallBack } from "@services/middleWear";


export default createBrowserRouter([
    {
        path: "/login",
        element: <SignIn />,
        errorElement: <ErrorPage />,
    },
    {
        path: "/auth",
        element: <GoogleCallBack />,
        errorElement: <ErrorPage />,
    },
    {
        path: "/",
        element: <Auth />,
        children: [
            {
                index: true,
                path: "home",
                element: <Home />,
                errorElement: <ErrorPage />,
            },
            {
                path: "labels",
                element: <LabelsOverview />,
                errorElement: <ErrorPage />,
            },

            {
                path: "labels/:label",
                element: <LabelPresenter />,
                errorElement: <ErrorPage />,
            },
            {
                path: "labels/:label/audit",
                element: <Audit />,
                errorElement: <ErrorPage />,
            },
            {
                path: "labels/:label/audit/result",
                element: <Result />,
                errorElement: <ErrorPage />,
            },
            // {
            //     path: "labels/:label/dashboard",
            //     element: <LabelDashboard />,
            //     errorElement: <ErrorPage />,
            // },
            // {
            //     path: "dashboards",
            //     element: <BoardsOverView />,
            //     errorElement: <ErrorPage />,
            // },
            // {
            //     path: "dashboards/:type",
            //     // element: <Unit />,
            //     errorElement: <ErrorPage />,
            // },
            {
                path: "dashboards/:type/:slug",
                element: <Board />,
                errorElement: <ErrorPage />,
            },
        ],
        errorElement: <ErrorPage />,
    },
]);
