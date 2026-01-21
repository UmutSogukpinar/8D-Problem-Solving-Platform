import React, { createContext, useContext, useState, useEffect } from "react";
import { apiFetch } from "../api/client";

const UserContext = createContext(null);

export function UserProvider({ children }) 
{
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        let cancelled = false;

        async function loadMe() 
        {
            try 
            {
                setLoading(true);
                const me = await apiFetch("/8d/me");
                if (!cancelled)
                    setUser(me);
            }
            catch (error) 
            {
                console.error("User fetch failed", error);

                if (!cancelled)
                    setUser(null);
            }
            finally 
            {
                if (!cancelled)
                    setLoading(false);
            }
        }

        loadMe();

        return () => {
            cancelled = true;
        };

    }, []);

    return (
        <UserContext.Provider value={{ user, loading, setUser }}>
            {children}
        </UserContext.Provider>
    );
}

export function useUser()
{
    return (useContext(UserContext));
}