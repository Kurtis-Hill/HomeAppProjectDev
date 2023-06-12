import * as React from 'react';
import { useState } from 'react';
import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';
import SensorDataContext from '../../Contexts/SensorDataContext';
import { SensorDataContextDataInterface } from '../../DataProviders/SensorDataProvider';
import { SensorTypeResponseInterface } from '../../Response/SensorType/SensorTypeResponseInterface';
import { Label } from '../../../Common/Components/Elements/Label';
import { addNewSensorRequest } from '../../Request/Sensor/AddNewSensorRequest'
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';

export type NewSensorType = {
    sensorName: string,
    deviceID: number,
    sensorTypeID: number,
}

export function AddNewSensor(props: {deviceID: number, refreshData?: () => void; setShowModal?: (showModal: boolean) => void;}) {
    const { deviceID, setShowModal, refreshData } = props;

    const [errors, setErrors] = useState<string[]>([]);

    const [newSensorFormInputs, setNewSensorFormInputs] = useState<NewSensorType>({
        sensorName: '',
        deviceID,
        sensorTypeID: 0
    });

    const [responseLoading, setResponseLoading] = useState<boolean>(false);

    const handleAddNewSensorInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const valueInput = (event.target as HTMLInputElement).value;
        
        let value: any;
        
        if (name === 'sensorTypeID') {
            value = parseInt(valueInput);
        } else {
            value = valueInput;
        }
        
        setNewSensorFormInputs((values: NewSensorType) => ({...values, [name]: value}))

        console.log(newSensorFormInputs);
    }

    const validateInputs = (): boolean => {
        const errors: string[] = [];

        if (newSensorFormInputs.sensorName.length === 0) {
            errors.push('Sensor Name is required');
        }

        if (newSensorFormInputs.sensorTypeID === 0) {
            errors.push('Sensor Type is required');
        }

        setErrors(errors);
        return errors.length === 0;
    }

    const handleNewSensorFormSubmission = async (e: Event) => {
        e.preventDefault();

        const validationPassed: boolean = validateInputs();
        if (validationPassed === true) {
            setResponseLoading(true);
            try {                
                const newSensorsResponse = await addNewSensorRequest(newSensorFormInputs);
                if (newSensorsResponse.status === 201) {
                    setResponseLoading(false);
                    if (refreshData !== undefined) {
                        refreshData();
                    }
                    if (setShowModal !== undefined) {
                        setShowModal(false);
                    }                
                }
            } catch (e: any) {
                setResponseLoading(false);
            }
        }
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
            <form onSubmit={(e: Event) => {handleNewSensorFormSubmission(e)}}>
                <InputWLabel
                    labelName='Sensor Name'
                    name="sensorName"
                    value={newSensorFormInputs.sensorName}
                    onChangeFunction={handleAddNewSensorInput}
                />
                <Label
                    htmlFor='sensorTypeID'
                    text='Sensor Type'
                />
                <div className="form-group">
                    <select className="form-control" onChange={(e: Event) => handleAddNewSensorInput(e)} name="sensorTypeID">
                        <option value={0}>Select Sensor Type</option>
                        <SensorDataContext.Consumer>
                            {(sensorData: SensorDataContextDataInterface) => (
                                sensorData.sensorTypes.map((sensorType: SensorTypeResponseInterface, index:Number) => (
                                    <option value={sensorType.sensorTypeID} key={index}>{sensorType.sensorTypeName}</option>
                                ))                     
                            )}
                        </SensorDataContext.Consumer>
                    </select>
                </div>

                {
                    responseLoading === false
                        ?
                            <>
                                <SubmitButton
                                    text='Add New Sensor'
                                    onClickFunction={(e: Event) => handleNewSensorFormSubmission(e)}                    
                                />
                                {
                                    setShowModal !== undefined
                                        ?
                                            <CloseButton
                                                buttonText='Close'
                                                close={setShowModal}
                                            /> 
                                        :
                                            null
                                }                            
                            </>
                        : 
                            <DotCircleSpinner classes="center-spinner" />
                }
            </form>
        </>
    )
}