import * as React from 'react';
import { useState, useEffect } from 'react';
import axios, {AxiosError, AxiosResponse} from 'axios';

import { checkAdmin } from "../../Authentication/Session/UserSessionHelper";

import { handleNavBarRequest } from "../Request/Navbar/NavBarRequest";
import { IndividualNavBarElement, NavBarResponseInterface } from "../Response/Navbar/NavBarResponseInterface";

import NavbarViewOptionListElements  from "./NavbarViewOptionListElements";

import HomeAppButton from "../../Common/Components/Buttons/HomeAppButton";

import { SidebarDividerWithHeading } from "../../Common/Components/Dividers/SidebarDividerWithHeading";
import DotCircleSpinner from "../../Common/Components/Spinners/DotCircleSpinner";
import { AnnouncementFlashModal } from '../../Common/Components/Modals/AnnouncementFlashModal';
import { AnnouncementFlashModalBuilder } from '../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import UserSettingsButton from '../../Common/Components/Buttons/UserSettingsButton';
import QueryButton from "../../Common/Components/Buttons/QueryButton";

export default function NavBar(props: {
    refreshNavbar: boolean,
    setRefreshNavDataFlag: (newValue: boolean) => void,
}) {
    const refreshNavbarIndicator = props.refreshNavbar;
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;

    const [navbarResponseData, setNavbarResponseData] = useState<NavBarResponseInterface>([]);
    const [loadingNavbarListItems, setLoadingNavbarListItems] = useState<boolean>(true);
    const [navbarToggleSizeSmall, setNavbarToggleSizeSmall] = useState<boolean>(false);

    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const [announcementCount, setAnnouncementCount] = useState<number>(0);
    
    const admin: boolean = checkAdmin();

    useEffect(() => {
        if (refreshNavbarIndicator === true) {
            requestNavbarData().then(() => {
                setRefreshNavDataFlag(false);
            });
        }
      }, [refreshNavbarIndicator]);

      const showAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            ...announcementModals,
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={errors}
                dataNumber={announcementCount}
                setErrorCount={setAnnouncementCount}
                timer={timer ? timer : 40}
            />
        ])
    }

    const requestNavbarData = async (): Promise<AxiosResponse> => {
        try {
            const navbarResponse: AxiosResponse = await handleNavBarRequest();
            const navbarResponseData: NavBarResponseInterface = navbarResponse.data;

            const triggersNavElement: IndividualNavBarElement = {
                header: 'Triggers',
                icon: 'bolt',
                itemName: 'Triggers',
                listItemLinks: [
                    {
                        displayName: 'View Triggers',
                        link: '/HomeApp/WebApp/sensors/triggers'
                    },
                ]
            }
            navbarResponseData.payload.push(triggersNavElement);

            setNavbarResponseData(navbarResponseData);
            setLoadingNavbarListItems(false);

            return navbarResponse;
        } catch (err) {
            const errors = err as Error | AxiosError;
            if (!axios.isAxiosError(errors) || !errors.response) {
                showAnnouncementFlash(
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
                <li className="nav-item">
                    <QueryButton />
                </li>
                <li className="nav-item">
                    <UserSettingsButton />
                </li>
                <SidebarDividerWithHeading heading="View options for:" />
                {
                    loadingNavbarListItems === true
                        ? <DotCircleSpinner classes="margin-spinner" />
                        : null
                }
                <NavbarViewOptionListElements 
                    navbarResponseData={navbarResponseData} 
                    setRefreshNavDataFlag={setRefreshNavDataFlag}
                />

                <hr className="sidebar-divider d-none d-md-block" />

                <div className="text-center d-none d-md-inline">
                    <button className="rounded-circle border-0" id="sidebarToggle" onClick={toggleNavbarSize}/>
                </div>
            </ul>
        </React.Fragment>
    );
}
