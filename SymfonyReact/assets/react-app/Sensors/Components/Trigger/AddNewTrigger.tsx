import * as React from 'react';
import { useState, useEffect } from 'react';
import GetSensorTriggerFormInterface from '../../Response/Trigger/GetSensorTriggerFormInterface';
import { getNewTriggerForm } from '../../Request/Trigger/AddNewTriggerForm';

export default function AddNewTrigger() {
    const [addNewTriggerForm, setAddNewTriggerForm] = useState<GetSensorTriggerFormInterface|null>(null);

    useEffect(() => {
        handleGetAdNewTriggerFormRequest();   
    });

    const handleGetAdNewTriggerFormRequest = async () => {
        if (addNewTriggerForm === null) {
            const addNewTriggerResponse = await getNewTriggerForm();
            if (addNewTriggerResponse.status === 200) {
                console.log('response', addNewTriggerResponse)
                setAddNewTriggerForm(addNewTriggerResponse.data.payload);
            }
        }
    }


    return (
        <>
            <h1>Add New Trigger</h1>
        </>
    )
}