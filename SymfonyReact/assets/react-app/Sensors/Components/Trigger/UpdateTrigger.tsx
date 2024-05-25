import * as React from 'react';
import { useState, useEffect } from 'react';
import { SensorTriggerResponseInterface } from '../../Response/Sensor/Trigger/SensorTriggerResponseInterface';
import { getSensorTriggerTypesRequest } from '../../Request/Trigger/GetTriggerRequest'
import TriggerForm, { TriggerFormType } from './TriggerForm';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';

export default function UpdateTrigger(props: {
    setShowUpdateModal: (show: boolean) => void
    triggerID: number,
    resetData: () => void,
}) {
    const { setShowUpdateModal, triggerID, resetData } = props;

    const [triggerUpdateData, setTriggerUpdateData] = useState<TriggerFormType>(null); 

    const [loading, setLoading] = useState<boolean>(true);
    
    const handleGettingTriggerData = async (triggerID: number) => {
        const sensorTriggerResponseData = await getSensorTriggerTypesRequest(triggerID);

        if (sensorTriggerResponseData.status === 200) {
            const sensorTriggerData: SensorTriggerResponseInterface = sensorTriggerResponseData.data.payload;
            console.log('new', sensorTriggerData);

            setTriggerUpdateData({
                operator: sensorTriggerData.operator.operatorID,
                triggerType: sensorTriggerData.triggerType.triggerTypeID,
                baseReadingTypeThatTriggers: sensorTriggerData.baseReadingTypeThatTriggers.baseReadingTypeID,
                baseReadingTypeThatIsTriggered: sensorTriggerData.baseReadingTypeThatIsTriggered.baseReadingTypeID,
                days: sensorTriggerData.days,
                valueThatTriggers: sensorTriggerData.valueThatTriggers,
                startTime: sensorTriggerData.startTime,
                endTime: sensorTriggerData.endTime,
            })
            
            setLoading(false);
        }
        

    }

    useEffect(() => {
        handleGettingTriggerData(triggerID);
    }, []);

    if (loading === true) {
        return (
            <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row hidden-scroll" />
        );
    }

    return (
        <>
            <TriggerForm
                closeForm={setShowUpdateModal}
                presets={triggerUpdateData}
                resetData={resetData}
            />
            {/* <SubmitButton
                text={'Update Trigger'}
                classes={"modal-submit-button"}
                onClickFunction={() => console.log('update trigger')}
            />
            <CloseButton
                close={setShowUpdateModal}
                classes={"modal-cancel-button"}
            /> */}
        </>
    )
}
