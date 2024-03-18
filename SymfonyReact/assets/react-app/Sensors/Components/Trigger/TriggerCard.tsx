import * as React from 'react';

import { SensorTriggerResponseInterface } from '../../Response/Sensor/Trigger/SensorTriggerResponseInterface';
import { BaseCard } from '../../../UserInterface/Cards/Components/BaseCard';
import DeleteButton from '../../../Common/Components/Buttons/DeleteButton';

export default function TriggerCard(props: {
    sensorTriggerData: SensorTriggerResponseInterface,
    handleShowDeleteModal: (triggerID: number) => void,
}) {
    const { sensorTriggerData, handleShowDeleteModal } = props;

    return (
        <>
            <BaseCard loading={false} setCardLoading={() => false} setVariableToUpdate={() => false}>
                {
                    sensorTriggerData.baseReadingTypeThatTriggers
                        ? <span>Sensor that triggers: {sensorTriggerData.baseReadingTypeThatTriggers.sensor.sensorName}</span>
                        : null
                }
                <br />
                {
                    sensorTriggerData.baseReadingTypeThatIsTriggered
                        ? <span>Sensor that is triggered: {sensorTriggerData.baseReadingTypeThatIsTriggered.sensor.sensorName}</span>
                        : null
                }
                <br />
                <span>Value that triggers: {sensorTriggerData.valueThatTriggers}</span>
                <br />
                <span>Operator: {sensorTriggerData.operator.operatorSymbol}</span>
                <br />
                <span>Monday: {sensorTriggerData.monday === true ? 'true' : 'false'}</span>
                <br />
                <span>Tuesday: {sensorTriggerData.tuesday === true ? 'true' : 'false'}</span>
                <br />
                <span>Wednesday: {sensorTriggerData.wednesday === true ? 'true' : 'false'}</span>
                <br />
                <span>Thursday: {sensorTriggerData.thursday === true ? 'true' : 'false'}</span>
                <br />
                <span>Friday: {sensorTriggerData.friday === true ? 'true' : 'false'}</span>
                <br />
                <span>Saturday: {sensorTriggerData.saturday === true ? 'true' : 'false'}</span>
                <br />
                <span>Sunday: {sensorTriggerData.sunday === true ? 'true' : 'false'}</span>
                <br />
                <br />
                <DeleteButton clickFunction={() => handleShowDeleteModal(sensorTriggerData.sensorTriggerID)}></DeleteButton>
            </BaseCard>
        </>
    )
}