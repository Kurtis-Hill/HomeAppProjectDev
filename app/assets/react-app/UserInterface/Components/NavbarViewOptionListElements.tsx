import * as React from 'react';
import { useState } from 'react';

import { IndividualNavBarElement, NavBarResponseInterface } from "../Response/Navbar/NavBarResponseInterface";
import NavbarListItem from './NavbarListItem'
import BaseModal from '../../Common/Components/Modals/BaseModal';
import { AddNewDeviceForm } from '../../Devices/Components/NewDevices/AddNewDeviceForm';
import { AddNewRoomForm } from '../../User/Components/Room/AddNewRoomForm';
import { checkAdmin } from '../../Authentication/Session/UserSessionHelper';
import { AddNewGroupForm } from '../../User/Components/Group/AddNewGroupForm';
import TriggerForm from '../../Sensors/Components/Trigger/TriggerForm';
import { AddNewTriggerType, addNewTriggerForm } from '../../Sensors/Request/Trigger/AddNewTriggerRequest';

export default function NavbarViewOptionListElements(props: {
    navbarResponseData: NavBarResponseInterface,
    setRefreshNavDataFlag: (newValue: boolean) => void,
}) {  
    const navbarResponseData = props.navbarResponseData;

    const [showAddNewDeviceModal, setAddNewDeviceModal] = useState<boolean>(false);
    const [showAddNewRoomModal, setAddNewRoomModal] = useState<boolean>(false);
    const [showAddNewGroupModal, setAddNewGroupModal] = useState<boolean>(false);
    const [showAddNewTriggerModal, setAddNewTriggerModal] = useState<boolean>(false);
    const [activeNavIndex, setActiveNavIndex] = useState<number | null>(null);

    const handleNavToggle = (index: number): void => {
        setActiveNavIndex(prev => prev === index ? null : index);
    };

    const setAddNewDeviceModalFlag = (show: boolean): void => {
        setAddNewDeviceModal(show);
    }

    const setAddNewRoomModalFlag = (show: boolean): void => {
        setAddNewRoomModal(show);
    }

    const setAddNewGroupModalFlag = (show: boolean): void => {
        setAddNewGroupModal(show);
    }

    const setAddNewTriggerModalFlag = (show: boolean): void => {
        setAddNewTriggerModal(show);
    }

    const handleSendNewTriggerRequest = async (e: Event, triggerRequest: AddNewTriggerType) => {
        const response = await addNewTriggerForm(triggerRequest);
        if (response.status === 200) {
            setAddNewTriggerModal(false);
        }
    };

    return (
        <React.Fragment>
            {
                navbarResponseData.payload?.map((item: IndividualNavBarElement, index: number) => {
                    let showAddNewModalFlag: ((show: boolean) => void) | null = null;
                    let addNewText: string = '+Add New';

                    if (item.itemName === 'devices') {
                        showAddNewModalFlag = setAddNewDeviceModal;
                        addNewText = '+Add New Device';
                    }
                    if (item.itemName === 'rooms' && checkAdmin()) {
                        showAddNewModalFlag = setAddNewRoomModal;
                        addNewText = '+Add New Room';
                    }
                    if (item.itemName === 'groups') {
                        showAddNewModalFlag = setAddNewGroupModal;
                        addNewText = '+Add New Group';
                    }
                    if (item.itemName === 'triggers') {
                        showAddNewModalFlag = setAddNewTriggerModal;
                        addNewText = '+Add New Trigger';
                    }

                    return (
                        <React.Fragment key={index}>
                            <NavbarListItem
                                header={item.header}
                                icon={item.icon}
                                listLinks={item.listItemLinks}
                                flagAddNewModal={showAddNewModalFlag}
                                errors={item.errors}
                                createNewText={addNewText}
                                isOpen={activeNavIndex === index}
                                onToggle={() => handleNavToggle(index)}
                            />
                        </React.Fragment>
                    );
                })
            }
            <BaseModal
                title={'Add New Device'}
                modalShow={showAddNewDeviceModal}
                setShowModal={setAddNewDeviceModalFlag}
                heightClasses="standard-modal-height"
            >
                <AddNewDeviceForm
                    setAddNewDeviceModal={setAddNewDeviceModal}
                    setRefreshNavDataFlag={props.setRefreshNavDataFlag}
                />
            </BaseModal>
            {
                showAddNewRoomModal === true
                    ?
                        <BaseModal 
                            title={'Add New Room'}
                            modalShow={showAddNewRoomModal}
                            setShowModal={setAddNewRoomModalFlag}
                            heightClasses="snap-modal-height"
                        >
                            <AddNewRoomForm
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
                <AddNewGroupForm
                    setAddNewGroupModal={setAddNewGroupModalFlag}
                    setRefreshNavDataFlag={props.setRefreshNavDataFlag}
                />
            </BaseModal>

            {showAddNewTriggerModal && (
                <BaseModal
                    title={'Add New Trigger'}
                    modalShow={showAddNewTriggerModal}
                    setShowModal={setAddNewTriggerModalFlag}
                    heightClasses="standard-modal-height"
                >
                    <TriggerForm
                        closeForm={setAddNewTriggerModalFlag}
                        presets={null}
                        handleTriggerRequest={handleSendNewTriggerRequest}
                        operation='Add'
                    />
                </BaseModal>
            )}

        </React.Fragment>
    );
}
