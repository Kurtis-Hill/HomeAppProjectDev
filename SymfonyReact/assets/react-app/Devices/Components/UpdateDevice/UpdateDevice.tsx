import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import RoomResponseInterface from '../../../User/Response/Room/RoomResponseInterface';

import { UpdateDeviceFormInputsInterface } from './UpdateDeviceFormInputsInterface';

import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { getAllUserGroupsRequest, GroupResponseInterface } from '../../../User/Request/Group/GetAllUserGroupsRequest'
import { UserDataResponseInterface } from '../../../User/Response/UserDataResponseInterface';
import { FormInlineSpan } from '../../../Common/Components/Elements/FormInlineSpan';
import { FormInlineSelectWLabel } from '../../../Common/Components/Selects/FormInlineSelectWLabel';
import { FormInlineInputWLabel } from '../../../Common/Components/Inputs/FormInlineInputWLabel';

export function UpdateDevice(props: {
    deviceID: number;
    deviceName: string;
    group: GroupResponseInterface;
    room: RoomResponseInterface;
    roles: string[]
}) {

    const { deviceID, deviceName, group: groupName, room, roles } = props;
    
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

    const [userData, setUserData] = useState({
        userGroups: [] as GroupResponseInterface[],
        userRooms: [] as RoomResponseInterface[],
    });

    useEffect(() => {
        console.log('props', activeFormForUpdating)
        if (activeFormForUpdating.deviceGroup === true) {
            handleUserGroupDataRequest();
        }
    }, [activeFormForUpdating, originalDeviceData, deviceID]);
    
    const toggleFormInput = (event: Event) => {
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;

        setDeviceUpdateFormInputs({
            ...deviceUpdateFormInputs,
            [name]: originalDeviceData.current[name],
        })

        setActiveFormForUpdating({
            ...activeFormForUpdating,
            [name]: !activeFormForUpdating[name],
        });
    }

    const handleUpdateDeviceInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setDeviceUpdateFormInputs({
            ...deviceUpdateFormInputs,
            [name]: value,
        });
    }

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
        }
    }



    const sendUpdateDeviceRequest = () => {
        console.log('update devicee');
        // send request to update device
    }

    const buildGroupWithSaveRejectButtons = () => {
        const optionsForBuilder = userData.userGroups.map((group: GroupResponseInterface) => {
            return {
                value: group.groupID,
                name: group.groupName,
            }
        });

        if (optionsForBuilder.length === 0) {
            return (
                <DotCircleSpinner />
            )
        }

        return (
            <FormInlineSelectWLabel 
                labelName={'Device Group:'}
                changeEvent={handleUpdateDeviceInput}
                selectName={'deviceGroup'}
                selectOptions={optionsForBuilder}
                acceptClickEven={(e: Event) => sendUpdateDeviceRequest()}
                declineClickEvent={(e: Event) => toggleFormInput(e)}
                selectDefaultValue={groupName.groupID}
                declineDataName={'deviceGroup'}

            />
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
                                        <FormInlineInputWLabel 
                                            labelName='Device Name: '
                                            nameParam='deviceName'
                                            changeEvent={handleUpdateDeviceInput}
                                            value={deviceUpdateFormInputs.deviceName}
                                            acceptClickEvent={(e: Event) => sendUpdateDeviceRequest()}
                                            declineClickEvent={(e: Event) => toggleFormInput(e)}
                                            dataName='deviceName'
                                        />
                                    :
                                        <FormInlineSpan
                                            spanOuterTag={'Device Name:'}
                                            spanInnerTag={deviceName}
                                            clickEvent={(e: Event) => toggleFormInput(e)}
                                            dataName={'deviceName'}
                                        />
                            }
                        </div>
                        <div className="row" style={{paddingTop: "2%"}}>
                        {
                            activeFormForUpdating.deviceGroup === true
                                ? 
                                    buildGroupWithSaveRejectButtons()
                                :
                                <FormInlineSpan
                                    spanOuterTag={'Device Group:'}
                                    spanInnerTag={deviceUpdateFormInputs.deviceGroupName}
                                    clickEvent={(e: Event) => toggleFormInput(e)}
                                    dataName={'deviceGroup'}
                                />
                        } 
                    </div>
                </form>
            </div>
        </>
    )
}
