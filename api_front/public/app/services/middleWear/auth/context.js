import * as React from 'react';

export const AuthContext = React.createContext({
    canSee: () => { },
    getUnit: () => { }
});

export function AuthProvider({ children, user }) {
    const authValue = React.useMemo(
        () => ({
            canSee: (key) => {
                let can = false;
                if (user.permissions) {
                    user.permissions.hasOwnProperty(key) ? can = Boolean(Number(user.permissions[key])) : can = false;
                }
                return can;
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
