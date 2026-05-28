import * as React from 'react';
import { useState, useEffect, useRef } from 'react';

import RoomResponseInterface from '../../../User/Response/Room/RoomResponseInterface';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { getAllUserGroupsRequest } from '../../../User/Request/Group/GetAllUserGroupsRequest'
import { getAllRoomRequest } from '../../../User/Request/Room/GetAllRoomRequest'
import { FormInlineSelectWLabel } from '../../../Common/Components/Selects/FormInlineSelectWLabel';
import { deviceUpdatePatchRequest, DeviceUpdatePatchInputInterface } from '../../Request/DeviceUpdatePatchRequest';
import { AxiosError } from 'axios';
import { AnnouncementFlashModalBuilder } from '../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { AnnouncementFlashModal } from '../../../Common/Components/Modals/AnnouncementFlashModal';
import GroupResponseInterface from '../../../User/Response/Group/GroupResponseInterface';
import { DeleteDeviceModal } from '../DeleteDevice/DeleteDeviceModal';
import { DeviceResponseInterface } from '../../Response/DeviceResponseInterface';
import { FormInlineInput } from '../../../Common/Components/Inputs/FormInlineUpdate';

type UpdateDeviceFormInputsType = {
    deviceID?: number;
    deviceName?: string;
    password?: string;
    deviceGroup?: number;
    deviceGroupName?: string;
    deviceRoom?: number;
    deviceRoomName?: string;
}

export function UpdateDeviceView(props: {
    setDeviceData: (data: DeviceResponseInterface) => void;
    setRefreshNavbar?: (newValue: boolean) => void;
    deviceData: DeviceResponseInterface;
}) {
    const { deviceData, setDeviceData, setRefreshNavbar } = props;

    const canEdit   = deviceData.canEdit   ?? false;
    const canDelete = deviceData.canDelete ?? false;

    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
        deviceName: false,
        password: false,
        deviceGroup: false,
        deviceRoom: false,
    });

    const [deviceUpdateFormInputs, setDeviceUpdateFormInputs] = useState<UpdateDeviceFormInputsType>({
        deviceName: deviceData.deviceName,
        password: '',
        deviceGroup: deviceData.group?.groupID,
        deviceGroupName: deviceData.group?.groupName,
        deviceRoom: deviceData.room?.roomID,
        deviceRoomName: deviceData.room?.roomName,
    });

    const originalDeviceData = useRef<UpdateDeviceFormInputsType>({
        deviceID: deviceData.deviceID,
        deviceName: deviceData.deviceName,
        password: '',
        deviceGroup: deviceData.group?.groupID,
        deviceGroupName: deviceData.group?.groupName,
        deviceRoom: deviceData.room?.roomID,
        deviceRoomName: deviceData.room?.roomName,
    });

    const [userData, setUserData] = useState({
        userGroups: [] as GroupResponseInterface[],
        userRooms: [] as RoomResponseInterface[],
    });

    const [announcementModals, setAnnouncementModals] = useState<React.JSX.Element[]>([]);

    const showAnnouncementFlash = (message: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={message}
                timer={timer ? timer : 40}
            />
        ]);
    };

    useEffect(() => {
        if (deviceData.deviceID === originalDeviceData.current.deviceID) {
            setDeviceUpdateFormInputs({
                deviceName: deviceData.deviceName,
                password: '',
                deviceGroup: deviceData.group?.groupID,
                deviceGroupName: deviceData.group?.groupName,
                deviceRoom: deviceData.room?.roomID,
                deviceRoomName: deviceData.room?.roomName,
            });
            originalDeviceData.current = {
                deviceID: deviceData.deviceID,
                deviceName: deviceData.deviceName,
                password: '',
                deviceGroup: deviceData.group?.groupID,
                deviceGroupName: deviceData.group?.groupName,
                deviceRoom: deviceData.room?.roomID,
                deviceRoomName: deviceData.room?.roomName,
            };
        }
        if (activeFormForUpdating.deviceGroup) handleUserGroupDataRequest();
        if (activeFormForUpdating.deviceRoom)  handleGetAllRoomRequest();
    }, [activeFormForUpdating, deviceData.deviceID, announcementModals]);

    const toggleFormInput = (name: string) => {
        setActiveFormForUpdating(prev => ({ ...prev, [name]: !prev[name] }));
        setDeviceUpdateFormInputs(prev => ({ ...prev, [name]: originalDeviceData.current[name] }));
    };

    /** Legacy event-based toggle kept for FormInlineSelectWLabel/FormInlineInputWLabel decline handlers */
    const toggleFormInputEvent = (event: Event) => {
        const name = (event.target as HTMLElement).dataset.name
            ?? (event.target as HTMLInputElement).name;
        toggleFormInput(name);
    };

    const handleGetAllRoomRequest = async () => {
        const res = await getAllRoomRequest();
        if (res.status === 200) {
            setUserData(prev => ({ ...prev, userRooms: res.data.payload as RoomResponseInterface[] }));
        }
    };

    const handleUserGroupDataRequest = async () => {
        const res = await getAllUserGroupsRequest();
        if (res.status === 200) {
            setUserData(prev => ({ ...prev, userGroups: res.data.payload as GroupResponseInterface[] }));
        }
    };

    const handleUpdateDeviceInput = (event: Event) => {
        const name  = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;
        setDeviceUpdateFormInputs(prev => ({ ...prev, [name]: value }));
    };

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
                dataToSend.deviceGroup = parseInt(String(deviceUpdateFormInputs.deviceGroup));
                break;
            case 'deviceRoom':
                dataToSend.deviceRoom = parseInt(String(deviceUpdateFormInputs.deviceRoom));
                break;
        }
        try {
            const res = await deviceUpdatePatchRequest(deviceData.deviceID, dataToSend, 'full');
            if (res.status === 200) {
                const payload = res.data.payload as DeviceResponseInterface;
                showAnnouncementFlash([res.data.title], 'Success');
                setDeviceUpdateFormInputs({
                    deviceName: payload.deviceName,
                    deviceGroup: payload.group?.groupID,
                    deviceGroupName: payload.group?.groupName,
                    deviceRoom: payload.room?.roomID,
                    deviceRoomName: payload.room?.roomName,
                });
                setActiveFormForUpdating(prev => ({ ...prev, deviceName: false, deviceGroup: false, deviceRoom: false }));
                if (refreshNavData) setRefreshNavbar?.(true);
                setDeviceData(payload);
            }
        } catch (error) {
            const errorResponse = (error as AxiosError).response as any;
            showAnnouncementFlash([errorResponse?.data?.message ?? 'Update failed'], 'Error', 30);
        }
    };

    /* ── Group select row ─────────────────────────────────────────── */
    const renderGroupField = () => {
        if (!activeFormForUpdating.deviceGroup || !canEdit) {
            return (
                <span
                    className={canEdit ? 'device-info-editable' : ''}
                    onClick={() => canEdit && toggleFormInput('deviceGroup')}
                    title={canEdit ? 'Click to edit' : undefined}
                >
                    {deviceUpdateFormInputs.deviceGroupName ?? '—'}
                </span>
            );
        }
        if (userData.userGroups.length === 0) return <DotCircleSpinner spinnerSize={1} />;
        return (
            <FormInlineSelectWLabel
                labelName=""
                changeEvent={handleUpdateDeviceInput}
                selectName="deviceGroup"
                selectOptions={userData.userGroups.map(g => ({ value: g.groupID, name: g.groupName }))}
                acceptClickEvent={(e: Event) => sendUpdateDeviceRequest(e)}
                declineClickEvent={(e: Event) => toggleFormInputEvent(e)}
                selectDefaultValue={deviceData.group?.groupID}
                declineDataName="deviceGroup"
            />
        );
    };

    /* ── Room select row ──────────────────────────────────────────── */
    const renderRoomField = () => {
        if (!activeFormForUpdating.deviceRoom || !canEdit) {
            return (
                <span
                    className={canEdit ? 'device-info-editable' : ''}
                    onClick={() => canEdit && toggleFormInput('deviceRoom')}
                    title={canEdit ? 'Click to edit' : undefined}
                >
                    {deviceUpdateFormInputs.deviceRoomName ?? '—'}
                </span>
            );
        }
        if (userData.userRooms.length === 0) return <DotCircleSpinner spinnerSize={1} />;
        return (
            <FormInlineSelectWLabel
                labelName=""
                changeEvent={handleUpdateDeviceInput}
                selectName="deviceRoom"
                selectOptions={userData.userRooms.map(r => ({ value: r.roomID, name: r.roomName }))}
                acceptClickEvent={(e: Event) => sendUpdateDeviceRequest(e)}
                declineClickEvent={(e: Event) => toggleFormInputEvent(e)}
                selectDefaultValue={deviceData.room?.roomID}
                declineDataName="deviceRoom"
            />
        );
    };

    /* ── Render ───────────────────────────────────────────────────── */
    return (
        <>
            {announcementModals.map((m: typeof AnnouncementFlashModal, i: number) => (
                <React.Fragment key={i}>{m}</React.Fragment>
            ))}

            <div className="device-view-card">

                {/* ── Header — name + delete ──────────────────── */}
                <div className="device-card-header">
                    <div className="device-card-name-area">
                        <i className="fas fa-server device-card-icon mr-2" />
                        {activeFormForUpdating.deviceName && canEdit ? (
                            <FormInlineInput
                                changeEvent={handleUpdateDeviceInput}
                                nameParam="deviceName"
                                value={deviceUpdateFormInputs.deviceName}
                                dataName="deviceName"
                                acceptClickEvent={(e: Event) => sendUpdateDeviceRequest(e)}
                                declineClickEvent={() => toggleFormInput('deviceName')}
                                extraClasses="device-name-inline-input"
                            />
                        ) : (
                            <h5
                                className={`device-card-name${canEdit ? ' hover' : ''}`}
                                onClick={() => canEdit && toggleFormInput('deviceName')}
                                title={canEdit ? 'Click to edit name' : undefined}
                            >
                                {deviceData.deviceName}
                            </h5>
                        )}
                    </div>
                    {canDelete && (
                        <div className="device-card-actions">
                            <DeleteDeviceModal
                                deviceID={deviceData.deviceID}
                                deviceName={deviceData.deviceName}
                            />
                        </div>
                    )}
                </div>

                {/* ── Body — info grid ────────────────────────── */}
                <div className="device-card-body">
                    <div className="device-info-grid">

                        <span className="device-info-label">Device ID</span>
                        <span className="device-info-value">
                            <span className="device-id-badge"># {deviceData.deviceID}</span>
                        </span>

                        <span className="device-info-label">Group</span>
                        <span className="device-info-value">{renderGroupField()}</span>

                        <span className="device-info-label">Room</span>
                        <span className="device-info-value">{renderRoomField()}</span>

                        {deviceData.ipAddress && <>
                            <span className="device-info-label">IP address</span>
                            <span className="device-info-value device-ip">{deviceData.ipAddress}</span>
                        </>}

                        {deviceData.externalIpAddress && <>
                            <span className="device-info-label">External IP</span>
                            <span className="device-info-value device-ip">{deviceData.externalIpAddress}</span>
                        </>}

                        {deviceData.createdBy && <>
                            <span className="device-info-label">Created by</span>
                            <span className="device-info-value">{deviceData.createdBy.email ?? '—'}</span>
                        </>}

                    </div>
                </div>
            </div>
        </>
    );
}
