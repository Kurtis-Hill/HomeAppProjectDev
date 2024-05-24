import * as React from 'react';
import { useState, useEffect } from 'react';
import CloseButton from "../../../Common/Components/Buttons/CloseButton";
import SubmitButton from "../../../Common/Components/Buttons/SubmitButton";
import { SensorTriggerResponseInterface } from '../../Response/Sensor/Trigger/SensorTriggerResponseInterface';
import { getSensorTriggerTypesRequest } from '../../Request/Trigger/GetTriggerRequest'

export default function UpdateTrigger(props: {
    setShowUpdateModal: (show: boolean) => void
    triggerID: number
}) {
    const { setShowUpdateModal, triggerID } = props;

    
    const handleGettingTriggerData = async (triggerID: number) => {
        const sensorTriggerResponseData = await getSensorTriggerTypesRequest(triggerID);

        const sensorTriggerData: SensorTriggerResponseInterface = sensorTriggerResponseData.data.payload;
        
        console.log('new', sensorTriggerData);

    }

    useEffect(() => {
        handleGettingTriggerData(triggerID);
    }, []);

    

    console.log('triggerID', triggerID);
    return (
        <>
            <h2>Update Trigger</h2>

            <SubmitButton
                text={'Update Trigger'}
                classes={"modal-submit-button"}
                onClickFunction={() => console.log('update trigger')}
            />
            <CloseButton
                close={setShowUpdateModal}
                classes={"modal-cancel-button"}
            />
        </>
    )
}
