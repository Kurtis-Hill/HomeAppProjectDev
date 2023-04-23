import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import GroupNameResponseInterface from '../../../User/Response/GroupName/GroupNameResponseInterface';
import RoomResponseInterface from '../../../User/Response/Room/RoomResponseInterface';
import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';

import { UpdateDeviceFormInputsInterface } from './UpdateDeviceFormInputsInterface';
import Input from '../../../Common/Components/Inputs/Input';

import { getAllUserGroupsRequest, GroupResponseInterface } from '../../../User/Request/Group/GetAllUserGroupsRequest'

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
            deviceRoom: room.roomID,
    });

    const originalDeviceData = useRef<UpdateDeviceFormInputsInterface>({
        deviceName: deviceName,
        password: '',
        deviceGroup: groupName.groupID,
        deviceRoom: room.roomID,
    });

    useEffect(() => {
        console.log('props', activeFormForUpdating)
    }, [activeFormForUpdating]);

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
                <InputWLabel
                    labelName={labelName}
                    name={name}
                    type="text"
                    onChangeFunction={handleUpdateDeviceInput}
                    autoFocus={true}
                    value={value}
                    labelExtraClasses='form-inline font-size-1-5 hover padding-r-1 displayBlockImportant'
                    extraClasses='hover col-6'
                />
                <i className="fas fa-check-circle fa-2x hover accept-button" onClick={(e: Event) => sendUpdateDeviceRequest()}></i>
                <i className="fas fa-times-circle fa-2x hover cancel-button" onClick={(e: Event) => toggleFormInput(e)} data-name={name}></i>
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
                <select name={name} className="form-control" onChange={(e) => handleUpdateDeviceInput(e)}>
                    {
                        options.map((option: any) => {
                            return (
                                <option value={option.value} selected={option.value === value}>{option.label}</option>
                            )
                        })
                    }
                </select>
            </>
        )
    }

    const buildGroupWithSaveRejectButtons = async () => {
        const allGroupsOptionsForUser = await getAllUserGroupsRequest();

        const allGroupsPayload: GroupNameResponseInterface[] = allGroupsOptionsForUser.data.payload;

        const optionsForBuilder = allGroupsPayload.map((group: GroupResponseInterface) => {
            return {
                value: group.groupID,
                label: group.groupName,
            }
        });

        return (
            buildSelectsWithSaveRejectButtons(
                'Device Group: ',
                'deviceGroup',
                deviceUpdateFormInputs.deviceGroup,
                optionsForBuilder,
            )
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
                        <div className="col-6">

                            { 
                                activeFormForUpdating.deviceName === true 
                                    ?
                                        buildInputWithSaveRejectButtons(
                                            'Device Name: ',
                                            'deviceName',
                                            deviceUpdateFormInputs.deviceName,
                                        )
                                    :
                                        <span style={{width: "100%", paddingBottom: "3%"}} onClick={(e: Event) => toggleFormInput(e)} data-name="deviceName" className="large font-weight-bold form-inline font-size-1-5 hover padding-r-1">Device Name: {deviceUpdateFormInputs.deviceName}</span>
                            }
                        </div>
                        <br />
                        <br />
                        {
                            activeFormForUpdating.deviceGroup === true
                                ? 
                                    buildGroupWithSaveRejectButtons()
                                :
                                        <span style={{width: "100%", paddingBottom: "3%"}} onClick={(e: Event) => toggleFormInput(e)} data-name="deviceGroup" className="large font-weight-bold form-inline font-size-1-5 hover padding-r-1">Device Group: {deviceUpdateFormInputs.deviceGroup}</span>
                        }
                    </div>
                </form>
            </div>
        </>
    )
}
