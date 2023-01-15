import * as React from 'react';
import { useState, useEffect } from 'react';
import { userDataRequest } from '../../Request/UserDataRequest';

import GroupNameNavbarResponseInterface from '../../Response/User/Navbar/Interfaces/GroupNameNavbarResponseInterface';
import RoomNavbarResponseInterface from '../../Response/User/Navbar/Interfaces/RoomNavbarResponseInterface';
import { UserDataResponseInterface } from '../../Response/User/Navbar/UserDataResponseInterface';

import UserDataContext from "../../Contexts/UserData/UserDataContext";

export function UserDataContextProvider({ children }) {
    // const [userGroups, setUserGroups] = useState<RoomNavbarResponseInterfaceInterface[]|[]>([]);
    console.log('user data stuff updating too')
    const [userData, setUserData] = useState<UserDataContextInterface>({ userGroups: [], userRooms: [] })
    // const [userRooms, setUserRooms] = useState<GroupNameNavbarResponseInterface[]|[]>([]);

    useEffect(() => {
        // if (userData.userGroups.length)
        handleUserDataRequest();
    }, []);

    const handleUserDataRequest = async () => {
        console.log('handleUserDataRequest');
        if (userData.userGroups.length === 0 || userData.userRooms.length === 0) {
            const userDataResponse = await userDataRequest();
            if (userDataResponse.status === 200) {
                const userDataPayload = userDataResponse.data.payload as UserDataResponseInterface;
                console.log(
                    'UDRES',
                    //  userDataResponse,
                    userDataPayload.userGroups,
                    userDataPayload.userRooms,
                    userDataResponse.status
                )
                setUserData({ userGroups: userDataPayload.userGroups, userRooms: userDataPayload.userRooms });
                console.log('this is user Data', userData);
                setUserData({ userGroups: userDataPayload.userGroups, userRooms: userDataPayload.userRooms });
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
            {children}
        </UserDataContext.Provider>
    );
}

export interface UserDataContextInterface {
    userGroups: GroupNameNavbarResponseInterface[]|[];
    userRooms: RoomNavbarResponseInterface[] | [];
}