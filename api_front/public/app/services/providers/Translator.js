import React from "react";
import i18next from "i18next";
import { I18nextProvider } from "react-i18next";
import common_fr from "@services/translations/fr/common.json";

i18next.init({
    interpolation: { escapeValue: false },  // React already does escaping
    lng: 'fr',                              // language to use
    resources: {
        fr: {
            common: common_fr               // 'common' is our custom namespace
        }
    },
});

export default function Translator({ children }) {
    return (
        <I18nextProvider i18n={i18next}>
            {children}
        </I18nextProvider>
    )
}
