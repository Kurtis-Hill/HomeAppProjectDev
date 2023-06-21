import * as React from 'react';
import { Link, useParams } from "react-router-dom";
import { getUserSessionValue } from '../../Authentication/Session/UserSession';
import { UserUpdate } from '../Components/User/UserUpdate';

export function UserSettingsPage() {
    const params = useParams();

    const userID: number = parseInt(getUserSessionValue('userID'));

    return (
        <>
            <UserUpdate userID={userID} />
        </>
    )
}
