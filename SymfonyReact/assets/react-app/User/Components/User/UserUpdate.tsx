import * as React from 'react';
import { NavigateFunction, useNavigate } from "react-router-dom";
import { useState, useEffect, useRef } from 'react';

import UserResponseInterface from '../../Response/UserResponseInterface';
import { getSingleUserRequest } from '../../Request/User/GetSingleUserRequest';
import { FormInlineInputWLabel } from '../../../Common/Components/Inputs/FormInlineInputWLabel';
import { FormInlineSpan } from '../../../Common/Components/Elements/FormInlineSpan';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import UpdateUserPassword from './UpdateUserPassword';
import UserUpdateRequest, { UserUpdateRequestType } from '../../Request/User/UserUpdateRequest';
import { logoutUrl } from '../../../Common/URLs/CommonURLs';

export function UserUpdate(props: { userID: number }) {
    const navigate: NavigateFunction = useNavigate();

    const { userID } = props;

    const [userData , setUserData] = useState<UserResponseInterface>({});

    const [activeFormForUpdatingUser, setActiveFormForUpdatingUser] = useState({
        firstName: false,
        lastName: false,
        email: false,
        group: false,
        profilePicture: false,
        roles: false,     
        password: false,   
    });

    const [userUpdateFormInputs, setUserUpdateFormInputs] = useState({
        firstName: userData.firstName,
        lastName: userData.lastName,
        email: userData.email,
        group: userData.group,
        profilePicture: userData.profilePicture,
        roles: userData.roles,
        newPassword: '',
        oldPassword: '',
    });
    
    const originalUserData = useRef<UserResponseInterface>(userData);
    
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (userData.userID !== originalUserData.current.userID || userData.userID === undefined) {
            handleUserChangeRequest(userID);   
        }
    });

    const handleUserChangeRequest = async (userID: number) => {
        const getSingleUserResponse = await getSingleUserRequest(userID);
        const userResponseData: UserResponseInterface = getSingleUserResponse.data.payload;
        setUserData(userResponseData);
        originalUserData.current = userResponseData;

        console.log('userResponseData: ', userResponseData)
    }
    
    const toggleFormInput = (event: Event) => {
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;

        setActiveFormForUpdatingUser({
            ...activeFormForUpdatingUser,
            [name]: !activeFormForUpdatingUser[name],
        });

        if (activeFormForUpdatingUser[name] !== 'password') {
            setUserUpdateFormInputs({
                ...userUpdateFormInputs,
                [name]: originalUserData.current[name],
            });
        }
    }

    const handleFormInputChange = (event: Event) => {
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name === undefined || (event.target as HTMLElement|HTMLInputElement).dataset.name == ''
            ? (event.target as HTMLInputElement).name
            : (event.target as HTMLElement|HTMLInputElement).dataset.name


        const value = (event.target as HTMLInputElement).value;
            
        setUserUpdateFormInputs({
            ...userUpdateFormInputs,
            [name]: value,
        });
            // console.log('userUpdateFormInputs: ', userUpdateFormInputs);

    }

    const sendUserUpdateRequest = async (event: Event) => {
        event.preventDefault();
        // console.log('userUpdateFormInputs: ', userUpdateFormInputs);
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name === undefined || (event.target as HTMLElement|HTMLInputElement).dataset.name == ''
        ? (event.target as HTMLInputElement).name
        : (event.target as HTMLElement|HTMLInputElement).dataset.name


        let requestDataToSend: UserUpdateRequestType = {};
        switch (name) {
            case 'firstName':
                requestDataToSend = {
                    firstName: userUpdateFormInputs.firstName,
                }
                break;
            case 'lastName':
                requestDataToSend = {
                    lastName: userUpdateFormInputs.lastName,
                }
                break;
            case 'email':
                requestDataToSend = {
                    email: userUpdateFormInputs.email,
                }
                break;
            case 'password':
                requestDataToSend = {
                    newPassword: userUpdateFormInputs.newPassword,
                    oldPassword: userUpdateFormInputs.oldPassword,
                }
                break;
            default: throw new Error('Invalid name for user update request');
        }

        console.log('requestDataToSend: ', requestDataToSend);

        const userUpdateResponse = await UserUpdateRequest(requestDataToSend, userID);
        
        if (userUpdateResponse.status !== undefined) {
            setLoading(false);
        }
        if (userUpdateResponse.status === 200) {
            toggleFormInput(event);
            handleUserChangeRequest(userID);

            if (requestDataToSend.email !== undefined) {
                navigate(logoutUrl)
            }
        }
    }

    if (userData.userID === undefined) {
        return <><DotCircleSpinner classes='spinner-absolute-center' /></>
    }
    return (
        <>
            {
                loading === true
                    ? <DotCircleSpinner classes='spinner-absolute-center' />
                    : null
            }
            <div className="container" style={{ textAlign: "center", margin: "inherit"}}>
                <div className="row" style={{ paddingTop: '5vh' }}>
                    <span className="large font-weight-bold form-inline font-size-1-5 padding-r-1">User ID: {userData.userID}</span>
                </div>
                <form>
                    <div className="row" style={{paddingTop: "4%"}}>
                        {
                            activeFormForUpdatingUser.firstName === true && userData.canEdit === true
                                ? 
                                    <FormInlineInputWLabel
                                        labelName='First Name: '
                                        nameParam='firstName'
                                        changeEvent={handleFormInputChange}
                                        value={userUpdateFormInputs.firstName}
                                        acceptClickEvent={(e: Event) => sendUserUpdateRequest(e)}
                                        declineClickEvent={(e: Event) => toggleFormInput(e)}
                                        dataName='firstName'
                                    />
                                :
                                    <FormInlineSpan
                                        spanOuterTag='First Name: '
                                        spanInnerTag={userData.firstName}
                                        clickEvent={(e: Event) => toggleFormInput(e)}
                                        dataName='firstName'
                                        canEdit={userData.canEdit}
                                    />

                        }

                        {
                            activeFormForUpdatingUser.lastName === true && userData.canEdit === true
                                ?
                                    <FormInlineInputWLabel
                                        labelName='Last Name: '
                                        nameParam='lastName'
                                        changeEvent={handleFormInputChange}
                                        value={userUpdateFormInputs.lastName}
                                        acceptClickEvent={(e: Event) => sendUserUpdateRequest(e)}
                                        declineClickEvent={(e: Event) => toggleFormInput(e)}
                                        dataName='lastName'
                                    />
                                :
                                    <FormInlineSpan
                                        spanOuterTag='Last Name: '
                                        spanInnerTag={userData.lastName}
                                        clickEvent={(e: Event) => toggleFormInput(e)}
                                        dataName='lastName'
                                        canEdit={userData.canEdit}
                                    />                            
                        }

                        {
                            activeFormForUpdatingUser.email === true && userData.canEdit === true
                                ?
                                    <FormInlineInputWLabel
                                        labelName='Email: '
                                        nameParam='email'
                                        changeEvent={handleFormInputChange}
                                        value={userUpdateFormInputs.email}
                                        acceptClickEvent={(e: Event) => sendUserUpdateRequest(e)}
                                        declineClickEvent={(e: Event) => toggleFormInput(e)}
                                        dataName='email'
                                    />
                                :
                                    <FormInlineSpan
                                        spanOuterTag='Email: '
                                        spanInnerTag={userData.email}
                                        clickEvent={(e: Event) => toggleFormInput(e)}
                                        dataName='email'
                                        canEdit={userData.canEdit}
                                    />
                        }

                        {
                            activeFormForUpdatingUser.password === true && userData.canEdit === true
                                ?
                                    <UpdateUserPassword
                                        toggleUserInput={(e: Event) => toggleFormInput(e)}
                                        updateUser={(e: Event) => sendUserUpdateRequest(e)}
                                        onInputChange={(e: Event) => handleFormInputChange(e)}
                                    />
                                :
                                    <>
                                        <div className="update-password-container">
                                            <SubmitButton
                                                text='Update Password'
                                                name='password'
                                                action='updateUser'
                                                onClickFunction={(e) => toggleFormInput(e)}
                                            />
                                        </div>
                                    </>
                        }
                    </div>
                </form>
            </div>
        </>
    )
}