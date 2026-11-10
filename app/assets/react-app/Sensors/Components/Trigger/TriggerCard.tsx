import * as React from 'react';
import { SensorTriggerResponseInterface } from '../../Response/Sensor/Trigger/SensorTriggerResponseInterface';
import DeleteButton from '../../../Common/Components/Buttons/DeleteButton';
import { BaseCard } from '../../../Common/Components/BaseCard';

const DAYS: Array<{ key: string; label: string }> = [
    { key: 'monday', label: 'Mon' }, { key: 'tuesday', label: 'Tue' },
    { key: 'wednesday', label: 'Wed' }, { key: 'thursday', label: 'Thu' },
    { key: 'friday', label: 'Fri' }, { key: 'saturday', label: 'Sat' },
    { key: 'sunday', label: 'Sun' },
];

export default function TriggerCard(props: {
    sensorTriggerData: SensorTriggerResponseInterface,
    handleShowDeleteModal: (triggerID: number) => void,
    setTriggerToUpdate: (triggerID: number) => void
    setShowUpdateModal: (set: boolean) => void,
    showUpdateModal: boolean,
    id: number
}) {
    const { sensorTriggerData, handleShowDeleteModal, setShowUpdateModal, setTriggerToUpdate, id } = props;

    return (
        <BaseCard loading={false} setCardLoading={() => setShowUpdateModal(true)} setVariableToUpdate={() => setTriggerToUpdate(id)} id={id}>
            <div className="trigger-info-grid">
                {sensorTriggerData.baseReadingTypeThatTriggers && <>
                    <span className="trigger-info-label">Triggers from</span>
                    <span className="trigger-info-value">{sensorTriggerData.baseReadingTypeThatTriggers.sensor?.sensorName ?? '—'}</span>
                </>}
                {sensorTriggerData.baseReadingTypeThatIsTriggered && <>
                    <span className="trigger-info-label">Triggers</span>
                    <span className="trigger-info-value">{sensorTriggerData.baseReadingTypeThatIsTriggered.sensor?.sensorName ?? '—'}</span>
                </>}
                <span className="trigger-info-label">Type</span>
                <span className="trigger-info-value">{sensorTriggerData.triggerType.triggerTypeName}</span>

                <span className="trigger-info-label">Operator</span>
                <span className="trigger-info-value">{sensorTriggerData.operator.operatorSymbol}</span>

                <span className="trigger-info-label">Value</span>
                <span className="trigger-info-value">{String(sensorTriggerData.valueThatTriggers)}</span>

                {(sensorTriggerData.startTime || sensorTriggerData.endTime) && <>
                    <span className="trigger-info-label">Time window</span>
                    <span className="trigger-info-value">
                        {sensorTriggerData.startTime ?? '—'} → {sensorTriggerData.endTime ?? '—'}
                    </span>
                </>}
            </div>

            <div className="trigger-days-badges">
                {DAYS.map(({ key, label }) => (
                    <span
                        key={key}
                        className={`trigger-day-badge${sensorTriggerData.days[key] === false ? ' inactive' : ''}`}
                    >
                        {label}
                    </span>
                ))}
            </div>

            <div style={{ marginTop: '0.75rem' }} onClick={e => e.stopPropagation()}>
                <DeleteButton clickFunction={() => handleShowDeleteModal(sensorTriggerData.sensorTriggerID)} />
            </div>
        </BaseCard>
    );
}
