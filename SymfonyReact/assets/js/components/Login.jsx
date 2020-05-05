import React, { useState } from 'react';
import axios from 'axios';
import { setUserSession } from '../Utilities/Common';

function Login(props) {
    const username = useFormInput('');
    const password = useFormInput('');
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);

    const handleLogin = () => {
        setError(null);
        setLoading(true);

        axios.post('login_check', {username: username.value, password: password.value})
        .then(response => {
            setLoading(false);
            setUserSession(response.data.token, response.data.user);
            props.history.push('index');
            console.log("success", response.data.token, response.data.user);
        }).catch(error => {
            setLoading(false);
            if (error.response.status === 401) setError(error.response.data.message);
            else setError("Something went Wrong");
        });
    }

    return (
        <React.Fragment>
            <div className="bg-gradient-primary">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-10 col-lg-12 col-md-9">
                            <div className="card o-hidden border-0 shadow-lg my-5">
                                <div className="card-body p-0">
                                    <div className="row">
                                        <div className="col-lg-6 d-none d-lg-block bg-login-image" />
                                        <div className="col-lg-6">
                                            <div className="p-5">
                                                <div className="text-center">
                                                    <h1 className="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                                </div>
                                                {error && <><small style={{ color: 'red' }}>{error}</small><br /></>}<br />
                                                <div className="user">
                                                    <div className="form-group">
                                                        <input type="text" {...username} autoComplete="new-password" className="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" />
                                                    </div>
                                                    <div className="form-group">
                                                        <input type="password" {...password} autoComplete="new-password" className="form-control form-control-user" id="exampleInputPassword" />
                                                    </div>
                                                    <div className="form-group">
                                                        <div className="custom-control custom-checkbox small">
                                                            <input type="checkbox" className="custom-control-input" id="customCheck" />
                                                            <label className="custom-control-label" htmlFor="customCheck">Remember Me</label>
                                                        </div>
                                                    </div>
                                                    {loading ? <div className=" fa-2x fas fa-spinner fa-spin"></div> : <a onClick={handleLogin} className="btn btn-primary btn-user btn-block">Login</a>}
                                                    <hr />
                                                    <a href="index.html" className="btn btn-google btn-user btn-block">
                                                        <i className="fab fa-google fa-fw" /> Login with Google
                                                    </a>
                                                    <a href="index.html" className="btn btn-facebook btn-user btn-block">
                                                        <i className="fab fa-facebook-f fa-fw" /> Login with Facebook
                                                    </a>
                                                </div>
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
    console.log(value);
    return {
        value,
        onChange: handleChange
    }
}
export default Login;