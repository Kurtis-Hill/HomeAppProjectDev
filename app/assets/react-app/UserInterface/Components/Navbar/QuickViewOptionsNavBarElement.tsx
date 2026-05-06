import * as React from 'react';

import { useState, useEffect } from 'react';
import NavbarListItem from "../NavbarListItem";
import {getAllRoomRequest} from "../../../User/Request/Room/GetAllRoomRequest";
import RoomResponseInterface from "../../../User/Response/Room/RoomResponseInterface";
import {ListLinkItem} from "../../Response/Navbar/NavBarResponseInterface";
import {cardIndex} from "../../../Common/URLs/CommonURLs";
import {getAllDevicesRequest, getDeviceRequest} from "../../../Devices/Request/GetDeviceRequest";

export function QuickViewOptionsNavBarElement(props: {

}) {
    const [linkItems, setLinkItems] = useState<ListLinkItem[]>([]);

    const [roomList, setRoomList] = useState<ListLinkItem[]>([]);

    const handleRooms = async () => {
        const roomResponse = await getAllRoomRequest();
        if (roomResponse.status === 200) {
            const rooms: RoomResponseInterface[] = roomResponse.data.payload;
            let linkItems: ListLinkItem[] = [];

            rooms.forEach((room: RoomResponseInterface) => {
                linkItems.push({displayName: room.roomName, link: cardIndex(`room/${room.roomID}`)});
            });

            setRoomList(linkItems);
        }
    }

    const handleDevices = async () => {
        const deviceResponse = await getAllDevicesRequest();

        if (deviceResponse.status === 200) {
            const devices = deviceResponse.data.payload;
            let linkItems: ListLinkItem[] = [];

            devices.forEach((device) => {
                linkItems.push({displayName: device.deviceName, link: cardIndex(`device/${device.deviceID}`)});
            });

            setLinkItems(linkItems);
        }
    }

    useEffect(() => {
        handleRooms().then(r => {
            return r
        });
        handleDevices().then(r => {
            return r
        });
    }, []);
    return (
        <>
            <NavbarListItem
                header={'Quick View Rooms'}
                icon={'running'}
                listLinks={linkItems}
                createNewText={'new me'}
            />

            <NavbarListItem
                header={'Quick View Devices'}
                icon={'running'}
                listLinks={roomList}
                createNewText={'new me'}
            />
        </>
    )
}
