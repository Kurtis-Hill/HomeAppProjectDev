import * as React from 'react';
import { useState, useEffect } from 'react';
import { Link, NavigateFunction, useNavigate } from "react-router-dom";

import axios, { AxiosError, AxiosResponse } from 'axios';

import { registerAccountUrl, indexUrl, loginUrl } from "../../Common/CommonURLs";
import { getRefreshToken, getToken } from "../../Common/APICommon";

import SubmitButton from "../../Components/Buttons/SubmitButton";
import Input from "../../Components/Form/Inputs/Input";
import ColouredPage from "../../Components/Pages/ColouredPage";
import DotCircleSpinner from "../../Components/Spinners/DotCircleSpinner";

import { LoginFormUserInputsInterface } from "../../Components/Form/UserInputs/Interface/LoginFormUserInputsInterface";

import { handleLogin, handleTokenRefresh } from "../../Request/LoginRequest";
import { handlePingRequest, PingInterface } from "../../Request/Ping";

export default function Login(): void {
    const [userInputs, setUserInputs] = useState<LoginFormUserInputsInterface>({});
    const [error, setError] = useState<Array<string>>([]);
    const [loading, setLoading] = useState(false);
    const [pingResult, setPingResult] = useState<boolean>(true);

    const loginPageImage: string = require('../../../images/login/index-photo.jpg')

    const navigate: NavigateFunction = useNavigate();

    useEffect(() => {
        checkCurrentToken();
    })

    const handleInput = (event: { target: { name: string; value: string; }; }) => {
        const name: string = event.target.name;
        const value: string = event.target.value;
        setUserInputs((values: LoginFormUserInputsInterface) => ({...values, [name]: value}))
    }

    const validateUserInput = (): boolean => {
        if (userInputs.username === undefined || userInputs.username === "") {
            setError(['Please fill in username']);
            setLoading(false);
            return false;
        }
        if (userInputs.password === "" || userInputs.password === undefined) {
            setError(['Please fill in password']);
            setLoading(false);
            return false;
        }
        
        return true;
    }

    const checkCurrentToken = async () => {
        const token = getToken();
        if (token !== null) {
            try {
                const pingRequest: PingInterface = await handlePingRequest();
                if (pingRequest.status !== 200) {
                }
                navigate(`${indexUrl}`);
            } catch (err) {
                const error = err as Error | AxiosError;
                const refreshTokenResponse: AxiosResponse = await handleTokenRefresh();
                if (refreshTokenResponse !== undefined && "status" in refreshTokenResponse) {
                    if (refreshTokenResponse.status === 200) {
                        navigate(`${indexUrl}`);
                    }
                }
            }
        }
        const refreshToken = getRefreshToken();
        if (refreshToken !== null) {
            try {
                const refreshTokenResponse: AxiosResponse = await handleTokenRefresh();
                if (refreshTokenResponse.status === 200) {
                    navigate(`${indexUrl}`);
                }
            } catch (err) {
                const error = err as Error | AxiosError;
            }
        }
        setPingResult(false);
    }
                

    /**
     * @throws Error
     */
    const handleLoginRequest = async (event: { preventDefault: () => void; }) => {
        event.preventDefault();
        setError([]);
        setLoading(true);

        const validationPassed = validateUserInput();
        if (validationPassed === false) {
            throw new Error(`User input validation failed`);
        }
        try {
            const loginResponse: AxiosResponse = await handleLogin(userInputs);

            if (loginResponse.status === 200) {
                navigate(`${indexUrl}`)
            } else {
                setError(['Login failed. Please try again.']);
                setLoading(false);
            }
        } catch (err) {
            const error = err as Error|AxiosError;
            if(!axios.isAxiosError(error)) {
                alert(`Something went wrong, please try refresh the browser ${error.message}`);
            } 
            if (axios.isAxiosError(error)) {
                const errorResponse = error.response;

                setLoading(false);
                if (errorResponse.status === 401) {
                    setError([errorResponse.statusText]);
                } else {
                    setError(['Something went wrong']);
                }
            }
        }
    }

    const loginPageContent = () => {
        return (
            <React.Fragment>
                <div className="card o-hidden border-0 shadow-lg my-5">
                    <div className="card-body p-0">
                        <div className="row">
                            <img src={ loginPageImage } className="col-lg-6 d-none d-lg-block"  alt="login-page-image" />
                            <div className="col-lg-6">
                                <div className="login-form">
                                    <div className="text-center">
                                        <h1 className="login-form-container h2 text-gray-700 mb-4 login-banner">Welcome Back To The Home-App!</h1>
                                    </div>
                                    {error.length > 0 ? <h2 className="text-center">{error}</h2> : null}
                                    <form className="user" id="login-form">
                                        <Input
                                            name="username"
                                            onChangeFunction={handleInput}
                                            extraClasses={"login-form-field"}
                                        />
                                        <Input
                                            name="password"
                                            type="password"
                                            onChangeFunction={handleInput}
                                            extraClasses={"login-form-field"}
                                        />
                                        <div>
                                            {loading === true
                                                ? <DotCircleSpinner spinnerSize={2} classes="center-spinner" />
                                                : null}
                                            <hr />
                                        </div>
                                        <SubmitButton
                                            onClickFunction={handleLoginRequest}
                                        />
                                    </form>
                                    <div className="text-center">
                                        <Link to={registerAccountUrl} className="small login-form-field">Create an Account!</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </React.Fragment>
        );
    }

    if (pingResult === false) {
        return (
            <React.Fragment>
                <ColouredPage content={loginPageContent()} />
            </React.Fragment>
        );
    }
}
