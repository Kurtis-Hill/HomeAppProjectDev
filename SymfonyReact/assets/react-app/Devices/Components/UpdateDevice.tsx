import * as React from 'react';
import { useState, useEffect, useMemo } from 'react';
import GroupNameResponseInterface from '../../User/Response/GroupName/GroupNameResponseInterface';
import RoomResponseInterface from '../../User/Response/Room/RoomResponseInterface';
import InputWLabel from '../../Common/Components/Inputs/InputWLabel';

export function UpdateDevice(props: {
    deviceNameID: number;
    deviceName: string;
    groupName: GroupNameResponseInterface;
    room: RoomResponseInterface;
    roles: string[]
}) {

    const { deviceNameID, deviceName, groupName, room, roles } = props;
    
    const [deviceUpdateFormInputs, setDeviceUpdateFormInputs] = useState<UpdateDeviceFormInputsInterface|null>({
            deviceName: deviceName,
            password: '',
            deviceGroup: groupName.groupNameID,
            deviceRoom: room.roomID,
    });

    const handleGroupNameInput = (event: { target: { name: string; value: string; }; }) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setDeviceUpdateFormInputs({
            ...deviceUpdateFormInputs,
            [name]: value,
        });
    }

    return (
        <>
            <h1>Update Device</h1>
            <form>
                <InputWLabel
                    labelName="Device Name"
                    name="deviceName"
                    type="text"
                    value={deviceUpdateFormInputs.deviceName}
                    onChangeFunction={handleGroupNameInput}
                />
            </form>
        </>
    )
}

export interface UpdateDeviceFormInputsInterface {
    deviceName?: string;
    password?: string;
    deviceGroup?: number;
    deviceRoom?: number;
}