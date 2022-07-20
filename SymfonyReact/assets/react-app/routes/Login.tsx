import { useState, useEffect,  } from 'react';
import * as React from 'react';
import * as ReactDOM from "react-dom/client";
import {
    BrowserRouter,
    Routes,
    Route,
} from "react-router-dom";
import axios from 'axios';

import SubmitButton from "../components/buttons/submit-button";
import Input from "../components/form/input";
import Page from "../components/pages/page";

import { webappURL, apiURL } from '../common/commonURLs';
import { setUserSession } from "../session/user-session";

export default function Login(props) {
    const [userInputs, setUserInputs] = useState({});
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);

    const handleInput = (event) => {
        const name = event.target.name;
        const value = event.target.value;
        setUserInputs(values => ({...values, [name]: value}))
    }

    const handleLoginRequest = async () => {
        setError(null);
        setLoading(true);

        try {
            const loginCheckResponse = await axios.post(
                `${apiURL}login_check`,
                userInputs
            );
            if (loginCheckResponse.status === 200) {
                setUserSession(
                    loginCheckResponse.data.token,
                    loginCheckResponse.data.refreshToken,
                    loginCheckResponse.data.userData,
                );
                // window.location.replace(`${webappURL}/index`);
            } else {
                setError('Login check response error')
            }
        } catch (error) {
            const errorResponse = error.response;
            setLoading(false);
            console.log(error, errorResponse.status)
            if (errorResponse.status === 401) {
                setError(errorResponse.data.message);
            } else {
                setError('Something went wrong');
            }
        }

    }


    return (
        <React.Fragment>
            <Page content="

            " />

            {/*<Input*/}
            {/*    name="username"*/}
            {/*    onChangeFunction={handleInput}*/}
            {/*/>*/}
            {/*<Input*/}
            {/*    name="password"*/}
            {/*    type="password"*/}
            {/*    onChangeFunction={handleInput}*/}
            {/*/>*/}

            {/*<SubmitButton*/}
            {/*    onClickFunction={handleLoginRequest}*/}
            {/*/>*/}
        </React.Fragment>
    );
}


const useFormInput = initialValue => {
    // const [value, setValue] = useState(initialValue);
    //
    // const handleChange = e => {
    //     setValue(e.target.value);
    // }
    //
    // return {
    //     value,
    //     onChange: handleChange
    // }

}
