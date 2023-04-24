import * as React from 'react';
import { useState, useMemo } from 'react';
import { useParams } from "react-router-dom";
import { NavigateFunction, useNavigate } from "react-router-dom";

import { getDeviceRequest, DeviceResponseInterface } from '../../Devices/Request/GetDeviceRequest';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';
import { UpdateDevice } from '../../Devices/Components/UpdateDevice/UpdateDevice';
import { AxiosError } from 'axios';
import { indexUrl } from '../../Common/URLs/CommonURLs';

export function DevicePage() {
    const params = useParams();

    const deviceID = params.deviceID;

    const [deviceData, setDeviceData] = useState<DeviceResponseInterface|null>(null);

    const [deviceLoading, setDeviceLoading] = useState<boolean>(true);

    const navigate: NavigateFunction = useNavigate();

    const getDeviceData = async () => {
        try {
            const getDeviceResponse = await getDeviceRequest(parseInt(deviceID), 'full');
            const deviceData: DeviceResponseInterface = getDeviceResponse.data.payload;
            setDeviceData(deviceData);
        } catch (error) {
            const err = error as AxiosError
            if (err.response.status === 404) {
                navigate(`${indexUrl}`)
            }
        }
        setDeviceLoading(false);
    }

    useMemo(() => {
        getDeviceData();
    }, [deviceID]);

    if (deviceLoading === true) {
        return <DotCircleSpinner spinnerSize={5} classes="center-spinner" />
    }

    return (
        <>
            <UpdateDevice
                deviceID={deviceData.deviceID}
                deviceName={deviceData.deviceName}
                group={deviceData.group}
                room={deviceData.room}
                roles={deviceData.roles}
            />
        </>
    );
}