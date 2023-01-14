import * as React from 'react';
import { useState, useEffect } from 'react';
import { userDataRequest } from '../../Request/UserDataRequest';

import GroupNameNavbarResponseInterface from '../../Response/User/Navbar/Interfaces/GroupNameNavbarResponseInterface';
import RoomNavbarResponseInterfaceInterface from '../../Response/User/Navbar/Interfaces/RoomNavbarResponseInterface';
import { UserDataResponseInterface } from '../../Response/User/Navbar/UserDataResponseInterface';

import UserDataContext from "../../Contexts/UserData/UserDataContext";

export function UserDataContextProvider({ children }) {
    // const [userGroups, setUserGroups] = useState<RoomNavbarResponseInterfaceInterface[]|[]>([]);

    const [userData, setUserData] = useState<UserDataContextInterface>({userGroups: [], userRooms: []})
    // const [userRooms, setUserRooms] = useState<GroupNameNavbarResponseInterface[]|[]>([]);

    useEffect(() => {
        // if (userData.userGroups.length)
        handleUserDataRequest();
    }, [userData]);

    const handleUserDataRequest = async () => {
        console.log('handleUserDataRequest');
        if (userData.userGroups.length === 0 || userData.userRooms.length === 0) {             
            const userDataResponse = await userDataRequest();
            const userDataPayload = userDataResponse.data.payload as UserDataResponseInterface;
            console.log(
                'UDRES',
                //  userDataResponse,
                  userDataPayload.userGroups,
                   userDataPayload.userRooms,
                   userDataResponse.status
                   )
            if (userDataResponse.status === 200) {
                setUserData({userDataPayload});
                console.log('this is user Data', userData);

                console.log('hi222', userDataPayload.userGroups);
                console.log('hi22233', userDataPayload.userRooms);
            } 
        }
    }


    return (
        <UserDataContext.Provider value={
            {
                userData
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