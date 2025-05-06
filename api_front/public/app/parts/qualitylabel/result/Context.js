
import React, { createContext, useCallback, useMemo, useReducer } from "react";

const initialValues = {
    audit: {
        summary: null,
        comment: "",
        validatedWealths: [],
        id: null
    },
    wealths: {
        global: [],
        formation: [],
        group: [],
        student: [],
        current: []
    },
    wrapper: {
        init: [],
        current: []
    },
    filters_selected: {
        indicator: [],
        group: [],
        student: [],
        formation: [],
        period: [],
    },
    filters_options: {
        indicator: [],
    },
    validation: {
        errors: {}
    }
};

export const ResultContext = createContext({
    resultValues: initialValues,
    handleChange() { },
});

// let deepFind = (myarray, keyPath, keyValue) => _.find(myarray, _.matchesProperty(keyPath, keyValue));

function reducer(state, action) {
    switch (action.type) {
        case "SET":
            return {
                ...state,
                resultValues: {
                    ...state.resultValues,
                    [action.name]: {
                        ...state.resultValues[action.name],
                        [action.who]: action.value
                    }
                }
            };
            break;
        case "ADD":
            const has = state.resultValues[action.name][action.who].filter((item) => { return item.id === action.value.id; }).length === 0;
            return {
                ...state,
                resultValues: {
                    ...state.resultValues,
                    [action.name]: {
                        ...state.resultValues[action.name],
                        // [action.who]: state.resultValues[action.name][action.who].push(action.value)
                        [action.who]: has ? state.resultValues[action.name][action.who].concat(action.value) : state.resultValues[action.name][action.who]
                    }
                }
            };
            break;

        case "DELETE_ONE":
            return {
                ...state,
                resultValues: {
                    ...state.resultValues,
                    [action.name]: {
                        ...state.resultValues[action.name],
                        [action.who]: state.resultValues[action.name][action.who].filter((item) => {
                            return item.id !== action.value.id;
                        })
                    }
                }
            };
            break;
        case "DELETE_ALL":
            return {
                ...state,
                resultValues: {
                    ...state.resultValues,
                    [action.name]: {
                        ...state.resultValues[action.name],
                        [action.who]: []
                    }
                }
            };
            break;

        default:

            break;
    }
    return state;
}

export function ResultProvider({ children }) {
    const [{ resultValues }, dispatch] = useReducer(reducer, {
        resultValues: initialValues
    });

    // Handle result change
    const handleChange = useCallback(
        (
            params
        ) => {
            const { type, who, name, value } = params;

            dispatch({ type, who, name, value });
        },
        []
    );

    const contextValue = useMemo(
        () => ({
            resultValues,
            handleChange,
        }),
        [resultValues, handleChange]
    );

    return (
        <ResultContext.Provider value={contextValue}>
            {children}
        </ResultContext.Provider>
    );
}
