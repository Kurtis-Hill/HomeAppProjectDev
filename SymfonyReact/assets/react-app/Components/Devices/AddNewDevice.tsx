import * as React from 'react';
import { useState, useEffect } from 'react';

import AddNewDeviceUserInputsInterface from './AddNewDeviceUserInputsInterface';

import InputWLabel from '../Form/Inputs/InputWLabel';
import UserDataContext from '../../Contexts/UserData/UserDataContext';
import {UserDataContextInterface} from "../UserDataProvider/UserDataContextProvider";
import GroupNameNavbarResponseInterface from '../../Response/User/Navbar/Interfaces/GroupNameNavbarResponseInterface';
import CloseButton from '../Buttons/CloseButton';


export function AddNewDevice(props: {
    showAddNewDeviceModal: boolean; 
    setAddNewDeviceModal: ((show: boolean) => void);
}) {
    const showAddNewDeviceModal = props.showAddNewDeviceModal;
    const setAddNewDeviceModal = props.setAddNewDeviceModal;

    const [errors, setErrors] = useState<string[]>([]);

    const [addNewDeviceUserInputs, setAddNewDeviceUserInputs] = useState<AddNewDeviceUserInputsInterface>({
        deviceName: '',
        devicePassword: '',
        devicePasswordConfirm: '',
        deviceGroup: 0,
        deviceRoom: 0
    });

    const handleAddNewDeviceInput = (event: { target: { name: string; value: string; }; }) => {
        const name: string = event.target.name;
        const value: string = event.target.value;
        setAddNewDeviceUserInputs((values: AddNewDeviceUserInputsInterface) => ({...values, [name]: value}))
    }

    const handleNewDeviceFormSubmission = (e: Event) => {
        e.preventDefault();

        const jsonFormData = {
            'deviceName' : addNewDeviceUserInputs.deviceName,
            'devicePassword' : addNewDeviceUserInputs.devicePassword,
            'devicePasswordCheck' : addNewDeviceUserInputs.devicePasswordConfirm,
            'deviceRoom' :  parseInt(addNewDeviceUserInputs.deviceRoom),
            'deviceGroup' :  parseInt(addNewDeviceUserInputs.deviceGroup),
        };   
    }

    return (
        <>
            {
                errors.length > 0 
                    ?
                        <div className="error-container">
                            <div className="form-modal-error-box">
                                <ol>
                                    {errors.map((error: string, index: number) => (
                                        <li key={index} className="form-modal-error-text">{Object.keys(error).length === 0 ? 'Something has gone wrong' : error}</li>
                                    ))}
                                </ol>
                            </div>
                        </div>
                    : null
            }
            <form onSubmit={(e: Event) => {handleNewDeviceFormSubmission(e)}} id="modal-form">
                <InputWLabel
                    labelName='Device Name'
                    name='deviceName'
                    value={addNewDeviceUserInputs.deviceName}
                    onChangeFunction={handleAddNewDeviceInput}
                />

                <InputWLabel
                    labelName='Device Password'
                    name='deviceName'
                    value={addNewDeviceUserInputs.devicePassword}
                    onChangeFunction={handleAddNewDeviceInput}
                    type="password"
                    />

                <InputWLabel
                    labelName='Retype Device Password'
                    name='deviceName'
                    value={addNewDeviceUserInputs.devicePasswordConfirmed}
                    onChangeFunction={handleAddNewDeviceInput}
                    type="password"
                />

                <UserDataContext.Consumer>
                    {(userData: UserDataContextInterface) => (
                        <>
                            <div className="form-group">
                                <label htmlFor="deviceGroup">Device Group</label>
                                <select
                                    className="form-control"
                                    name="deviceGroup"
                                    id="deviceGroup"
                                    value={addNewDeviceUserInputs.deviceGroup}
                                    onChange={handleAddNewDeviceInput}
                                >
                                    <option value="0">Select a group</option>
                                    {
                                        userData.userGroups !== undefined && userData.userGroups.length > 0 
                                            ? 
                                                userData.userGroups.map((group: GroupNameNavbarResponseInterface, index: number) => (
                                                <option key={index} value={group.groupNameID}>{group.groupName}</option>
                                                )) 
                                            : 
                                                null
                                    }
                                </select>
                            </div>
                        </>
                    )} 
                </UserDataContext.Consumer>
                <CloseButton close={setAddNewDeviceModal} />
            </form>
        </>
    )
}
