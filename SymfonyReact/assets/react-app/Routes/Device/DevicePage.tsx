import * as React from 'react';
import { useState, useMemo } from 'react';
import { useParams } from "react-router-dom";
import { NavigateFunction, useNavigate } from "react-router-dom";
import { Context } from 'react';
import {  useOutletContext  } from "react-router-dom";

import { getDeviceRequest, DeviceResponseInterface } from '../../Devices/Request/GetDeviceRequest';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';
import { UpdateDevice } from '../../Devices/Components/UpdateDevice/UpdateDevice';
import { AxiosError } from 'axios';
import { indexUrl } from '../../Common/URLs/CommonURLs';
import { useMainIndicators } from '../../Common/Components/Pages/MainPageTop';
import { ResponseTypeFull } from '../../Common/API/APIResponseType';

export function DevicePage() {
    const { setRefreshNavbar } = useMainIndicators();

    const params = useParams();

    const deviceID: number = parseInt(params.deviceID);

    const [deviceData, setDeviceData] = useState<DeviceResponseInterface|null>(null);

    const [deviceLoading, setDeviceLoading] = useState<boolean>(true);

    const navigate: NavigateFunction = useNavigate();

    const getDeviceData = async () => {
        try {
            const getDeviceResponse = await getDeviceRequest(deviceID, ResponseTypeFull);
            const deviceData: DeviceResponseInterface = getDeviceResponse.data.payload;
            setDeviceData(deviceData);
        } catch (error) {
            const err = error as AxiosError
            if (err.response?.status === 404) {
                navigate(`${indexUrl}`)
            }
        }
        setDeviceLoading(false);
    }

    useMemo(() => {
        getDeviceData();
    }, [deviceID]);


    if (deviceLoading === true || deviceData === null) {
        return <DotCircleSpinner spinnerSize={5} classes="center-spinner" />
    }

    return (
        <>
            <div className="container" style={{ textAlign: "center", margin: "inherit"}}>
                <div className="btn-group btn-group-toggle" data-toggle="buttons">
                    <label className="btn btn-secondary active">
                        <input type="radio" name="options" id="option1" autoComplete="off" defaultChecked /> Device
                    </label>
                    <label className="btn btn-secondary">
                        <input type="radio" name="options" id="option2" autoComplete="off" /> Sensors
                    </label>
                    <label className="btn btn-secondary">
                        <input type="radio" name="options" id="option3" autoComplete="off" /> Commands
                    </label>
                </div>

                <UpdateDevice
                    setDeviceData={setDeviceData}
                    setRefreshNavbar={setRefreshNavbar}
                    deviceData={deviceData}
                />
            </div>
        </>
    );
}