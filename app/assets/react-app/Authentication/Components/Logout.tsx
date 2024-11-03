import * as React from 'react';
import { NavigateFunction, useNavigate } from "react-router-dom";
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';
import { useEffect } from 'react';
import { loginUrl } from '../../Common/URLs/CommonURLs';
import {removeUserSession} from "../Session/UserSessionHelper";


export function Logout() {
    const navigate: NavigateFunction = useNavigate();

    useEffect(() => {
        removeUserSession();
        navigate(loginUrl);
    });


    return (
        <DotCircleSpinner classes='center-spinner' />
    );
}
