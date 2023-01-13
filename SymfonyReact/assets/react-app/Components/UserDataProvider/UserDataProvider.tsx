import * as React from 'react';
import { useState, useEffect } from 'react';
import { userDataRequest } from '../../Request/UserDataRequest';

import GroupNameNavbarResponseInterface from '../../Response/User/Navbar/Interfaces/GroupNameNavbarResponseInterface';
import RoomNavbarResponseInterfaceInterface from '../../Response/User/Navbar/Interfaces/RoomNavbarResponseInterface';
import { UserDataResponseInterface } from '../../Response/User/Navbar/UserDataResponseInterface';

import UserDataContext from "../../Contexts/UserData/UserDataContext"
export function UserDataProvider({ children }) {
    const [userGroups, setUserGroups] = useState<RoomNavbarResponseInterfaceInterface[]|[]>([]);

    const [userRooms, setUserRooms] = useState<GroupNameNavbarResponseInterface[]|[]>([]);

    useEffect(() => {
        handleUserDataRequest();
    }, []);

    const handleUserDataRequest = async () => {
        console.log('handleUserDataRequest');
        if (userGroups.length === 0 || userRooms.length === 0) {             
            const userGroupsResponse = await userDataRequest();
            const userData = userGroupsResponse.data.payload as UserDataResponseInterface;
            if (userGroupsResponse.status == 200) {
                setUserGroups(userData.userGroups);
                setUserRooms(userData.userRooms);
            } 
        }
    }

    return (
        <UserDataContext.Provider value={
            {
                userGroups,
                userRooms,
            }
        }
        >
            { children }
        </UserDataContext.Provider>
    );
}

export interface UserDataContextInterface {
    userGroups: GroupNameNavbarResponseInterface[]|[];
    userRooms: RoomNavbarResponseInterfaceInterface[]|[];
}