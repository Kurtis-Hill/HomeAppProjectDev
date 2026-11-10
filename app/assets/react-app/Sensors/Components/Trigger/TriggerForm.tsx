import * as React from 'react';
import { useState, useEffect } from 'react';
import GetSensorTriggerFormInterface from '../../Response/Trigger/GetSensorTriggerFormInterface';
import { getNewTriggerForm } from '../../Request/Trigger/GetNewTriggerForm';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { TriggerTypeResponseInterface } from '../../Response/Sensor/Trigger/TriggerTypeResponseInterface';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import RelayResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/RelayResponseInterface';
import { OperatorResponseInterface } from '../../../Common/Response/OperatorResponseInterface';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import { AddNewTriggerType } from '../../Request/Trigger/AddNewTriggerRequest';
import { DaysEnum } from '../../../Common/DaysEnum';
import { SensorDaysType as SensorTriggerDaysType } from '../../Types/SensorTriggerDaysType';

export type TriggerFormType = {
    operator: number;
    triggerType: number;
    baseReadingTypeThatTriggers: number;
    baseReadingTypeThatIsTriggered: number;
    days: SensorTriggerDaysType;
    valueThatTriggers: number|boolean|string;
    startTime: number|string|null;
    endTime: number|string|null;
    override: boolean;
};

/** Internal state shape — uses friendly field names + always-string valueThatTriggers */
type TriggerFormState = {
    sensorThatTriggers: number;
    sensorToBeTriggered: number;
    triggerType: number;
    valueThatTriggers: string;
    operator: number;
    startTime: string | null;
    endTime: string | null;
    days: SensorTriggerDaysType;
    override: boolean;
};

const DAYS: Array<{ key: keyof SensorTriggerDaysType; label: string }> = [
    { key: 'monday',    label: 'Mon' },
    { key: 'tuesday',   label: 'Tue' },
    { key: 'wednesday', label: 'Wed' },
    { key: 'thursday',  label: 'Thu' },
    { key: 'friday',    label: 'Fri' },
    { key: 'saturday',  label: 'Sat' },
    { key: 'sunday',    label: 'Sun' },
];

export default function TriggerForm(props: {
    closeForm: (value: boolean) => void,
    presets: TriggerFormType|null
    handleTriggerRequest: (e: Event, triggerRequest: AddNewTriggerType) => void,
    operation: string
}) {
    const { closeForm, operation, presets, handleTriggerRequest } = props;

    const [triggerFormInputs, setTriggerFormInputs] = useState<GetSensorTriggerFormInterface|null>(null);

    const [triggerRequest, setTriggerRequest] = useState<TriggerFormState>({
        sensorThatTriggers: 0,
        sensorToBeTriggered: 0,
        triggerType: 0,
        valueThatTriggers: '',
        operator: 0,
        startTime: null,
        endTime: null,
        days: {
            monday: true, tuesday: true, wednesday: true, thursday: true,
            friday: true, saturday: true, sunday: true
        },
        override: false,
    });

    useEffect(() => {
        handleGetAddNewTriggerFormRequest();
    }, []);

    const handleGetAddNewTriggerFormRequest = async () => {
        if (triggerFormInputs === null) {
            const addNewTriggerResponse = await getNewTriggerForm();
            if (addNewTriggerResponse.status === 200) {
                setTriggerFormInputs(addNewTriggerResponse.data.payload);
                if (presets !== null) {
                    setTriggerRequest((values: TriggerFormState) => ({
                        ...values,
                        sensorThatTriggers: presets.baseReadingTypeThatTriggers,
                        sensorToBeTriggered: presets.baseReadingTypeThatIsTriggered,
                        triggerType: presets.triggerType,
                        valueThatTriggers: presets.valueThatTriggers !== null && presets.valueThatTriggers !== undefined
                            ? String(presets.valueThatTriggers)
                            : '',
                        operator: presets.operator,
                        startTime: presets.startTime !== null ? String(presets.startTime) : null,
                        endTime: presets.endTime !== null ? String(presets.endTime) : null,
                        days: { ...presets.days },
                        override: presets.override,
                    }));
                } else {
                    setTriggerRequest((values: TriggerFormState) => ({
                        ...values,
                        triggerType: addNewTriggerResponse.data.payload.triggerTypes[0].triggerTypeID,
                        operator: addNewTriggerResponse.data.payload.operators[0].operatorID,
                    }));
                }
            }
        }
    }

    const handleSendNewTriggerRequest = async (e: Event) => {
        e.preventDefault();
        if (triggerRequest.sensorThatTriggers === 0 && triggerRequest.sensorToBeTriggered === 0) {
            alert('You must select a sensor that triggers or a sensor to be triggered');
            return;
        }

        const dayMap: Record<keyof SensorTriggerDaysType, DaysEnum> = {
            monday: DaysEnum.Monday, tuesday: DaysEnum.Tuesday, wednesday: DaysEnum.Wednesday,
            thursday: DaysEnum.Thursday, friday: DaysEnum.Friday, saturday: DaysEnum.Saturday,
            sunday: DaysEnum.Sunday,
        };
        const days: DaysEnum[] = DAYS
            .filter(({ key }) => triggerRequest.days[key])
            .map(({ key }) => dayMap[key]);

        // Convert the raw string value to the appropriate type before submitting
        let valueThatTriggers: number|boolean|string = triggerRequest.valueThatTriggers;
        if (typeof valueThatTriggers === 'string') {
            if (valueThatTriggers === 'true')  valueThatTriggers = true;
            else if (valueThatTriggers === 'false') valueThatTriggers = false;
            else {
                const num = parseFloat(valueThatTriggers);
                if (!isNaN(num)) valueThatTriggers = num;
            }
        }

        const triggerRequestData: AddNewTriggerType = {
            operator: triggerRequest.operator,
            triggerType: triggerRequest.triggerType,
            baseReadingTypeThatTriggers: triggerRequest.sensorThatTriggers !== 0 ? triggerRequest.sensorThatTriggers : null,
            baseReadingTypeThatIsTriggered: triggerRequest.sensorToBeTriggered !== 0 ? triggerRequest.sensorToBeTriggered : null,
            days,
            valueThatTriggers: valueThatTriggers as number | boolean,
            startTime: triggerRequest.startTime as (string | null),
            endTime: triggerRequest.endTime as (string | null),
        }
        handleTriggerRequest(e, triggerRequestData);
    }

    const handleChange = (event: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        const { name, value } = event.target;

        if (DAYS.some(d => d.key === name)) {
            setTriggerRequest(prev => ({ ...prev, days: { ...prev.days, [name]: !prev.days[name as keyof SensorTriggerDaysType] } }));
            return;
        }

        switch (name) {
            case 'valueThatTriggers':
                setTriggerRequest(prev => ({ ...prev, valueThatTriggers: value }));
                break;
            case 'sensorThatTriggers':
            case 'sensorToBeTriggered':
            case 'triggerType':
            case 'operator':
                setTriggerRequest(prev => ({ ...prev, [name]: parseInt(value) }));
                break;
            case 'startTime':
            case 'endTime':
                setTriggerRequest(prev => ({ ...prev, [name]: value || null }));
                break;
        }
    }

    const formatTime = (raw: number|string|null): string => {
        if (!raw) return '';
        const s = String(raw);
        if (s.length === 4 && !s.includes(':')) {
            return s.slice(0, 2) + ':' + s.slice(2);
        }
        return s;
    };

    if (triggerFormInputs === null) {
        return <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row hidden-scroll" />;
    }

    return (
        <form onSubmit={(e: React.FormEvent) => handleSendNewTriggerRequest(e as unknown as Event)}>
            <div className="trigger-form-grid">

                {/* Trigger Type */}
                <div className="trigger-form-field">
                    <label className="trigger-form-label" htmlFor="triggerType">Trigger Type</label>
                    <p className="trigger-form-hint">What kind of trigger do you want to create?</p>
                    <select
                        className="form-control"
                        name="triggerType"
                        id="triggerType"
                        value={triggerRequest.triggerType}
                        onChange={handleChange}
                    >
                        {triggerFormInputs.triggerTypes.map((t: TriggerTypeResponseInterface, i: number) => (
                            <option key={i} value={t.triggerTypeID}>{t.triggerTypeName}</option>
                        ))}
                    </select>
                </div>

                {/* Sensor that triggers */}
                <div className="trigger-form-field">
                    <label className="trigger-form-label" htmlFor="sensorThatTriggers">Sensor That Triggers</label>
                    <p className="trigger-form-hint">Sensor that fires the notification or relay event</p>
                    <select
                        className="form-control"
                        name="sensorThatTriggers"
                        id="sensorThatTriggers"
                        value={triggerRequest.sensorThatTriggers}
                        onChange={handleChange}
                    >
                        <option value={0}>— None —</option>
                        {triggerFormInputs.sensors.map((sensor: SensorResponseInterface) => {
                            const opts = [];
                            const rt = sensor.sensorReadingTypes;
                            if (rt.temperature) opts.push(<option key={rt.temperature.baseReadingTypeID}  value={rt.temperature.baseReadingTypeID}>{rt.temperature.sensor.sensorName} – temperature</option>);
                            if (rt.humidity)    opts.push(<option key={rt.humidity.baseReadingTypeID}     value={rt.humidity.baseReadingTypeID}>{rt.humidity.sensor.sensorName} – humidity</option>);
                            if (rt.relay)       opts.push(<option key={rt.relay.baseReadingTypeID}        value={rt.relay.baseReadingTypeID}>{rt.relay.sensor.sensorName} – relay</option>);
                            if (rt.analog)      opts.push(<option key={rt.analog.baseReadingTypeID}       value={rt.analog.baseReadingTypeID}>{rt.analog.sensor.sensorName} – analog</option>);
                            if (rt.latitude)    opts.push(<option key={rt.latitude.baseReadingTypeID}     value={rt.latitude.baseReadingTypeID}>{rt.latitude.sensor.sensorName} – latitude</option>);
                            if (rt.motion)      opts.push(<option key={rt.motion.baseReadingTypeID}       value={rt.motion.baseReadingTypeID}>{rt.motion.sensor.sensorName} – motion</option>);
                            return opts;
                        })}
                    </select>
                </div>

                {/* Sensor to be triggered */}
                <div className="trigger-form-field">
                    <label className="trigger-form-label" htmlFor="sensorToBeTriggered">Sensor To Be Triggered</label>
                    <p className="trigger-form-hint">Typically a relay that will be switched</p>
                    <select
                        className="form-control"
                        name="sensorToBeTriggered"
                        id="sensorToBeTriggered"
                        value={triggerRequest.sensorToBeTriggered}
                        onChange={handleChange}
                    >
                        <option value={0}>— None —</option>
                        {triggerFormInputs.relays.map((relay: RelayResponseInterface, i: number) => (
                            <option key={i} value={relay.baseReadingTypeID}>{relay.sensor?.sensorName} – relay</option>
                        ))}
                    </select>
                </div>

                {/* Value that triggers */}
                <div className="trigger-form-field">
                    <label className="trigger-form-label" htmlFor="valueThatTriggers">Trigger Value</label>
                    <p className="trigger-form-hint">Use <code>true</code>/<code>false</code> for relays, or a number for temperature/humidity</p>
                    <input
                        className="form-control"
                        type="text"
                        id="valueThatTriggers"
                        name="valueThatTriggers"
                        value={typeof triggerRequest.valueThatTriggers === 'boolean'
                            ? String(triggerRequest.valueThatTriggers)
                            : (triggerRequest.valueThatTriggers ?? '')}
                        onChange={handleChange}
                        placeholder="e.g. 25.5 or true"
                    />
                </div>

                {/* Operator */}
                <div className="trigger-form-field">
                    <label className="trigger-form-label" htmlFor="operator">Operator</label>
                    <p className="trigger-form-hint">How to compare the sensor reading against the trigger value</p>
                    <select
                        className="form-control"
                        name="operator"
                        id="operator"
                        value={triggerRequest.operator}
                        onChange={handleChange}
                    >
                        {triggerFormInputs.operators.map((op: OperatorResponseInterface, i: number) => (
                            <option key={i} value={op.operatorID}>{op.operatorSymbol}</option>
                        ))}
                    </select>
                </div>

                {/* Start / End time — side by side */}
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.75rem' }}>
                    <div className="trigger-form-field">
                        <label className="trigger-form-label" htmlFor="startTime">Start Time</label>
                        <input
                            className="form-control"
                            type="time"
                            id="startTime"
                            name="startTime"
                            value={formatTime(triggerRequest.startTime)}
                            onChange={handleChange}
                        />
                    </div>
                    <div className="trigger-form-field">
                        <label className="trigger-form-label" htmlFor="endTime">End Time</label>
                        <input
                            className="form-control"
                            type="time"
                            id="endTime"
                            name="endTime"
                            value={formatTime(triggerRequest.endTime)}
                            onChange={handleChange}
                        />
                    </div>
                </div>

                {/* Days */}
                <div className="trigger-form-field">
                    <label className="trigger-form-label">Active Days</label>
                    <div className="days-pill-row">
                        {DAYS.map(({ key, label }) => (
                            <label
                                key={key}
                                className={`day-pill${triggerRequest.days[key] ? ' active' : ''}`}
                            >
                                <input
                                    type="checkbox"
                                    name={key}
                                    checked={triggerRequest.days[key]}
                                    onChange={handleChange}
                                />
                                {label}
                            </label>
                        ))}
                    </div>
                </div>

                {/* Actions */}
                <div className="form-actions">
                    <SubmitButton
                        onClickFunction={(e) => handleSendNewTriggerRequest(e)}
                        type="submit"
                        text={`${operation} Trigger`}
                        name="trigger-submit"
                        action="submit"
                        classes="add-new-submit-button"
                    />
                    <CloseButton close={() => closeForm(false)} classes="modal-cancel-button" />
                </div>

            </div>
        </form>
    );
}
