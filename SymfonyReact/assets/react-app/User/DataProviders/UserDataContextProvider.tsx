import * as React from 'react';
import { useState, useEffect, useRef, useMemo, useReducer } from 'react';
import { userDataRequest } from '../Request/UserDataRequest';

import GroupNameResponseInterface from '../Response/GroupName/GroupNameResponseInterface';
import RoomNavbarResponseInterface from '../../UserInterface/Navbar/Response/RoomNavbarResponseInterface';
import { UserDataResponseInterface } from '../Response/UserDataResponseInterface';

import UserDataContext from "../Contexts/UserDataContext";

export function UserDataContextProvider({ children }) {
    // const [userGroups, setUserGroups] = useState<RoomNavbarResponseInterfaceInterface[]|[]>([]);
    // const [userData, setUserData] = useState<UserDataContextInterface>({ userGroups: [], userRooms: [] })
    const userData = useRef<UserDataContextInterface>({ userGroups: [], userRooms: [] })
    // const userData = useMemo<UserDataContextInterface>(() => handleUserDataRequest(), []);
    // const [refreshUserData, setRefreshUserData] = useState<boolean>(true);

    // const [userRooms, setUserRooms] = useState<GroupNameNavbarResponseInterface[]|[]>([]);

    useEffect(() => {
        // if (refreshUserData === true) {
            handleUserDataRequest();
            // setRefreshUserData(false);
        // }
    });

    // function handleRequest() {
    //     handleUserDataRequest();
    // }
    
    const handleUserDataRequest = async () => {
        console.log('handleUserDataRequest');
        // if (userData.userGroups.length === 0 || userData.userRooms.length === 0) {
            const userDataResponse = await userDataRequest();
            if (userDataResponse.status === 200) {
                const userDataPayload = userDataResponse.data.payload as UserDataResponseInterface;

                console.log('payload', userDataPayload)
                userData.current = {
                    userGroups: userDataPayload.userGroups,
                    userRooms: userDataPayload.userRooms
                }
                // setUserData({ userGroups: userDataPayload.userGroups, userRooms: userDataPayload.userRooms });
                console.log('this is user Data', userData);
                // setUserData({ userGroups: userDataPayload.userGroups, userRooms: userDataPayload.userRooms });
            }
        // }
    }

    const refreshAllUserData = (trigger?: boolean) => {
        console.log('refresh is being triggeredddd')
        // if (refreshUserData === false) {
        //     setRefreshUserData(true);
        // }
        // setRefreshUserData(true);
        // setRefreshUserData(refreshUserData => !refreshUserData);
        // handleUserDataRequest();
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
    userGroups: GroupNameResponseInterface[]|[];
    userRooms: RoomNavbarResponseInterface[]|[];
}