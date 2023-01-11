import * as React from 'react';
import { useState, useEffect } from 'react';
import BaseModal from '../Modals/BaseModal';

export function AddNewDevice(props: {showAddNewDeviceModal: boolean; setAddNewDeviceModal: ((show: boolean) => void)}) {
    const showAddNewDeviceModal = props.showAddNewDeviceModal;
    const setAddNewDeviceModal = props.setAddNewDeviceModal;
    // const [showModal, setShowModal] = useState<boolean>(false);

    // const toggleShowModal = (show: boolean): void => {
    //     console.log('toggle', show);
    //     setShowModal(show);
    // }
    
    // const closeModal = () => {
    //     console.log('close!');
    //     setShowModal(false);
    // }

    // useEffect(() => {
    // }, [showModal])

    return (
        <>
        <h1>hiii</h1>
        </>
    )
}