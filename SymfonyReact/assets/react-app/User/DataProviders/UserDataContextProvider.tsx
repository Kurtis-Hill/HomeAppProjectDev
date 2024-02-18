import * as React from 'react';
import { useState, useEffect, useRef, useMemo, useReducer } from 'react';
import { userDataRequest } from '../Request/UserDataRequest';

import GroupResponseInterface from '../Response/Group/GroupResponseInterface';
import RoomNavbarResponseInterface from '../../UserInterface/Navbar/Response/RoomNavbarResponseInterface';
import { UserDataResponseInterface } from '../../UserInterface/Navbar/Response/UserDataResponseInterface';

import UserDataContext from "../Contexts/UserDataContext";

export function UserDataContextProvider({ children }) {
    const userData = useRef<UserDataContextInterface>({ userGroups: [], userRooms: [] })

    useEffect(() => {
        // if (refreshUserData === true) {
            handleUserDataRequest();
            // setRefreshUserData(false);
        // }
    }, []);

    const handleUserDataRequest = async () => {
        const userDataResponse = await userDataRequest();
        if (userDataResponse.status === 200) {
            const userDataPayload = userDataResponse.data.payload as UserDataResponseInterface;

            userData.current = {
                userGroups: userDataPayload.userGroups,
                userRooms: userDataPayload.userRooms
            }
        }
    }


    return (
        <UserDataContext.Provider value={
            {
                userData: userData.current,
                // forceUpdate,
                // userData,
                // setUserData,
                // setRefreshUserData,
                // setRefreshUserData: (value: boolean) => refreshAllUserData(value),
                // setRefreshUserData,
                // refreshAllUserData,
                // handleUserDataRequest: handleRequest
            }
        }
        >
            {children}
        </UserDataContext.Provider>
    );
}

export interface UserDataContextInterface {
    userGroups: GroupResponseInterface[]|[];
    userRooms: RoomNavbarResponseInterface[]|[];
}