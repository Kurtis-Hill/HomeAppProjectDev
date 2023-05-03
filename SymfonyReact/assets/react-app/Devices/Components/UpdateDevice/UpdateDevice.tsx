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
import { AnnouncementFlashModalBuilder } from '../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { AnnouncementFlashModal } from '../../../Common/Components/Modals/AnnouncementFlashModal';
import { DeviceResponseInterface } from '../../Request/GetDeviceRequest';
import { Label } from '../../../Common/Components/Elements/Label';

export function UpdateDevice(props: {
    setDeviceData: (data: DeviceResponseInterface) => void;
    setRefreshNavbar?: (newValue: boolean) => void;
    deviceData: DeviceResponseInterface;
}) {
    const { deviceData, setDeviceData, setRefreshNavbar } = props;
    
    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
            deviceName: false,
            password: false,
            deviceGroup: false,
            deviceRoom: false,
    });

    const [deviceUpdateFormInputs, setDeviceUpdateFormInputs] = useState<UpdateDeviceFormInputsInterface|null>({
            deviceName: deviceData.deviceName,
            password: '',
            deviceGroup: deviceData.group.groupID,
            deviceGroupName: deviceData.group.groupName,
            deviceRoom: deviceData.room.roomName,
    });

    const originalDeviceData = useRef<UpdateDeviceFormInputsInterface>({
        deviceID: deviceData.deviceID,
        deviceName: deviceData.deviceName,
        password: '',
        deviceGroup: deviceData.group.groupID,
        deviceGroupName: deviceData.group.groupName,
        deviceRoom: deviceData.room.roomID,
    });

    const [userData, setUserData] = useState({
        userGroups: [] as GroupResponseInterface[],
        userRooms: [] as RoomResponseInterface[],
    });

    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const showAnnouncementFlash = (message: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={message}
                timer={timer ? timer : 40}
            />
        ])
    }

    useEffect(() => {
        if (deviceData.deviceID !== originalDeviceData.current.deviceID) {
            console.log('oops');
            setDeviceUpdateFormInputs({
                deviceName: deviceData.deviceName,
                password: '',
                deviceGroup: deviceData.group.groupID,
                deviceGroupName: deviceData.group.groupName,
                deviceRoom: deviceData.room.roomID,
            });
            originalDeviceData.current = {
                deviceID: deviceData.deviceID,
                deviceName: deviceData.deviceName,
                password: '',
                deviceGroup: deviceData.group.groupID,
                deviceGroupName: deviceData.group.groupName,
                deviceRoom: deviceData.room.roomID,
            };
        }

        if (activeFormForUpdating.deviceGroup === true) {
            handleUserGroupDataRequest();
        }
        if (activeFormForUpdating.deviceRoom === true) {
            getAllRoomRequest();
        }
    }, [activeFormForUpdating, deviceData.deviceID, announcementModals]);
    
    const toggleFormInput = (event: Event) => {
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;

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
        const groupDataResponse = await getAllUserGroupsRequest();
        if (groupDataResponse.status === 200) {
            const groupDataPayload = groupDataResponse.data.payload as UserDataResponseInterface;
            setUserData({
                ...userData,
                userGroups: groupDataPayload,
            })
        }
    }



    const sendUpdateDeviceRequest = async (e: Event) => {
        const name = (e.target as HTMLElement).dataset.name;

        let dataToSend: DeviceUpdatePatchInputInterface = {};

        let refreshNavData = false;
        switch (name) {
            case 'deviceName':
                dataToSend.deviceName = deviceUpdateFormInputs.deviceName;
                refreshNavData = true;
                break;
            case 'deviceGroup':
                dataToSend.deviceGroup = parseInt(deviceUpdateFormInputs.deviceGroup);
                break;
            case 'deviceRoom':
                dataToSend.deviceRoom = parseInt(deviceUpdateFormInputs.deviceRoom);
                break;
        }
        try {
            const deviceUpdateResponse = await deviceUpdatePatchRequest(deviceData.deviceID, dataToSend, 'full');

            if (deviceUpdateResponse.status === 202) {
                const deviceResponsePayload = deviceUpdateResponse.data.payload as DeviceResponseInterface;
                console.log('device update response', deviceUpdateResponse.data.payload);
                showAnnouncementFlash([deviceUpdateResponse.data.title], 'Success', 30);

                setDeviceUpdateFormInputs({
                    ...deviceUpdateFormInputs,
                    deviceName: deviceResponsePayload.deviceName,
                    deviceGroup: deviceResponsePayload.group.groupID,
                    deviceGroupName: deviceResponsePayload.group.groupName,
                    deviceRoom: deviceResponsePayload.room.roomID,
                });
                
                if (refreshNavData === true) {
                    setRefreshNavbar(true);
                }
                setDeviceData(deviceResponsePayload)
                // getDeviceData();
                // props.setRefreshNavDataFlag(true);
            }
        } catch(error) {
            const errorResponse = error.response as Error|AxiosError;
            
        }
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
                <>
                    <Label 
                        classes='form-inline font-size-1-5 hover padding-r-1 display-block-important'
                        text={'Device Group:'}
                    />
                    <DotCircleSpinner />
                </>
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
                selectDefaultValue={deviceData.group.groupID}
                declineDataName={'deviceGroup'}

            />
        )
    }

    const buildRoomWithSaveRejectButtons = () => {
        const optionsForBuilder = userData.userRooms.map((room: RoomResponseInterface) => {
            return {
                value: room.roomID,
                name: room.roomName,
            }
        });
        
        console.log('options for builder', optionsForBuilder)
        if (optionsForBuilder.length === 0) {
            return (
                <>
                    <Label 
                        classes='form-inline font-size-1-5 hover padding-r-1 display-block-important'
                        text={'Device Room:'}
                    />
                    <DotCircleSpinner />
                </>
            )
        }

        return (
            <FormInlineSelectWLabel
                labelName={'Device Room:'}
                changeEvent={handleUpdateDeviceInput}
                selectName={'deviceRoom'}
                selectOptions={optionsForBuilder}
                acceptClickEvent={(e: Event) => sendUpdateDeviceRequest(e)}
                declineClickEvent={(e: Event) => toggleFormInput(e)}
                selectDefaultValue={deviceData.room.roomID}
                declineDataName={'deviceRoom'}
            />
        )
    }


    return (
        <>
            {
                announcementModals.map((announcementErrorModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            { announcementErrorModal }
                        </React.Fragment>
                    );
                })
            }
                <div className="row">
                    <span className="large font-weight-bold form-inline font-size-1-5 padding-r-1">Device ID: {deviceData.deviceID}</span>
                </div>
                <form>
                    <div className="row" 
                    style={{paddingTop: "2%"}}
                    >
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
                                            spanInnerTag={deviceData.deviceName}
                                            clickEvent={(e: Event) => toggleFormInput(e)}
                                            dataName={'deviceName'}
                                        />
                            }
                        </div>
                        <div className="row" 
                        style={{paddingTop: "2%"}}
                        >
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
                    <div className="row" 
                    style={{paddingTop: "2%"}}
                    >
                        {
                            activeFormForUpdating.deviceRoom === true
                                ?
                                    buildRoomWithSaveRejectButtons()
                                :
                                    <FormInlineSpan
                                        spanOuterTag={'Device Room:'}
                                        spanInnerTag={deviceUpdateFormInputs.deviceRoom}
                                        clickEvent={(e: Event) => toggleFormInput(e)}
                                        dataName={'deviceRoom'}
                                    />
                        }
                    </div>
                    <button type="button" className="btn btn-danger btn-lg btn-block">Delete</button>
                </form>
        </>
    )
}
function getAllRoomRequest() {
    throw new Error('Function not implemented.');
}

