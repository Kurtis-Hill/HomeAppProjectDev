import * as React from 'react';
import { useState, useEffect } from 'react';

import { webappURL } from "../../Common/CommonURLs";

import RoomNavbarResponseInterfaceInterface from "../../Response/User/Navbar/Interfaces/RoomNavbarResponseInterface";
import DeviceNavbarResponseInterface from "../../Response/User/Navbar/Interfaces/DeviceNavbarResponseInterface";
import GroupNameNavbarResponseInterface from "../../Response/User/Navbar/Interfaces/GroupNameNavbarResponseInterface";

import NavBarResponseInterface from "../../Response/NavBar/NavBarResponseInterface";
import { NavbarListItemInterface } from "./Interfaces/NavbarItemInterfaces";
import NavbarListItem from './NavbarListItem'

import { BuildNavbarItem } from "./Builders/NavbarItemBuilder";

export default function NavbarViewOptionListElements(props: { navbarResponseData: NavBarResponseInterface }) {
    const [navbarViewOptionItems, setNavbarViewOptionItems] = useState<Array<typeof NavbarListItem>>([]);

    useEffect(() => {
        createNavListItems(props.navbarResponseData);
    }, [props.navbarResponseData]);

    const createNavListItems = (navbarResponseData: NavBarResponseInterface): void => {
        // console.log('nav response data', navbarResponseData);
        let builtNavItems: Array<typeof NavbarListItem> = [];

        const userRooms: Array<RoomNavbarResponseInterfaceInterface>|undefined = navbarResponseData.userRooms;
        if (userRooms !== undefined && userRooms.length > 0) {
            let navbarUserRoomsListItem: Array<NavbarListItemInterface> = [];
            for (let i = 0; i < userRooms.length; i++) {
                navbarUserRoomsListItem.push({
                    link: `${webappURL}room?room-id=${userRooms[i].roomID}`,
                    displayName: userRooms[i].roomName
                });
            }
            builtNavItems.push(
                BuildNavbarItem({
                    heading: "Rooms",
                    listLinks: navbarUserRoomsListItem,
                    icon: "person-booth",
                    createNewLink: `${webappURL}add-room`,
                    createNewText: "+Add New Room"
                })
            );
        }

        const userDevices: Array<DeviceNavbarResponseInterface>|undefined = navbarResponseData.devices;
        if (userDevices !== undefined && userDevices.length > 0) {
            let deviceListItem: Array<NavbarListItemInterface> = [];
            for (let i = 0; i < userDevices.length; i++) {
                deviceListItem.push({
                    link: `${webappURL}device?device-id=${userDevices[i].deviceNameID}&device-group=${userDevices[i].groupNameID}&device-room=${userDevices[i].roomID}&view=device${userDevices[i].deviceName}`,
                    displayName: userDevices[i].deviceName
                })
            }

            builtNavItems.push(
                BuildNavbarItem({
                    heading: "Devices",
                    listLinks: deviceListItem,
                    icon: "microchip",
                    createNewLink: `${webappURL}add-device`,
                    createNewText: "+Add New Device"
                })
            );
        }

        const userGroupNames: Array<GroupNameNavbarResponseInterface>|undefined = navbarResponseData.groupNames;
        if (userGroupNames !== undefined && userGroupNames.length > 0) {
            let groupListItem: Array<NavbarListItemInterface> = [];
            for (let i = 0; i < userGroupNames.length; i++) {
                groupListItem.push({
                     link: `${webappURL}group?group-id=${userGroupNames[i].groupNameID}`,
                    displayName: userGroupNames[i].groupName
                })
            }

            builtNavItems.push(
                BuildNavbarItem({
                    heading: "Groups",
                    listLinks: groupListItem,
                    icon: "users",
                    createNewLink: `${webappURL}add-group`,
                    createNewText: "+Add New Group"
                })
            );
        }

        setNavbarViewOptionItems(builtNavItems);
    }

    return (
        <React.Fragment>
            {
                navbarViewOptionItems.map((navbarItem: typeof NavbarListItem, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            {navbarItem}
                        </React.Fragment>
                    );
                })
            }
        </React.Fragment>
    );
}
