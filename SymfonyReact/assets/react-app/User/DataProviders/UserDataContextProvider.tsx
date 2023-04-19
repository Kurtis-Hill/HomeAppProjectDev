import * as React from 'react';
import { useState, useEffect } from 'react';
import { userDataRequest } from '../Request/UserDataRequest';

import GroupNameResponseInterface from '../Response/GroupName/GroupNameResponseInterface';
import RoomNavbarResponseInterface from '../../UserInterface/Navbar/Response/RoomNavbarResponseInterface';
import { UserDataResponseInterface } from '../Response/UserDataResponseInterface';

import UserDataContext from "../Contexts/UserDataContext";

export function UserDataContextProvider({ children }) {
    // const [userGroups, setUserGroups] = useState<RoomNavbarResponseInterfaceInterface[]|[]>([]);
    const [userData, setUserData] = useState<UserDataContextInterface>({ userGroups: [], userRooms: [] })
    const [refreshUserData, setRefreshUserData] = useState<boolean>(true);
    // const [userRooms, setUserRooms] = useState<GroupNameNavbarResponseInterface[]|[]>([]);

    useEffect(() => {
        if (refreshUserData === true) {
            handleUserDataRequest();
            setRefreshUserData(false);
        }
    }, [refreshUserData]);

    const handleUserDataRequest = async () => {
        console.log('handleUserDataRequest');
        if (userData.userGroups.length === 0 || userData.userRooms.length === 0) {
            const userDataResponse = await userDataRequest();
            if (userDataResponse.status === 200) {
                const userDataPayload = userDataResponse.data.payload as UserDataResponseInterface;

                setUserData({ userGroups: userDataPayload.userGroups, userRooms: userDataPayload.userRooms });
                console.log('this is user Data', userData);
                setUserData({ userGroups: userDataPayload.userGroups, userRooms: userDataPayload.userRooms });
            }
        }
    }

    const refreshAllUserData = (trigger: boolean) => {
        console.log('refresh is being triggeredddd')
        setRefreshUserData(true);
    }


    return (
        <UserDataContext.Provider value={
            {
                userData,
                // setRefreshUserData: (value: boolean) => refreshAllUserData(value)
                setRefreshUserData,
                refreshAllUserData
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