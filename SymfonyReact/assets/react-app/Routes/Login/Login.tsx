import { useState } from 'react';
import * as React from 'react';
import { Link, useNavigate } from "react-router-dom";

import { webappURL, registerAccountUrl } from "../../Common/CommonURLs";
import { setUserSession } from "../../session/user-session";

import SubmitButton from "../../Components/Buttons/SubmitButton";
import Input from "../../Components/Form/Input";
import ColouredPage from "../../Components/Pages/ColouredPage";
import DotCircleSpinner from "../../Components/Spinners/DotCircleSpinner";

import { LoginFormUserInputs } from "../../Components/Form/UserInputs/Interface/LoginFormUserInputs";

import { handleLogin } from "../../Request/LoginRequest";

import { LoginInterface } from "./LoginInterface"

export default function Login(props): LoginInterface {
    const [userInputs, setUserInputs] = useState<LoginFormUserInputs>({});
    const [error, setError] = useState<Array<string>>([]);
    const [loading, setLoading] = useState(false);

    const loginPageImage = require('../../../images/login/index-photo.jpg')

    let navigate = useNavigate();

    const handleInput = (event) => {
        const name: string = event.target.name;
        const value: string = event.target.value;
        setUserInputs(values => ({...values, [name]: value}))
    }

    const handleLoginRequest = async (event) => {
        event.preventDefault();
        setError([]);
        setLoading(true);

        if (userInputs.username === undefined || userInputs.username === "") {
            setError(['Please fill in username']);
            setLoading(false);
            return;
        }
        if (userInputs.password === "" || userInputs.password === undefined) {
            setError(['Please fill in password']);
            setLoading(false);
            return;
        }

        try {
            const loginResponse = await handleLogin(userInputs);

            setUserSession(loginResponse);
            // window.location.replace(`${webappURL}index`)
            navigate(`${webappURL}index`)
            
        } catch (error) {
            const errorResponse = error.response;
            console.log(error, errorResponse)
            setLoading(false);
            if (errorResponse.status === 401) {
                setError([errorResponse.data.message]);
            } else {
                setError(['Something went wrong']);
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
                                        />
                                        <Input
                                            name="password"
                                            type="password"
                                            onChangeFunction={handleInput}
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

    return (
        <React.Fragment>
            <ColouredPage content={loginPageContent()} />
        </React.Fragment>
    );
}
