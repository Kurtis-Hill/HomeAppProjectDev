import * as React from 'react';

import { webappURL } from "../../Common/CommonURLs";

import { IndividualNavBarResponse, NavBarResponseInterface } from "../../Response/NavBar/NavBarResponseInterface";
import NavbarListItem from './NavbarListItem'

import { BuildNavbarItem } from "./Builders/NavbarItemBuilder";


export default function NavbarViewOptionListElements(props: {
    navbarResponseData: NavBarResponseInterface,
    showAddNewDeviceModalFlag: (show: boolean) => void,
}) {  
    const showAddNewDeviceModalFlag = props.showAddNewDeviceModalFlag;

    const createNavListItems = (navbarResponseData: NavBarResponseInterface): React => {
        const builtNavItems: Array<typeof NavbarListItem> = [];
        
        if (navbarResponseData.payload) {
            for (let i = 0; i < navbarResponseData.payload.length; i++) {
                const individualNavBarItem: IndividualNavBarResponse = navbarResponseData.payload[i];
                let showAddNewModalFlag: (show: boolean) => void|null = null;
                let addNewText: string = '+Add New';
                console.log('hi', individualNavBarItem);
                if (individualNavBarItem.itemName === 'devices') {
                    showAddNewModalFlag = showAddNewDeviceModalFlag;
                    addNewText = '+Add New Device';
                } 
                builtNavItems.push(
                    BuildNavbarItem({
                        heading: individualNavBarItem.header,
                        icon: individualNavBarItem.icon,
                        listLinks: individualNavBarItem.listItemLinks,
                        createNewText: addNewText,
                        errors: individualNavBarItem.errors,
                        flagAddNewModal: showAddNewModalFlag
                    })
                );
            }
        }

        // const userRooms: Array<RoomNavbarResponseInterfaceInterface>|undefined = navbarResponseData.userRooms;
        // if (userRooms !== undefined && userRooms.length > 0) {
        //     let navbarUserRoomsListItem: Array<NavbarListItemInterface> = [];
        //     for (let i = 0; i < userRooms.length; i++) {
        //         navbarUserRoomsListItem.push({
        //             link: `${webappURL}room?room-id=${userRooms[i].roomID}`,
        //             displayName: userRooms[i].roomName
        //         });
        //     }
        //     builtNavItems.push(
        //         BuildNavbarItem({
        //             heading: "View Rooms",
        //             listLinks: navbarUserRoomsListItem,
        //             icon: "person-booth",
        //             createNewLink: `${webappURL}add-room`,
        //             createNewText: "+Add New Room",
        //         })
        //     );
        // }
            
        //     const userDevices: Array<DeviceNavbarResponseInterface>|undefined = navbarResponseData.devices;
        //     if (userDevices !== undefined && userDevices.length > 0) {
        //         let deviceListItem: Array<NavbarListItemInterface> = [];
        //         for (let i = 0; i < userDevices.length; i++) {
        //             deviceListItem.push({
        //                 link: `${webappURL}device?device-id=${userDevices[i].deviceNameID}&device-group=${userDevices[i].groupNameID}&device-room=${userDevices[i].roomID}&view=device${userDevices[i].deviceName}`,
        //                 displayName: userDevices[i].deviceName
        //             })
        //         }

        //         builtNavItems.push(
        //             BuildNavbarItem({
        //                 heading: "View Devices",
        //                 listLinks: deviceListItem,
        //                 icon: "microchip",
        //                 createNewText: "+Add New Device",
        //                 flagAddNewModal: showAddNewDeviceModalFlag
        //             })
        //     );
        // }

        // const userGroupNames: Array<GroupNameNavbarResponseInterface>|undefined = navbarResponseData.groupNames;
        // if (userGroupNames !== undefined && userGroupNames.length > 0) {
        //     let groupListItem: Array<NavbarListItemInterface> = [];
        //     for (let i = 0; i < userGroupNames.length; i++) {
        //         groupListItem.push({
        //             link: `${webappURL}group?group-id=${userGroupNames[i].groupNameID}`,
        //             displayName: userGroupNames[i].groupName
        //         })
        //     }
            
        //     builtNavItems.push(
        //         BuildNavbarItem({
        //             heading: "View Groups",
        //             listLinks: groupListItem,
        //             icon: "users",
        //             createNewLink: `${webappURL}add-group`,
        //             createNewText: "+Add New Group",
        //         })
        //         );
        //     }
            
            return builtNavItems.map((item: typeof NavbarListItem, index: number) => {
                return (
                    <React.Fragment key={index}>
                        {item}
                    </React.Fragment>
                );
            });
        }
        
        return (
            <React.Fragment>
                { createNavListItems(props.navbarResponseData) }
            </React.Fragment>
        );
}
