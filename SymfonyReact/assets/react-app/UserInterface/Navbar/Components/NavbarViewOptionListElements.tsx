import * as React from 'react';
import { useState, useMemo } from 'react';

import { IndividualNavBarElement, NavBarResponseInterface } from "../Response/NavBarResponseInterface";
import NavbarListItem from './NavbarListItem'
import { BuildNavbarItem } from '../Builders/NavbarItemBuilder';
import BaseModal from '../../../Common/Components/Modals/BaseModal';
import { AddNewDevice } from '../../../Devices/Components/NewDevices/AddNewDevice';
import { AddNewRoom } from '../../../User/Components/Room/AddNewRoom';
import { checkAdmin } from '../../../Authentication/Session/UserSession';
import { AddNewGroup } from '../../../User/Components/Group/AddNewGroup';

export default function NavbarViewOptionListElements(props: {
    navbarResponseData: NavBarResponseInterface,
    setRefreshNavDataFlag: (newValue: boolean) => void,
}) {  
    const navbarResponseData = props.navbarResponseData

    const [showAddNewDeviceModal, setAddNewDeviceModal] = useState<boolean>(false);
    const [showAddNewRoomModal, setAddNewRoomModal] = useState<boolean>(false);
    const [showAddNewGroupModal, setAddNewGroupModal] = useState<boolean>(false);

    const navbarItems = useMemo(
        () => createNavListItems(props.navbarResponseData), 
        [navbarResponseData]
    );

    const setAddNewDeviceModalFlag = (show: boolean): void => {
        setAddNewDeviceModal(show);
    }

    const setAddNewRoomModalFlag = (show: boolean): void => {
        setAddNewRoomModal(show);
    }

    const setAddNewGroupModalFlag = (show: boolean): void => {
        setAddNewGroupModal(show);
    }

    function createNavListItems(navbarResponseData: NavBarResponseInterface): React {
        const builtNavItems: Array<typeof NavbarListItem> = [];
        
        if (navbarResponseData.payload) {
            for (let i = 0; i < navbarResponseData.payload.length; i++) {
                const individualNavBarItem: IndividualNavBarElement = navbarResponseData.payload[i];
                let showAddNewModalFlag: (show: boolean) => void|null = null;
                let addNewText: string = '+Add New';

                if (individualNavBarItem.itemName === 'devices') {
                    showAddNewModalFlag = setAddNewDeviceModal;
                    addNewText = '+Add New Device';
                } 
                if (individualNavBarItem.itemName === 'rooms' && checkAdmin()) {
                    showAddNewModalFlag = setAddNewRoomModal;
                    addNewText = '+Add New Room';
                } 
                if (individualNavBarItem.itemName === 'groups') {
                    showAddNewModalFlag = setAddNewGroupModal;
                    addNewText = '+Add New Group';
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
            { navbarItems }

            {/* {            
                showAddNewDeviceModal === true
                    ? */}
            <BaseModal 
                title={'Add New Device'}
                modalShow={showAddNewDeviceModal}
                setShowModal={setAddNewDeviceModalFlag}
                heightClasses="standard-modal-height"
            >
                <AddNewDevice
                    setAddNewDeviceModal={setAddNewDeviceModal}
                    setRefreshNavDataFlag={props.setRefreshNavDataFlag}
                />
            </BaseModal>
                    {/* :
                        null */}
            {/* } */}
            {
                showAddNewRoomModal === true
                    ?
                        <BaseModal 
                            title={'Add New Room'}
                            modalShow={showAddNewRoomModal}
                            setShowModal={setAddNewRoomModalFlag}
                            heightClasses="snap-modal-height"
                        >
                            <AddNewRoom
                                setAddNewRoomModal={setAddNewRoomModalFlag}
                                setRefreshNavDataFlag={props.setRefreshNavDataFlag}
                            />
                        </BaseModal>
                    :
                        null
            }


            <BaseModal
                title={'Add New Group'}
                modalShow={showAddNewGroupModal}
                setShowModal={setAddNewGroupModalFlag}
                heightClasses="snap-modal-height"
            >
                <AddNewGroup
                    setAddNewGroupModal={setAddNewGroupModalFlag}
                    setRefreshNavDataFlag={props.setRefreshNavDataFlag}
                />
            </BaseModal>

        </React.Fragment>
    );
}
