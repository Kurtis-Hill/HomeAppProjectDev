import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import GroupNameResponseInterface from '../../../User/Response/GroupName/GroupNameResponseInterface';
import RoomResponseInterface from '../../../User/Response/Room/RoomResponseInterface';
import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';

import { UpdateDeviceFormInputsInterface } from './UpdateDeviceFormInputsInterface';
import Input from '../../../Common/Components/Inputs/Input';

import { getAllUserGroupsRequest, GroupResponseInterface } from '../../../User/Request/Group/GetAllUserGroupsRequest'
import { userDataRequest } from '../../../User/Request/UserDataRequest';
import { UserDataResponseInterface } from '../../../User/Response/UserDataResponseInterface';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';

export function UpdateDevice(props: {
    deviceID: number;
    deviceName: string;
    groupName: GroupNameResponseInterface;
    room: RoomResponseInterface;
    roles: string[]
}) {

    const { deviceID, deviceName, groupName, room, roles } = props;
    
    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
            deviceName: false,
            password: false,
            deviceGroup: false,
            deviceRoom: false,
    });

    const [deviceUpdateFormInputs, setDeviceUpdateFormInputs] = useState<UpdateDeviceFormInputsInterface|null>({
            deviceName: deviceName,
            password: '',
            deviceGroup: groupName.groupID,
            deviceGroupName: groupName.groupName,
            deviceRoom: room.roomID,
    });

    const originalDeviceData = useRef<UpdateDeviceFormInputsInterface>({
        deviceName: deviceName,
        password: '',
        deviceGroup: groupName.groupID,
        deviceGroupName: groupName.groupName,
        deviceRoom: room.roomID,
    });

    // const userData = useRef({
    //     userGroups: [] as GroupResponseInterface[],
    //     userRooms: [] as RoomResponseInterface[],
    // });

    const [userData, setUserData] = useState({
        userGroups: [] as GroupResponseInterface[],
        userRooms: [] as RoomResponseInterface[],
    });

    const handleUserGroupDataRequest = async () => {
        console.log('handleUserDataRequest')
        const groupDataResponse = await getAllUserGroupsRequest();
        if (groupDataResponse.status === 200) {
            const groupDataPayload = groupDataResponse.data.payload as UserDataResponseInterface;

            console.log('payload UPDATE DEVICE', groupDataPayload)
            setUserData({
                ...userData,
                userGroups: groupDataPayload,
            })
            // userData.current = { 
            //     userGroups: userDataPayload.userGroups, 
            //     userRooms: userDataPayload.userRooms 
            // };
        }
    }

    useEffect(() => {
        console.log('props', activeFormForUpdating)
        if (activeFormForUpdating.deviceGroup === true || activeFormForUpdating.deviceRoom === true) {
            handleUserGroupDataRequest();
        }
    }, [activeFormForUpdating, originalDeviceData]);

    const handleUpdateDeviceInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setDeviceUpdateFormInputs({
            ...deviceUpdateFormInputs,
            [name]: value,
        });
    }

    const toggleFormInput = (event: Event) => {
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;
        console.log('name', name, event.target);

        setDeviceUpdateFormInputs({
            ...deviceUpdateFormInputs,
            [name]: originalDeviceData.current[name],
        })

        setActiveFormForUpdating({
            ...activeFormForUpdating,
            [name]: !activeFormForUpdating[name],
        });
    }

    const sendUpdateDeviceRequest = () => {
        console.log('update devicee');
        // send request to update device

    }



    const buildInputWithSaveRejectButtons = (
        labelName: string,
        name: string,
        value: object,
    ) => {
        return (
            <>
            {/* <div className="col-6"> */}

                <InputWLabel
                    labelName={labelName}
                    name={name}
                    type="text"
                    onChangeFunction={handleUpdateDeviceInput}
                    autoFocus={true}
                    value={value}
                    labelExtraClasses='form-inline font-size-1-5 padding-r-1 displayBlockImportant'
                    extraClasses='hover'
                    />
            {/* </div> */}
            <span>
                <i className="fas fa-check-circle fa-2x hover accept-button" onClick={(e: Event) => sendUpdateDeviceRequest()}></i>
                <i className="fas fa-times-circle fa-2x hover cancel-button" onClick={(e: Event) => toggleFormInput(e)} data-name={name}></i>
            </span>
            </>
        )
    }

    const buildSelectsWithSaveRejectButtons = (
        labelName: string,
        name: string,
        value: object,
        options: object[],
    ) => {
        return (
            <>
                <label className="large font-weight-bold form-inline font-size-1-5 hover padding-r-1 displayBlockImportant">{labelName}</label>
                <div className="form-group">
                    <select name={name} defaultValue={originalDeviceData.deviceGroup} className="form-control" onChange={(e) => handleUpdateDeviceInput(e)}>
                        {
                            options.map((option: any, index: number) => {
                                return (
                                    <option key={index} value={option.value}>{option.label}</option>
                                )
                            })
                        }
                    </select>
                </div>
                <span>
                    <i className="fas fa-check-circle fa-2x hover accept-button" onClick={(e: Event) => sendUpdateDeviceRequest()}></i>
                    <i className="fas fa-times-circle fa-2x hover cancel-button" onClick={(e: Event) => toggleFormInput(e)} data-name={name}></i>
                </span>
            </>
        )
    }

    const buildGroupWithSaveRejectButtons = () => {
        // const allGroupsOptionsForUser = await getAllUserGroupsRequest();

        // const allGroupsPayload: GroupNameResponseInterface[] = allGroupsOptionsForUser.data.payload;

        // const optionsForBuilder = userData.current.userGroups.map((group: GroupResponseInterface) => {
        const optionsForBuilder = userData.userGroups.map((group: GroupResponseInterface) => {
            return {
                value: group.groupID,
                label: group.groupName,
            }
        });

        if (optionsForBuilder.length === 0) {
            return (
                <DotCircleSpinner />
            )
        }
        return (
            buildSelectsWithSaveRejectButtons(
                'Device Group: ',
                'deviceGroup',
                deviceUpdateFormInputs.deviceGroup,
                optionsForBuilder,
            )
        )
    }

    const buildDeviceDisplayViewFormElement = () => {
        return (
            // <div className="col-6">
                <span style={{width: "100%", paddingBottom: "3%"}} onClick={(e: Event) => toggleFormInput(e)} data-name="deviceName" className="large font-weight-bold form-inline font-size-1-5 hover padding-r-1">Device Name: <span className="padding-l-1">{deviceUpdateFormInputs.deviceName}</span></span>
            // </div>
        )
    }

    const buildDeviceGroupDisplayViewFormElement = () => {
        return (
            // <div className="col-6">
                <span style={{width: "100%", paddingBottom: "3%"}} onClick={(e: Event) => toggleFormInput(e)} data-name="deviceGroup" className="large font-weight-bold form-inline font-size-1-5 hover padding-r-1">Device Group: <span className="padding-l-1">{deviceUpdateFormInputs.deviceGroupName}</span></span>
            // </div>
        )
    }

    return (
        <>
            <div className="container">
                <div className="row">
                    <span className="large font-weight-bold form-inline font-size-1-5 padding-r-1">Device ID: {deviceID}</span>
                </div>
                <form>
                    <div className="row" style={{paddingTop: "2%"}}>
                            { 
                                activeFormForUpdating.deviceName === true 
                                    ?
                                        buildInputWithSaveRejectButtons(
                                            'Device Name: ',
                                            'deviceName',
                                            deviceUpdateFormInputs.deviceName,
                                        )
                                    :
                                        buildDeviceDisplayViewFormElement()
                            }
                        </div>
                        <div className="row" style={{paddingTop: "2%"}}>
                        {
                            activeFormForUpdating.deviceGroup === true
                                ? 
                                    buildGroupWithSaveRejectButtons()
                                :
                                    buildDeviceGroupDisplayViewFormElement()
                        } 
                    </div>
                </form>
            </div>
        </>
    )
}
