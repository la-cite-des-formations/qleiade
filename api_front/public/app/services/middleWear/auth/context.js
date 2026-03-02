import * as React from 'react';

export const AuthContext = React.createContext({
    canSee: () => { },
    getUnit: () => { }
});

export function AuthProvider({ children, user }) {
    const authValue = React.useMemo(
        () => ({
            canSee: (key) => {
                return user && user.permissions && Boolean(user.permissions[key]);
            },
            getUnit: (key) => {
                if (user) {
                    return user.unit;
                }
                return [];

            },
        }),
        [user]
    );

    return (
        <AuthContext.Provider value={authValue}>
            {children}
        </AuthContext.Provider>
    )
}
