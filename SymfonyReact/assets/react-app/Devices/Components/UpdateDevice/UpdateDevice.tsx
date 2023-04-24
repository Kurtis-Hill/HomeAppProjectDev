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
import { deviceUpdatePatchRequest, DeviceUpdatePatchInputInterface } from '../../Request/DeviceUpdatePatchRequest';
import { AxiosError, AxiosResponse } from 'axios';

export function UpdateDevice(props: {
    deviceID: number;
    deviceName: string;
    group: GroupResponseInterface;
    room: RoomResponseInterface;
    roles: string[];
    showErrorAnnouncementFlash?: (errors: Array<string>, title: string, timer?: number | null) => void;
    setRefreshNavDataFlag?: (newValue: boolean) => void;
}) {

    const { deviceID, deviceName, group: groupName, room, roles, showErrorAnnouncementFlash } = props;
    
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
        deviceID: deviceID,
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
        if (deviceID !== originalDeviceData.current.deviceID) {
            setDeviceUpdateFormInputs({
                deviceName: deviceName,
                password: '',
                deviceGroup: groupName.groupID,
                deviceGroupName: groupName.groupName,
                deviceRoom: room.roomID,
            });
            originalDeviceData.current = {
                deviceID: deviceID,
                deviceName: deviceName,
                password: '',
                deviceGroup: groupName.groupID,
                deviceGroupName: groupName.groupName,
                deviceRoom: room.roomID,
            };
        }

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
        // console.log('handleUserDataRequest')
        const groupDataResponse = await getAllUserGroupsRequest();
        if (groupDataResponse.status === 200) {
            const groupDataPayload = groupDataResponse.data.payload as UserDataResponseInterface;
            // console.log('payload UPDATE DEVICE', groupDataPayload)
            setUserData({
                ...userData,
                userGroups: groupDataPayload,
            })
        }
    }



    const sendUpdateDeviceRequest = async (e: Event) => {

        const name = (e.target as HTMLElement).dataset.name;

        let dataToSend: DeviceUpdatePatchInputInterface = {};

        switch (name) {
            case 'deviceName':
                dataToSend.deviceName = deviceUpdateFormInputs.deviceName;
                break;
            case 'deviceGroup':
                dataToSend.deviceGroup = deviceUpdateFormInputs.deviceGroup;
                break;
            case 'deviceRoom':
                dataToSend.deviceRoom = deviceUpdateFormInputs.deviceRoom;
                break;
        }
        try {
            const deviceUpdateResponse = await deviceUpdatePatchRequest(deviceID, dataToSend);

            if (deviceUpdateResponse.status === 202) {
                console.log('device update response', deviceUpdateResponse.data.payload);
                console.log('me too', showErrorAnnouncementFlash)
                showErrorAnnouncementFlash(['Device updated successfully'], 'Success', 3000);
                // props.setRefreshNavDataFlag(true);
            }
        } catch(error) {
            const errorResponse = error.response as Error|AxiosError;

            // if (errorResponse instanceof Error) {
            //     console.log('error', errorResponse.message);
            // } else {
            //     console.log('error', errorResponse.message);
            // }
        }

        // console.log('meTOOO', dataToSend);

        // console.log('update devicee');
        // console.log('deviceUpdateFormInputs', deviceUpdateFormInputs);
        // console.log('originalDeviceData', originalDeviceData.current);
        // console.log('device passed in', deviceID, deviceName, groupName, room, roles)
        // console.log('lol', e.target)
        // console.log('event data name', (e.target as HTMLElement).dataset.name);
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
                acceptClickEvent={(e: Event) => sendUpdateDeviceRequest(e)}
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
                                            acceptClickEvent={(e: Event) => sendUpdateDeviceRequest(e)}
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
