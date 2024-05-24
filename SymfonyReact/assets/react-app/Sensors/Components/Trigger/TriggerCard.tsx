import * as React from 'react';
import { useState, useEffect } from 'react';

import { SensorTriggerResponseInterface } from '../../Response/Sensor/Trigger/SensorTriggerResponseInterface';
import { BaseCard } from '../../../UserInterface/Cards/Components/BaseCard';
import DeleteButton from '../../../Common/Components/Buttons/DeleteButton';

export default function TriggerCard(props: {
    sensorTriggerData: SensorTriggerResponseInterface,
    handleShowDeleteModal: (triggerID: number) => void,
    setTriggerToUpdate: (triggerID: number) => void
    setShowUpdateModal: (set: boolean) => void,
    showUpdateModal: boolean,
    id: number
}) {
    const { sensorTriggerData, handleShowDeleteModal, showUpdateModal, setShowUpdateModal, setTriggerToUpdate, id } = props;

    return (
        <>
            <BaseCard loading={false} setCardLoading={() => setShowUpdateModal(true)} setVariableToUpdate={() => setTriggerToUpdate(id)} id={id}>
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
                <span>Start time {sensorTriggerData.startTime}</span>
                <br />
                <span>End time {sensorTriggerData.endTime}</span>
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
