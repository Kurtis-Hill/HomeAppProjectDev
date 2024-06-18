import * as React from 'react';
// import { useParams } from "react-router-dom";
import { UserUpdateForm } from './UserUpdateForm';
import {getUserSessionValue} from "../../../Authentication/Session/UserSessionHelper";

export function UserSettingsView() {
    // const params = useParams();

    const userID: number = parseInt(getUserSessionValue('userID'));

    return (
        <>
            <UserUpdateForm userID={userID} />
        </>
    )
}
