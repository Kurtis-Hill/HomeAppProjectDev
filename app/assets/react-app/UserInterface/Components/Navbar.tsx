import * as React from 'react';
import { useState, useEffect, useContext, useMemo, JSX } from 'react';
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
import AdminButton from '../../Common/Components/Buttons/AdminButton';
import LogsButton from '../../Common/Components/Buttons/LogsButton';
import {QuickViewOptionsNavBarElement} from "./Navbar/QuickViewOptionsNavBarElement";
import SensorDataContext from '../../Sensors/Contexts/SensorDataContext';
import { SensorDataContextDataInterface } from '../../Sensors/DataProviders/SensorDataProvider';

export default function NavBar(props: {
    refreshNavbar: boolean,
    setRefreshNavDataFlag: (newValue: boolean) => void,
}) {
    const refreshNavbarIndicator = props.refreshNavbar;
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;

    const sensorCtx = useContext(SensorDataContext) as SensorDataContextDataInterface | null;

    const [baseNavbarData, setBaseNavbarData] = useState<NavBarResponseInterface>(null);
    const [loadingNavbarListItems, setLoadingNavbarListItems] = useState<boolean>(true);
    const [navbarToggleSizeSmall, setNavbarToggleSizeSmall] = useState<boolean>(false);

    const [announcementModals, setAnnouncementModals] = useState<JSX.Element[]>([]);
    const [announcementCount, setAnnouncementCount] = useState<number>(0);
    
    const admin: boolean = checkAdmin();

    // Build the full navbar every time base data or the sensor list changes
    const navbarResponseData: NavBarResponseInterface = useMemo(() => {
        if (!baseNavbarData) return { payload: [] } as NavBarResponseInterface;

        // Deep-clone payload so we don't mutate cached base data
        const payload: IndividualNavBarElement[] = baseNavbarData.payload.map(el => ({ ...el }));

        const sensorLinks = (sensorCtx?.allSensors ?? []).map(sensor => ({
            displayName: sensor.sensorName,
            link: `/WebApp/sensors/${sensor.sensorID}/triggers`,
        }));

        const triggersNavElement: IndividualNavBarElement = {
            header: 'Triggers',
            icon: 'bolt',
            itemName: 'triggers',
            listItemLinks: [
                { displayName: 'All Triggers', link: '/WebApp/sensors/triggers' },
                ...sensorLinks,
            ],
        };

        return { ...baseNavbarData, payload: [...payload, triggersNavElement] };
    }, [baseNavbarData, sensorCtx?.allSensors]);

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

            const queryNavElement: IndividualNavBarElement = {
                header: 'Query',
                icon: 'search',
                itemName: 'Query',
                listItemLinks: [
                    { displayName: 'Out of bounds', link: '/WebApp/query' },
                ]
            };
            navbarResponseData.payload.push(queryNavElement);

            // Store base data — triggers dropdown is built reactively via useMemo
            setBaseNavbarData(navbarResponseData);
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
                const data: NavBarResponseInterface = navbarResponse.data;
                setBaseNavbarData(data);
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
            <ul className={`navbar-nav bg-gradient-primary sidebar sidebar-dark accordion sidebar-modern ${navbarToggleSizeSmall === true ? 'toggled' : ''}`}>
                <HomeAppButton />
                <hr className="sidebar-divider my-0" />
                <li className="nav-item">
                    <UserSettingsButton />
                </li>
                {admin && (
                    <li className="nav-item">
                        <AdminButton />
                    </li>
                )}
                {admin && (
                    <li className="nav-item">
                        <LogsButton />
                    </li>
                )}
                <SidebarDividerWithHeading heading="View options for:" />
                {
                    loadingNavbarListItems === true
                        ? <DotCircleSpinner classes="margin-spinner" />
                        : null
                }
                {/*{*/}
                {/*    <QuickViewOptionsNavBarElement />*/}
                {/*}*/}
                <NavbarViewOptionListElements 
                    navbarResponseData={navbarResponseData} 
                    setRefreshNavDataFlag={setRefreshNavDataFlag}
                />

                <hr className="sidebar-divider d-none d-md-block" />

                <div className="text-center d-none d-md-inline">
                    <button
                        className="rounded-circle border-0 sidebar-toggle-btn"
                        id="sidebarToggle"
                        onClick={toggleNavbarSize}
                        title={navbarToggleSizeSmall ? 'Expand sidebar' : 'Collapse sidebar'}
                    />
                </div>
            </ul>
        </React.Fragment>
    );
}
