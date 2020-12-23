import React, { useState, useEffect, } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { setUserTokens, setUserSession, webappURL, apiURL, getToken } from '../Utilities/Common';

function Login(props) {
    const username = useFormInput('');
    const password = useFormInput('');
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);

    const loginPhoto = require('../../images/sitepictures/indexPhoto.jpg');

    const createAccountLink = "/HomeApp/register";

    const handleLogin = async (event) => {
        setError(null);
        setLoading(true);
        
        const csrfTokenResponse = await axios.get(apiURL+'csrfToken')
            .catch(error => {
                alert('Please Fresher The Browser');
            });

        const formToken = csrfTokenResponse.data.token;

        const loginCheckResponse = await axios.post(apiURL+'login_check', {username: username.value, password: password.value})        
            .catch(error => {
                setError('Invalid Credentials');
                setLoading(false); 
            });
        
        loginCheckResponse 
            ? setUserTokens(loginCheckResponse.data.token, loginCheckResponse.data.refreshToken)
            : setError('Login check response error');

        const loginForm = document.getElementById('login-form');

        const formData = new FormData(loginForm);

        formData.append('_csrf_token', formToken);

        const loginResponse = await axios.post('/HomeApp/login', formData, { headers: { 'content-type': 'multipart/form-data' } })
            .catch(error => {
                setError('Login Failed Please Try Again');
                setLoading(false); 
            });

        if (loginResponse.status === 200) {
            const userDetailsResponse = await axios.get(apiURL+'user/account-details', { headers: {"Authorization" : `BEARER ${getToken()}`} });
            
            //const userSession = userDetailsResponse.status === 200 ? setUserSession(userDetailsResponse.data.userID, userDetailsResponse.data.roles) : setError(userDetailsResponse.data.error);
            if (userDetailsResponse.status === 200) {
                setUserSession(userDetailsResponse.data.userID, userDetailsResponse.data.roles);
                window.location.replace(webappURL+'index');
            }
            else {
                setLoading(false);
            }
        } 
        else {
            setError(loginResponse.data.errors);
            setLoading(false);
        }
    }
    
    return (
        <React.Fragment>
            <div className="bg-gradient-primary">
                <div className="row justify-content-center" style={{height:'100vh'}}>
                    <div className="col-xl-5 col-lg-2 col-md-12">
                        <div className="card o-hidden border-0 shadow-lg my-5">
                            <div className="card-body p-0">
                                <div className="row">
                                    <img src={ loginPhoto } className="col-lg-6 d-none d-lg-block" />
                                    <div className="col-lg-6">
                                        <div className="login-form">
                                            <div className="text-center">
                                                <h1 className="login-formn-container h2 text-gray-700 mb-4 login-banner">Welcome Back To The Home-App!</h1>
                                            </div>  
                                            {error !== null ? <h2 className="text-center">{error}</h2> : null}                                          
                                            <form className="user" id="login-form">
                                                <div className="form-group">                                    
                                                    <input type="text" name="email" {...username} placeholder="E-mail" autoComplete="username" className="form-control form-control-user login-form-field" aria-describedby="emailHelp" />
                                                </div>
                                                <div className="form-group">
                                                    <input type="password" name="password" {...password} placeholder="Password" autoComplete="new-password" className="form-control form-control-user login-form-field" />
                                                </div>
                                                <div className="form-group">
                                                    <div className="custom-control custom-checkbox small">
                                                        <input type="checkbox" className="custom-control-input" id="customCheck" />
                                                        <label className="custom-control-label" htmlFor="customCheck">Remember Me</label>
                                                    </div>
                                                </div>
                                                {loading 
                                                    ? <div className="center-item login-spinner fa-2x fas fa-spinner fa-spin"></div> 
                                                    : <button name="submit" onClick={handleLogin} action="submit" className="btn btn-primary btn-user btn-block">Login</button>}
                                                <hr />        
                                            </form>                                               
                                            <div className="text-center">
                                                <a href={createAccountLink} className="small login-form-field" href="register.html">Create an Account!</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}

const useFormInput = initialValue => {
    const [value, setvalue] = useState(initialValue);

    const handleChange = e => {
        setvalue(e.target.value);
    }

    return {
        value,
        onChange: handleChange
    }
}

export default Login;