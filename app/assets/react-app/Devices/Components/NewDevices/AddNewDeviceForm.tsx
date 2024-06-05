import * as React from 'react';
import { useState, useEffect } from 'react';

import { UserDataContextInterface } from "../../../User/DataProviders/UserDataContextProvider";
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';

import { AddNewDeviceInputInterface, addNewDeviceRequest } from "../../Request/AddNewDeviceRequest"
import { DeviceResponseInterface } from '../../Response/DeviceResponseInterface';

import DotCircleSpinner from "../../../Common/Components/Spinners/DotCircleSpinner";
import { apiURL, webappURL } from '../../../Common/URLs/CommonURLs';
import { Link } from 'react-router-dom';
import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';
import GroupResponseInterface from '../../../User/Response/Group/GroupResponseInterface';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import RoomNavbarResponseInterface from '../../../UserInterface/Navbar/Response/RoomNavbarResponseInterface';
import { userDataRequest } from '../../../User/Request/UserDataRequest';
import { UserDataResponseInterface } from '../../../UserInterface/Navbar/Response/UserDataResponseInterface';
import { registeredDeviceIPsRequest } from '../../Request/RegisteredDeviceIPsRequest';
import { IPLogResponseInterface } from '../../../Common/Response/IPLogResponseInterface';

type AddNewDeviceUserInputsInterface = {
    deviceName: string;
    devicePassword: string;
    devicePasswordConfirm: string;
    deviceGroup: number;
    deviceRoom: number;
    deviceIPAddress: string|null;
}
export function AddNewDeviceForm(props: {
    setAddNewDeviceModal: ((show: boolean) => void);
    setRefreshNavDataFlag: (newValue: boolean) => void;
}) {
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;
    const setAddNewDeviceModal = props.setAddNewDeviceModal;
    
    const [addNewDeviceUserInputs, setAddNewDeviceUserInputs] = useState<AddNewDeviceUserInputsInterface>({
        deviceName: '',
        devicePassword: '',
        devicePasswordConfirm: '',
        deviceGroup: 0,
        deviceRoom: 0,
        deviceIPAddress: null,
    });

    const [errors, setErrors] = useState<string[]>([]);
    
    const [deviceRequestLoading, setDeviceRequestLoading] = useState<boolean>(false);

    const [newDeviceAddedData, setNewDeviceAddedData] = useState<DeviceResponseInterface|null>(null);

    const [userData, setUserData] = useState<UserDataContextInterface>({ userGroups: [], userRooms: [] })

    const [deviceIPs, setDeviceIPs] = useState<IPLogResponseInterface[]>([]);

    useEffect(() => {
        handleUserDataRequest();
    }, []);

    const handleUserDataRequest = async () => {
        const userDataResponse = await userDataRequest();
        const registeredDeviceIPs = await registeredDeviceIPsRequest();
        if (userDataResponse.status === 200) {
            const userDataPayload = userDataResponse.data.payload as UserDataResponseInterface;
            setUserData({ 
                userGroups: userDataPayload.userGroups, 
                userRooms: userDataPayload.userRooms ,
            });
        }
        if (registeredDeviceIPs.status === 200) {
            if (Array.isArray(registeredDeviceIPs.data.payload)) {
                const registeredDeviceIPsPayload = registeredDeviceIPs.data.payload as IPLogResponseInterface[];
                setDeviceIPs(registeredDeviceIPsPayload);
            }
        }
    }

    const handleAddNewDeviceInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;
        
        setAddNewDeviceUserInputs((values: AddNewDeviceUserInputsInterface) => ({...values, [name]: value}))
    }
    
    const handleNewDeviceFormSubmission = async (e: Event) => {
        e.preventDefault();
        setErrors([]);
        const validationPassed: boolean = validateAddNewDeviceUserInputs();
        if (validationPassed === false) {
            setDeviceRequestLoading(true);
            const jsonFormData: AddNewDeviceInputInterface = {
                'deviceName' : addNewDeviceUserInputs.deviceName,
                'devicePassword' : addNewDeviceUserInputs.devicePassword,
                'devicePasswordCheck' : addNewDeviceUserInputs.devicePasswordConfirm,
                'deviceRoom' :  parseInt(addNewDeviceUserInputs.deviceRoom),
                'deviceGroup' :  parseInt(addNewDeviceUserInputs.deviceGroup),
                'deviceIPAddress' : addNewDeviceUserInputs.deviceIPAddress ?? null,
            };

            try {
                const addNewDeviceResponse = await addNewDeviceRequest(jsonFormData);            
                if (addNewDeviceResponse !== null && addNewDeviceResponse.status === 201) {
                    const addNewDevicePayload: DeviceResponseInterface = addNewDeviceResponse.data.payload;
                    setNewDeviceAddedData(addNewDevicePayload);
                    setErrors([]);
                    setRefreshNavDataFlag(true);
                } else {
                    setErrors((errors: string[]) => ['Error adding new device, unexpected response']);
                }
            } catch(error: any) {
                setErrors((errors: string[]) => ['Error adding new device, unexpected response']);
            }
            setDeviceRequestLoading(false);
        }
    }

    const validateAddNewDeviceUserInputs = (): boolean => {
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
        return `${webappURL}device/${newDeviceID}`;
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
                    type="text"
                    autoFocus={true}
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
                                userData && userData.userGroups.length > 0 
                                    ? 
                                        userData.userGroups.map((group: GroupResponseInterface, index: number) => (
                                            <option key={index} value={group.groupID}>{group.groupName}</option>
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
                                userData && userData.userRooms.length > 0
                                    ?
                                        userData.userRooms.map((room: RoomNavbarResponseInterface, index: number) => (
                                            <option key={index} value={room.roomID}>{room.roomName}</option>
                                        ))
                                    :
                                        null
                            }
                        </select>
                    </div>

                    <div className="form-group">
                        <label className="large font-weight-bold" htmlFor="deviceIPAddress">Registered Device IP's</label>
                        <select
                            className="form-control"
                            name="deviceIPAddress"
                            id="deviceIPAddress"
                            value={addNewDeviceUserInputs.deviceIPAddress ?? "0"}
                            onChange={handleAddNewDeviceInput}
                        >
                            <option value="0">No IP Selected</option>
                            {
                                deviceIPs && deviceIPs.length > 0
                                    ?
                                        deviceIPs.map((deviceIP: IPLogResponseInterface, index: number) => (
                                            <option key={index} value={deviceIP.ipAddress}>{deviceIP.ipAddress}</option>
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
                                    action="submit"
                                    classes="add-new-device-submit-button"
                                />
                            :
                                null
                    }
                </>
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
