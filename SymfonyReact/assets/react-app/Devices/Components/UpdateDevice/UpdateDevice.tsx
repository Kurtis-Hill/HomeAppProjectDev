import * as React from 'react';
import { useState, useEffect, useRef } from 'react';

import RoomResponseInterface from '../../../User/Response/Room/RoomResponseInterface';
import { UpdateDeviceFormInputsInterface } from './UpdateDeviceFormInputsInterface';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { getAllUserGroupsRequest } from '../../../User/Request/Group/GetAllUserGroupsRequest'
import { getAllRoomRequest } from '../../../User/Request/Room/GetAllRoomRequest'
import { UserDataResponseInterface } from '../../../UserInterface/Navbar/Response/UserDataResponseInterface';
import { FormInlineSpan } from '../../../Common/Components/Elements/FormInlineSpan';
import { FormInlineSelectWLabel } from '../../../Common/Components/Selects/FormInlineSelectWLabel';
import { FormInlineInputWLabel } from '../../../Common/Components/Inputs/FormInlineInputWLabel';
import { deviceUpdatePatchRequest, DeviceUpdatePatchInputInterface } from '../../Request/DeviceUpdatePatchRequest';
import { AxiosError } from 'axios';
import { AnnouncementFlashModalBuilder } from '../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { AnnouncementFlashModal } from '../../../Common/Components/Modals/AnnouncementFlashModal';
import { Label } from '../../../Common/Components/Elements/Label';
import GroupResponseInterface from '../../../User/Response/Group/GroupResponseInterface';
import { DeleteDevice } from '../DeleteDevice/DeleteDevice';
import { DeviceResponseInterface } from '../../Response/DeviceResponseInterface';

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

    const [deviceUpdateFormInputs, setDeviceUpdateFormInputs] = useState<UpdateDeviceFormInputsInterface>({
        deviceName: deviceData.deviceName,
        password: '',
        deviceGroup: deviceData.group.groupID,
        deviceGroupName: deviceData.group.groupName,
        deviceRoom: deviceData.room.roomID,
        deviceRoomName: deviceData.room.roomName,
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
            setDeviceUpdateFormInputs({
                deviceName: deviceData.deviceName,
                password: '',
                deviceGroup: deviceData.group.groupID,
                deviceGroupName: deviceData.group.groupName,
                deviceRoom: deviceData.room.roomID,
                deviceRoomName: deviceData.room.roomName,
            });
            originalDeviceData.current = {
                deviceID: deviceData.deviceID,
                deviceName: deviceData.deviceName,
                password: '',
                deviceGroup: deviceData.group.groupID,
                deviceGroupName: deviceData.group.groupName,
                deviceRoom: deviceData.room.roomID,
                deviceRoomName: deviceData.room.roomName,
            };
        }

        if (activeFormForUpdating.deviceGroup === true) {
            handleUserGroupDataRequest();
        }
        if (activeFormForUpdating.deviceRoom === true) {
            handleGetAllRoomRequest();
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

        setDeviceUpdateFormInputs({
            ...deviceUpdateFormInputs,
            [name]: originalDeviceData.current[name],
        });
    }

    const handleGetAllRoomRequest = async () => {
        const roomRequestResponse = await getAllRoomRequest();
        if (roomRequestResponse.status === 200) {
            const roomDataPayload = roomRequestResponse.data.payload as RoomResponseInterface[];
            setUserData({
                ...userData,
                userRooms: roomDataPayload
            })
        }
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

    const handleUpdateDeviceInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setDeviceUpdateFormInputs({
            ...deviceUpdateFormInputs,
            [name]: value,
        });
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

            if (deviceUpdateResponse.status === 200) {
                const deviceResponsePayload = deviceUpdateResponse.data.payload as DeviceResponseInterface;
                console.log('device update response', deviceUpdateResponse.data.payload);
                showAnnouncementFlash([deviceUpdateResponse.data.title], 'Success', 30);

                setDeviceUpdateFormInputs({
                    ...deviceUpdateFormInputs,
                    deviceName: deviceResponsePayload.deviceName,
                    deviceGroup: deviceResponsePayload.group.groupID,
                    deviceGroupName: deviceResponsePayload.group.groupName,
                    deviceRoom: deviceResponsePayload.room.roomID,
                    deviceRoomName: deviceResponsePayload.room.roomName,
                });

                setActiveFormForUpdating({
                    ...activeFormForUpdating,
                    deviceName: false,
                    deviceGroup: false,
                    deviceRoom: false,
                });
                
                if (refreshNavData === true) {
                    setRefreshNavbar(true);
                }
                setDeviceData(deviceResponsePayload)
            }
        } catch(error) {
            const errorResponse = error.response as Error|AxiosError;
            showAnnouncementFlash([errorResponse.message], 'Error', 30);
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
                        classes={`form-inline font-size-1-5 ${deviceData.canEdit ? 'hover' : null }hover padding-r-1 display-block-important`}
                        text={'Device Group:'}
                    />
                    <DotCircleSpinner classes="center-spinner-inline"/>
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
        
        if (optionsForBuilder.length === 0) {
            return (
                <>
                    <Label 
                        classes={`form-inline font-size-1-5 ${deviceData.canEdit ? 'hover' : null } padding-r-1 display-block-important`}
                        text={'Device Room:'}
                    />
                    <DotCircleSpinner classes="center-spinner-inline" />
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
                announcementModals.map((announcementModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            { announcementModal }
                        </React.Fragment>
                    );
                })
            }
            <div className="row" style={{ paddingTop: '5vh' }}>
                <span className="large font-weight-bold form-inline font-size-1-5 padding-r-1">Device ID: {deviceData.deviceID}</span>
            </div>
            <form>
                <div className="row" 
                style={{paddingTop: "4%"}}
                >
                    { 
                        activeFormForUpdating.deviceName === true && deviceData.canEdit === true
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
                                    canEdit={deviceData.canEdit}
                                />
                    }
                </div>
                <div className="row" style={{paddingTop: "2%"}}>
                    {
                        activeFormForUpdating.deviceGroup === true && deviceData.canEdit === true
                            ? 
                                buildGroupWithSaveRejectButtons()
                            :
                                <FormInlineSpan
                                    spanOuterTag={'Device Group:'}
                                    spanInnerTag={deviceUpdateFormInputs.deviceGroupName}
                                    clickEvent={(e: Event) => toggleFormInput(e)}
                                    dataName={'deviceGroup'}
                                    canEdit={deviceData.canEdit}
                                />
                    } 
                </div>
                <div className="row" style={{paddingTop: "2%"}}>
                    {
                        activeFormForUpdating.deviceRoom === true && deviceData.canEdit === true
                            ?
                                buildRoomWithSaveRejectButtons()
                            :
                                <FormInlineSpan
                                    spanOuterTag={'Device Room:'}
                                    spanInnerTag={deviceUpdateFormInputs.deviceRoomName}
                                    clickEvent={(e: Event) => toggleFormInput(e)}
                                    dataName={'deviceRoom'}
                                    canEdit={deviceData.canEdit}
                                />
                    }
                </div>
                {
                    deviceData.canDelete === true
                        ?
                            <DeleteDevice
                                deviceID={deviceData.deviceID}
                                deviceName={deviceData.deviceName}
                            />
                        :
                            null
                }
            </form>
        </>
    )
}

