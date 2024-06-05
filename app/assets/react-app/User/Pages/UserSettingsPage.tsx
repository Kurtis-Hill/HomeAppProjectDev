import * as React from 'react';
import { Link, useParams } from "react-router-dom";
import { UserUpdate } from '../Components/User/UserUpdate';
import {getUserSessionValue} from "../../Authentication/Session/UserSessionHelper";

export function UserSettingsPage() {
    const params = useParams();

    const userID: number = parseInt(getUserSessionValue('userID'));

    return (
        <>
            <UserUpdate userID={userID} />
        </>
    )
}
