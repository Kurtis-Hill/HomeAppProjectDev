import * as React from 'react';
import { useState, useEffect } from 'react';

import AddNewDeviceUserInputsInterface from './AddNewDeviceUserInputsInterface';

import UserDataContext from '../../../User/Contexts/UserDataContext';
import { UserDataContextInterface } from "../../../User/DataProviders/UserDataContextProvider";
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';

import { AddNewDeviceInputInterface, addNewDeviceRequest } from "../../Request/AddNewDeviceRequest"
import { AddNewDeviceResponse } from '../../Response/DeviceResponseInterface';

import DotCircleSpinner from "../../../Common/Components/Spinners/DotCircleSpinner";
import { apiURL, webappURL } from '../../../Common/URLs/CommonURLs';
import { Link } from 'react-router-dom';
import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';
import GroupNameNavbarResponseInterface from '../../../UserInterface/Navbar/Response/GroupNameNavbarResponseInterface';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import RoomNavbarResponseInterface from '../../../UserInterface/Navbar/Response/RoomNavbarResponseInterface';

export function AddNewDevice(props: {
    showAddNewDeviceModal: boolean; 
    setAddNewDeviceModal: ((show: boolean) => void);
}) {
    const showAddNewDeviceModal = props.showAddNewDeviceModal;
    const setAddNewDeviceModal = props.setAddNewDeviceModal;
    
    const [addNewDeviceUserInputs, setAddNewDeviceUserInputs] = useState<AddNewDeviceUserInputsInterface>({
        deviceName: '',
        devicePassword: '',
        devicePasswordConfirm: '',
        deviceGroup: 0,
        deviceRoom: 0
    });

    const [errors, setErrors] = useState<string[]>([]);
    
    const [deviceRequestLoading, setDeviceRequestLoading] = useState<boolean>(false);

    const [newDeviceAddedData, setNewDeviceAddedData] = useState<AddNewDeviceResponse|null>(null);

    // useEffect(() => {

    // },[newDeviceAddedData])
    const handleAddNewDeviceInput = (event: { target: { name: string; value: string; }; }) => {
        const name: string = event.target.name;
        const value: string = event.target.value;
        setAddNewDeviceUserInputs((values: AddNewDeviceUserInputsInterface) => ({...values, [name]: value}))
    }
    
    
    const handleNewDeviceFormSubmission = async (e: Event) => {
        e.preventDefault();
        setErrors([]);
        const validationFailed: boolean = validateAddNewDeviceUserInputs();
        // console.log('we are here at least', validationFailed)
        if (validationFailed === false) {
            setDeviceRequestLoading(true);
            console.log('validation passed', validationFailed)
            const jsonFormData: AddNewDeviceInputInterface = {
                'deviceName' : addNewDeviceUserInputs.deviceName,
                'devicePassword' : addNewDeviceUserInputs.devicePassword,
                'devicePasswordCheck' : addNewDeviceUserInputs.devicePasswordConfirm,
                'deviceRoom' :  parseInt(addNewDeviceUserInputs.deviceRoom),
                'deviceGroup' :  parseInt(addNewDeviceUserInputs.deviceGroup),
            };

            const addNewDeviceResponse = await addNewDeviceRequest(jsonFormData);

            console.log('we passed and status is 201', addNewDeviceResponse);
            if (addNewDeviceResponse !== null && addNewDeviceResponse.status === 200) {
                const addNewDevicePayload: AddNewDeviceResponse = addNewDeviceResponse.data.payload;
                setNewDeviceAddedData(addNewDevicePayload);
                setDeviceRequestLoading(false);
                setErrors([]);
            } else {
                console.log('why here too');
                setDeviceRequestLoading(false);
                setErrors((errors: string[]) => ['Error adding new device, unexpected response']);
            }
        } 
    }


    const validateAddNewDeviceUserInputs = (): boolean => {
        console.log('here is inputs', addNewDeviceUserInputs)
        let failedValidation = false;
        if (addNewDeviceUserInputs.deviceName === '' || addNewDeviceUserInputs.deviceName === null) {
            setErrors((errors: string[]) => [...errors, 'Device name is required']);
            failedValidation = true;
        }
        
        if (addNewDeviceUserInputs.devicePassword === '' || addNewDeviceUserInputs.devicePassword === null) {
            setErrors((errors: string[]) => [...errors, 'Device password is required']);
            failedValidation = true;
        }
        
        if (addNewDeviceUserInputs.devicePasswordConfirm === '' || addNewDeviceUserInputs.devicePasswordConfirm === null) {
            setErrors((errors: string[]) => [...errors, 'Device password confirmation is required']);
            failedValidation = true;
        }
        
        if (addNewDeviceUserInputs.devicePassword !== addNewDeviceUserInputs.devicePasswordConfirm) {
            setErrors((errors: string[]) => [...errors, 'Device passwords do not match']);
            failedValidation = true;
        }
        
        if (addNewDeviceUserInputs.deviceGroup === 0 || addNewDeviceUserInputs.deviceGroup === null) {
            setErrors((errors: string[]) => [...errors, 'Device group is required']);
            failedValidation = true;
        }
        
        if (addNewDeviceUserInputs.deviceRoom === 0 || addNewDeviceUserInputs.deviceRoom === null) {
            setErrors((errors: string[]) => [...errors, 'Device room is required']);
            failedValidation = true;
        }

        return failedValidation;
    }

    const buildNewDeviceUrl = (newDeviceID: number): string => {
        return `${webappURL}device?device-id=${newDeviceID}`;
    }

    // const 

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
                    name='devicePassword'
                    value={addNewDeviceUserInputs.devicePassword}
                    onChangeFunction={handleAddNewDeviceInput}
                    type="password"
                    />

                <InputWLabel
                    labelName='Retype Device Password'
                    name='devicePasswordConfirm'
                    value={addNewDeviceUserInputs.devicePasswordConfirm}
                    onChangeFunction={handleAddNewDeviceInput}
                    type="password"
                />

                <UserDataContext.Consumer>                    
                    {(userData: {'userData': UserDataContextInterface}) => (
                        <>
                            <div className="form-group">
                                <label className="large font-weight-bold" htmlFor="deviceGroup">Device Group</label>
                                <select
                                    className="form-control"
                                    name="deviceGroup"
                                    id="deviceGroup"
                                    value={addNewDeviceUserInputs.deviceGroup}
                                    onChange={handleAddNewDeviceInput}
                                >
                                    <option value="0">Select a group</option>
                                    {
                                        userData.userData.userGroups.length > 0 
                                            ? 
                                                userData.userData.userGroups.map((group: GroupNameNavbarResponseInterface, index: number) => (
                                                    <option key={index} value={group.groupNameID}>{group.groupName}</option>
                                                )) 
                                            : 
                                                null
                                    }
                                </select>
                            </div>
                            <div className="form-group">
                                <label className="large font-weight-bold" htmlFor="deviceRoom">Device Room</label>
                                <select
                                    className="form-control"
                                    name="deviceRoom"
                                    id="deviceRoom"
                                    value={addNewDeviceUserInputs.deviceRoom}
                                    onChange={handleAddNewDeviceInput}
                                >
                                    <option value="0">Select a room</option>
                                    {
                                        userData.userData.userRooms.length > 0
                                            ?
                                                userData.userData.userRooms.map((room: RoomNavbarResponseInterface, index: number) => (
                                                    <option key={index} value={room.roomID}>{room.roomName}</option>
                                                ))
                                            :
                                                null
                                    }
                                </select>
                            </div>
                            { 
                                deviceRequestLoading === false && newDeviceAddedData === null
                                    ?
                                        <SubmitButton
                                            type="submit"
                                            text="Add Device"
                                            name="Add-Device"
                                            action="GET"
                                            classes="add-new-device-submit-button"
                                        />
                                    :
                                        null
                            }
                        </>
                    )} 
                </UserDataContext.Consumer>
                { 
                    deviceRequestLoading === false &&  newDeviceAddedData === null
                        ?
                            <CloseButton 
                                close={setAddNewDeviceModal} 
                                classes={"modal-cancel-button"} 
                            />
                        : 
                            newDeviceAddedData === null && deviceRequestLoading === true
                                ? <DotCircleSpinner classes="center-spinner" />
                                : null
                }
                {
                    newDeviceAddedData !== null && newDeviceAddedData.secret !== null 
                        ?
                            <div className="secret-container">
                                <label className="modal-space large font-weight-bold">This is your devices secret, you will need this when doing an initial setup of your device you will not see this again after you continue.</label>
                                <div className="secret-box">
                                    <p className="font-weight-bold"> {newDeviceAddedData.secret}</p>
                                </div>
                                <div className="center" style={{paddingTop:"2%"}}>
                                    <Link to={ buildNewDeviceUrl } onClick={() => {setAddNewDeviceModal(false)}} data-dismiss="modal" className="btn-primary modal-submit-center" type="submit" value="submit">Got it!</Link>
                                </div>
                            </div>
                        : 
                            null
                }
            </form>
        </>
    )
}
