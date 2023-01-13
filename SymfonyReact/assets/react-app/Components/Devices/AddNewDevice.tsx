import * as React from 'react';
import { useState, useEffect } from 'react';
import BaseModal from '../Modals/BaseModal';

import AddNewDeviceUserInputsInterface from './AddNewDeviceUserInputsInterface';

import InputWLabel from '../Form/Inputs/InputWLabel';
import SensorDataContext from '../../Contexts/SensorData/SensorDataContext';

export function AddNewDevice(props: {
    showAddNewDeviceModal: boolean; 
    setAddNewDeviceModal: ((show: boolean) => void);
}) {
    const showAddNewDeviceModal = props.showAddNewDeviceModal;
    const setAddNewDeviceModal = props.setAddNewDeviceModal;

    const [errors, setErrors] = useState<string[]>([]);

    const [addNewDeviceUserInputs, setAddNewDeviceUserInputs] = useState<AddNewDeviceUserInputsInterface>({
        deviceName: '',
        devicePassword: '',
        devicePasswordConfirm: '',
        deviceGroup: 0,
        deviceRoom: 0
    });
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

    const handleAddNewDeviceInput = (event: { target: { name: string; value: string; }; }) => {
        const name: string = event.target.name;
        const value: string = event.target.value;
        setAddNewDeviceUserInputs((values: AddNewDeviceUserInputsInterface) => ({...values, [name]: value}))
    }

    const handleNewDeviceFormSubmission = (e: Event) => {
        e.preventDefault();

        const jsonFormData = {
            'deviceName' : addNewDeviceUserInputs.deviceName,
            'devicePassword' : addNewDeviceUserInputs.devicePassword,
            'devicePasswordCheck' : addNewDeviceUserInputs.devicePasswordConfirm,
            'deviceRoom' :  parseInt(addNewDeviceUserInputs.deviceRoom),
            'deviceGroup' :  parseInt(addNewDeviceUserInputs.deviceGroup),
        };   
    }

    return (
        <>
            {
                errors.length > 0 
                    ?
                        <div className="error-container">
                            <div className="form-modal-error-box">
                                <ol>
                                    {errors.map((error: string, index: number) => (
                                        <li key={index} className="form-modal-error-text">{Object.keys(error).length === 0 ? 'Something has gone wrong' : error}</li>
                                    ))}
                                </ol>
                            </div>
                        </div>
                    : null
            }
            <form onSubmit={(e: Event) => {handleNewDeviceFormSubmission(e)}} id="modal-form">
                <InputWLabel
                    labelName='Device Name'
                    name='deviceName'
                    value={addNewDeviceUserInputs.deviceName}
                    onChangeFunction={handleAddNewDeviceInput}
                />

                <InputWLabel
                    labelName='Device Password'
                    name='deviceName'
                    value={addNewDeviceUserInputs.devicePassword}
                    onChangeFunction={handleAddNewDeviceInput}
                    type="password"
                    />

                <InputWLabel
                    labelName='Retype Device Password'
                    name='deviceName'
                    value={addNewDeviceUserInputs.devicePasswordConfirmed}
                    onChangeFunction={handleAddNewDeviceInput}
                    type="password"
                />

                {/* <SensorDataContext.Consumer>
                    {({ sensorData }) => (
                        <>
                            <div className="form-group">
                                <label htmlFor="deviceGroup">Device Group</label>
                </SensorDataContext.Consumer> */}
            </form>
        </>
    )
}