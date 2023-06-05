import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import { AddSensorInputsInterface } from './AddSensorInputsInterface';

export function AddNewSensor(props: {deviceID: number}) {
    const newSensorFormInputs: AddSensorInputsInterface = {
        sensorName: '',
        deviceID: props.deviceID,
        sensorType: 0,
    };

    const [errors, setErrors] = useState<string[]>([]);


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
        </>
    )
}