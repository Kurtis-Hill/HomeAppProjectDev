import * as React from 'react';
import { useState, useEffect } from 'react';
import {
    Outlet, useOutletContext
} from "react-router-dom";

import Navbar from "../../../UserInterface/Navbar/Components/Navbar";

import { AnnouncementFlashModal } from "../Modals/AnnouncementFlashModal";
import { AnnouncementFlashModalBuilder } from "../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";
import { RequestInterceptor } from "../../Request/Axios/RequestInterceptor";

import { SensorDataContextProvider } from "../../../Sensors/DataProviders/SensorDataProvider";

import { UserDataContextProvider } from '../../../User/DataProviders/UserDataContextProvider';
import { ResponseComponent } from "../../Request/Interceptors/ResponseComponent";

type ContextType = {
     showAnnouncementFlash: (errors: Array<string>, title: string, timer?: number | null) => void | null;
     setRefreshNavbar: (newValue: boolean) => void | null;
};

export function MainPageTop() {
    const [refreshNavbar, setRefreshNavbar] = useState<boolean>(true);
    
    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const setRefreshNavDataFlag = (newValue: boolean) => {
        console.log('setRefreshNavDataFlag', newValue);
        setRefreshNavbar(newValue);
    }

    const [announcementCount, setAnnouncementCount] = useState<number>(0);

    const showAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            ...announcementModals,
            <AnnouncementFlashModalBuilder
                announcementModals={announcementModals}
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={errors}
                dataNumber={announcementCount}
                setErrorCount={setAnnouncementCount}
                timer={timer ? timer : 40}
            />
        ])
    }

    return (
        <React.Fragment>
            <RequestInterceptor />
            {
                announcementModals.map((errorAnnouncementErrorModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            {errorAnnouncementErrorModal}
                        </React.Fragment>
                    );
                })
            }
            <ResponseComponent showAnnouncementFlash={showAnnouncementFlash} announcementModals={announcementModals} />
            <div id="page-top">
                <div id="wrapper">
                    <UserDataContextProvider children={undefined}>
                        <SensorDataContextProvider children={undefined}>
                            <Navbar
                                refreshNavbar={refreshNavbar}
                                setRefreshNavDataFlag={setRefreshNavDataFlag}
                                showErrorAnnouncementFlash={showAnnouncementFlash}
                            />
                            <Outlet
                                context={
                                    {
                                        showAnnouncementFlash,
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
