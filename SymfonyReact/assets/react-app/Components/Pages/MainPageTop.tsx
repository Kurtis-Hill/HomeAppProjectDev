import * as React from 'react';
import { useState, useEffect } from 'react';
import {
    Outlet,
} from "react-router-dom";

import Navbar from "../Navbar/Navbar";

import { AnnouncementFlashModal } from "../Modals/AnnouncementFlashModal";
import { BuildAnnouncementErrorFlashModal } from "../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";
import { ErrorResponseInterceptor } from "../../Request/Axios/ErrorResponseInterceptor";
import { RequestInterceptor } from "../../Request/Axios/RequestInterceptor";

import { SensorDataContextProvider } from "../SensorDataProvider/SensorDataProvider";

import { UserDataContextProvider } from '../../Components/UserDataProvider/UserDataContextProvider'; 

export function MainPageTop() {
    const [refreshNavbar, setRefreshNavbar] = useState<boolean>(true);

    const setRefreshNavDataFlag = (newValue: boolean) => {
        setRefreshNavbar(newValue);
    }

    const [errorAnnouncementErrorModals, setErrorAnnouncementErrorModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const showErrorAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        setErrorAnnouncementErrorModals([
            ...errorAnnouncementErrorModals,
            <BuildAnnouncementErrorFlashModal
                title={title}
                dataToList={errors}
                dataNumber={errorAnnouncementErrorModals.length}
                timer={timer ? timer : 40}
            />
        ])
    }

    console.log('too many times');
    // useEffect(() => {

    // }, [errorAnnouncementErrorModals])
    return (
        <React.Fragment>
            {
                errorAnnouncementErrorModals.map((errorAnnouncementErrorModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            {errorAnnouncementErrorModal}
                        </React.Fragment>
                    );
                })
            }
            <ErrorResponseInterceptor showErrorAnnouncementFlash={showErrorAnnouncementFlash} />
            <RequestInterceptor />
            <div id="page-top">
                <div id="wrapper">
                    <UserDataContextProvider children={undefined}>
                        <SensorDataContextProvider children={undefined}>
                            
                            <Navbar
                                refreshNavbar={refreshNavbar}
                                setRefreshNavDataFlag={setRefreshNavDataFlag}
                                showErrorAnnouncementFlash={showErrorAnnouncementFlash}
                            />
                            <Outlet
                                context={
                                    [
                                        setRefreshNavDataFlag,
                                        showErrorAnnouncementFlash
                                    ]
                                }
                            />
                        </SensorDataContextProvider>
                    </UserDataContextProvider>
                </div>
            </div>
        </React.Fragment>
    );
}
