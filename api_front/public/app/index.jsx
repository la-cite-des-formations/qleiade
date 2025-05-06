import React from "react";
import ReactDOM from "react-dom";
import { Sanctum } from "react-sanctum";
import Main from "@views/Main";
import API from "@services/axios";
import Toaster from "@services/providers/Toaster";
import Translator from "@services/providers/Translator";

const sanctumConfig = {
    apiUrl: process.env.API_BASE_URL,
    csrfCookieRoute: process.env.FRONT_SANCTUM_CONFIG_CSRF_COOKIE_ROUTE,
    signInRoute: process.env.FRONT_SANCTUM_CONFIG_SIGNIN_ROUTE,
    signOutRoute: process.env.FRONT_SANCTUM_CONFIG_SIGNOUT_ROUTE,
    userObjectRoute: process.env.FRONT_SANCTUM_CONFIG_USER_ROUTE,
    axiosInstance: API,
};

const root = document.getElementById('main');

const app = <React.StrictMode>
    <Sanctum config={sanctumConfig} checkOnInit={false}>
        <Translator>
            <Toaster>
                <Main />
            </Toaster>
        </Translator>
    </Sanctum>
</React.StrictMode>;

ReactDOM.render(app, root);
