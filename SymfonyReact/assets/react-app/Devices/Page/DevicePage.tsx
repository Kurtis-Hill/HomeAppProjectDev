import * as React from 'react';
import { useState, useMemo } from 'react';
import { useParams } from "react-router-dom";
import { NavigateFunction, useNavigate } from "react-router-dom";
import { getDeviceRequest } from '../Request/GetDeviceRequest';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';
import { UpdateDevice } from '../Components/UpdateDevice/UpdateDevice';
import { AxiosError } from 'axios';
import { indexUrl } from '../../Common/URLs/CommonURLs';
import { useMainIndicators } from '../../Common/Components/Pages/MainPageTop';
import { ResponseTypeFull } from '../../Common/API/APIResponseType';
import { DeviceResponseInterface } from '../Response/DeviceResponseInterface';
import { TabSelector } from '../../Common/Components/TabSelector';
import { ViewSensorsPage } from '../../Sensors/Page/ViewSensorsPage';

export function DevicePage() {
    const tabOptions = ['Device', 'Sensors', 'Commands'];

    const { setRefreshNavbar } = useMainIndicators();

    const params = useParams();

    const deviceID: number = parseInt(params.deviceID);

    const [deviceData, setDeviceData] = useState<DeviceResponseInterface|null>(null);

    const [deviceLoading, setDeviceLoading] = useState<boolean>(true);

    const [currentTab, setCurrentTab] = useState<string>(tabOptions[0]);

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
                <TabSelector
                    options={tabOptions}
                    currentTab={currentTab}
                    setCurrentTab={setCurrentTab}
                />
                {
                    currentTab === tabOptions[0]
                        ? 
                            <UpdateDevice
                                setDeviceData={setDeviceData}
                                setRefreshNavbar={setRefreshNavbar}
                                deviceData={deviceData}
                            />
                        :
                            null
                }
                {
                    currentTab === tabOptions[1]
                        ?
                            <ViewSensorsPage deviceID={deviceID} sensorData={deviceData.sensorData} refreshData={getDeviceData} />
                        :
                            null
                }
                {
                    currentTab === tabOptions[2]
                        ?
                            <h1>Commands</h1>
                        :
                            null
                }
            </div>
        </>
    );
}