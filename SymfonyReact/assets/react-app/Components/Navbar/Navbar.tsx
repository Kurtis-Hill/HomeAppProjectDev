import * as React from 'react';
import { useState, useEffect, useMemo } from 'react';
import axios, {AxiosError, AxiosResponse} from 'axios';

import { checkAdmin } from "../../Session/UserSession";

import { handleNavBarRequest } from "../../Request/NavBar/NavBarRequest";
import { NavBarResponseInterface, IndividualNavBarResponse } from "../../Response/NavBar/NavBarResponseInterface";

import NavbarViewOptionListElements  from "./NavbarViewOptionListElements";

import HomeAppButton from "../Buttons/HomeAppButton"
import AdminButton from "../Buttons/AdminButton";

import { SidebarDividerWithHeading } from "../Dividers/SidebarDividerWithHeading";
import DotCircleSpinner from "../Spinners/DotCircleSpinner";
import { AddNewDevice } from '../Devices/AddNewDevice';
import BaseModal from '../Modals/BaseModal';

export default function NavBar(props: {
    refreshNavbar: boolean,
    setRefreshNavDataFlag: (newValue: boolean) => void,
    showErrorAnnouncementFlash: (errors: Array<string>, title: string, timer?: number|null) => void,
}) {
    console.log('cause ferfresh')
    const refreshNavbarIndicator = props.refreshNavbar;
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;
    const errorAnnouncementFlash = props.showErrorAnnouncementFlash;

    const [navbarResponseData, setNavbarResponseData] = useState<NavBarResponseInterface>([]);
    const [loadingNavbarListItems, setLoadingNavbarListItems] = useState<boolean>(true);
    const [navbarToggleSizeSmall, setNavbarToggleSizeSmall] = useState<boolean>(false);
    const [showAddNewDeviceModal, setAddNewDeviceModal] = useState<boolean>(false);

    const admin: boolean = checkAdmin();

    const setAddNewDeviceModalFlag = (show: boolean): void => {
        setAddNewDeviceModal(show);
    }

    useEffect(() => {
        if (refreshNavbarIndicator === true) {
            requestNavbarData().then(r => {
                setRefreshNavDataFlag(false);
            });
        }
      }, [refreshNavbarIndicator]);

    const requestNavbarData = async (): Promise<AxiosResponse> => {
        try {
            const navbarResponse: AxiosResponse = await handleNavBarRequest();
            const navbarResponseData: NavBarResponseInterface = navbarResponse.data;

            setNavbarResponseData(navbarResponseData);
            setLoadingNavbarListItems(false);

            return navbarResponse;
        } catch (err) {
            const errors = err as Error | AxiosError;
            if (!axios.isAxiosError(errors) || !errors.response) {
                errorAnnouncementFlash(
                    [`Something went wrong, please try refresh the browser or log out and back in again`],
                    'Unrecognized Error'
                );
            }

            const axiosError: AxiosError = errors as AxiosError;
            const errorStatusCode: number = axiosError.response.status;
            if (errorStatusCode === 401 || err.status === 403) {
                const navbarResponse: AxiosResponse = await handleNavBarRequest();
                const navbarResponseData: NavBarResponseInterface = navbarResponse.data;
    
                setNavbarResponseData(navbarResponseData);
                setLoadingNavbarListItems(false);
            }
            setLoadingNavbarListItems(false);
        }
    }

    const toggleNavbarSize = () => {
        setNavbarToggleSizeSmall(!navbarToggleSizeSmall);
    }

    return (
        <React.Fragment>
            <ul className={`navbar-nav bg-gradient-primary sidebar sidebar-dark accordion ${navbarToggleSizeSmall === true ? 'toggled' : ''}` }>
                <HomeAppButton />
                <hr className="sidebar-divider my-0" />
                {
                    admin === true
                        ? 
                            <li className="nav-item">
                                <AdminButton />
                            </li>
                        : null
                }
                <SidebarDividerWithHeading heading="View options for:" />

                {
                    loadingNavbarListItems === true
                        ? <DotCircleSpinner classes="margin-spinner" />
                        : null
                }
                <NavbarViewOptionListElements navbarResponseData={navbarResponseData} showAddNewDeviceModalFlag={setAddNewDeviceModalFlag} />

                    <BaseModal 
                        title={'Add New Device'}
                        modalShow={showAddNewDeviceModal}
                        setShowModal={setAddNewDeviceModalFlag}
                        heightClasses="standard-modal-height"

                    >
                        <AddNewDevice
                            showAddNewDeviceModal={showAddNewDeviceModal}                    
                            setAddNewDeviceModal={setAddNewDeviceModal}
                        
                        />
                    </BaseModal>
                <hr className="sidebar-divider d-none d-md-block" />

                <div className="text-center d-none d-md-inline">
                    <button className="rounded-circle border-0" id="sidebarToggle" onClick={toggleNavbarSize}/>
                </div>
            </ul>
        </React.Fragment>
    );
}
