import * as React from 'react';
import { useState, useEffect } from 'react';
import {
    Outlet, useOutletContext
} from "react-router-dom";

import Navbar from "../../../UserInterface/Components/Navbar";

import { SensorDataContextProvider } from "../../../Sensors/DataProviders/SensorDataProvider";

import { UserDataContextProvider } from '../../../User/DataProviders/UserDataContextProvider';
import { ResponseComponent } from "../Response/ResponseComponent";
import { RequestInterceptor } from '../../Request/Axios/RequestInterceptor';

type ContextType = {
     setRefreshNavbar: (newValue: boolean) => void | null;
};

export function MainPageTop() {
    const [refreshNavbar, setRefreshNavbar] = useState<boolean>(true);
    
    const setRefreshNavDataFlag = (newValue: boolean) => {
        setRefreshNavbar(newValue);
    }

    useEffect(() => {
    }, [refreshNavbar]);
    
    return (
        <React.Fragment>
            <RequestInterceptor />
            <ResponseComponent refreshNavBar={setRefreshNavbar} />
            <div id="page-top">
                <div id="wrapper">
                    <UserDataContextProvider children={undefined}>
                        <SensorDataContextProvider children={undefined}>
                            <Navbar
                                refreshNavbar={refreshNavbar}
                                setRefreshNavDataFlag={setRefreshNavDataFlag}
                            />
                            <Outlet
                                context={
                                    {
                                        setRefreshNavbar
                                    }
                                }
                            />
                        </SensorDataContextProvider>
                    </UserDataContextProvider>
                </div>
            </div>
        </React.Fragment>
    );
}

export function useMainIndicators() {
    return useOutletContext<ContextType>();
}
