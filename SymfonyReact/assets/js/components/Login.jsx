import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { setUserSession } from '../Utilities/Common';


function Login(props) {
    const username = useFormInput('');
    const password = useFormInput('');
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);

    const loginPhoto = require('../../images/sitepictures/indexPhoto.jpg');

    const handleLogin = (event) => {
        setError(null);
        setLoading(true);

        axios.get('csrfToken')
        .then(response => {
            const formToken = response.data.token;
        
            axios.post('api/login_check', {username: username.value, password: password.value})
            .then(response => {               
                setUserSession(response.data.token, response.data.refreshToken);
                
                const loginForm = document.getElementById('loginForm');
                const formData = new FormData(loginForm); 
                
                formData.append('_csrf_token', formToken);
                
                const config = {     
                    headers: { 'content-type': 'multipart/form-data' }
                }
                
                axios.post('login', formData, config)
                .then(response => {
                    console.log(response);
                    setLoading(false);
                    //props.history.push('index');
                    window.location.replace('index');
                })
                .catch(error => {
                    console.log(error);
                });
            }).catch(error => {
                setLoading(false);
                console.log(error);
            });
        })
        .catch(error => {
            console.log(error);
        })
    }

    // const handleRefreshToken = () => {
    //     console.log('HEYYY');
    //     setTimeout(() => {handleRefreshToken(), 5000})
    // }

    return (
        <React.Fragment>
            <div className="bg-gradient-primary">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-10 col-lg-12 col-md-9">
                            <div className="card o-hidden border-0 shadow-lg my-5">
                                <div className="card-body p-0">
                                    <div className="row">
                                        <img src={ loginPhoto } className="col-lg-6 d-none d-lg-block bg-login-image" />
                                        <div className="col-lg-6">
                                            <div className="p-5">
                                                <div className="text-center">
                                                    <h1 className="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                                </div>
                                                {error && <><small style={{ color: 'red' }}>{error}</small><br /></>}<br />
                                                <form id="loginForm" className="user">
                                                    <div className="form-group">
                                                        <input type="text" name="email" {...username} autoComplete="username" className="form-control form-control-user" aria-describedby="emailHelp" />
                                                    </div>
                                                    <div className="form-group">
                                                        <input type="password" name="password" {...password} autoComplete="new-password" className="form-control form-control-user" />
                                                    </div>
                                                    <div className="form-group">
                                                        <div className="custom-control custom-checkbox small">
                                                            <input type="checkbox" className="custom-control-input" id="customCheck" />
                                                            <label className="custom-control-label" htmlFor="customCheck">Remember Me</label>
                                                        </div>
                                                    </div>
                                                    {loading ? <div className="center-item fa-2x fas fa-spinner fa-spin"></div> : <button name="submit" onClick={handleLogin} action="submit" className="btn btn-primary btn-user btn-block">Login</button>}
                                                    <hr />
                                                    <a href="index.html" className="btn btn-google btn-user btn-block">
                                                        <i className="fab fa-google fa-fw" /> Login with Google
                                                    </a>
                                                    <a href="index.html" className="btn btn-facebook btn-user btn-block">
                                                        <i className="fab fa-facebook-f fa-fw" /> Login with Facebook
                                                    </a>
                                                </form>
                                                <hr />
                                                <div className="text-center">
                                                    <a className="small" href="forgot-password.html">Forgot Password?</a>
                                                </div>
                                                <div className="text-center">
                                                    <a className="small" href="register.html">Create an Account!</a>
                                                </div>
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