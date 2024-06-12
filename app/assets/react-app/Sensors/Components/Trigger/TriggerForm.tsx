import * as React from 'react';
import { useState, useEffect } from 'react';
import GetSensorTriggerFormInterface from '../../Response/Trigger/GetSensorTriggerFormInterface';
import { getNewTriggerForm } from '../../Request/Trigger/GetNewTriggerForm';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { Label } from '../../../Common/Components/Elements/Label';
import { TriggerTypeResponseInterface } from '../../Response/Sensor/Trigger/TriggerTypeResponseInterface';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import RelayResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/RelayResponseInterface';
import Input from '../../../Common/Components/Inputs/Input';
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
    valueThatTriggers: number|boolean;
    startTime: number|null;
    endTime: number|null;
    override: boolean;
};

export default function TriggerForm(props: {
    closeForm: (value: boolean) => void, 
    presets: TriggerFormType|null
    handleTriggerRequest: (e: Event, triggerRequest: AddNewTriggerType) => void,
    operation: string
}) {
    const { closeForm, operation, presets, handleTriggerRequest } = props;

    const [triggerFormInputs, setTriggerFormInputs] = useState<GetSensorTriggerFormInterface|null>(null);

    const [triggerRequest, setTriggerRequest] = useState<TriggerFormType>({
        sensorThatTriggers: 0,
        sensorToBeTriggered: 0,
        triggerType: 0,
        valueThatTriggers: '',
        operator: 0,
        startTime: null,
        endTime: null,
        days: {
            monday: true,
            tuesday: true,
            wednesday: true,
            thursday: true,
            friday: true,
            saturday: true,
            sunday: true
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
                    setTriggerRequest((values: TriggerFormType) => ({
                        ...values,
                        sensorThatTriggers: presets.baseReadingTypeThatTriggers,
                        sensorToBeTriggered: presets.baseReadingTypeThatIsTriggered,
                        triggerType: presets.triggerType,
                        valueThatTriggers: presets.valueThatTriggers,
                        operator: presets.operator,
                        startTime: presets.startTime,
                        endTime: presets.endTime,
                        days: {
                            monday: presets.days.monday,
                            tuesday: presets.days.tuesday,
                            wednesday: presets.days.wednesday,
                            thursday: presets.days.thursday,
                            friday: presets.days.friday,
                            saturday: presets.days.saturday,
                            sunday: presets.days.sunday,
                        },
                        override: presets.override,
                    })); 
                } else {
                    setTriggerRequest((values: TriggerFormType) => ({
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

        //run through the days and remove any that are false
        let days = [];
        if (triggerRequest.days.monday === true) {
            days.push(DaysEnum.Monday);
        }
        if (triggerRequest.days.tuesday === true) {
            days.push(DaysEnum.Tuesday);
        }
        if (triggerRequest.days.wednesday === true) {
            days.push(DaysEnum.Wednesday);
        }
        if (triggerRequest.days.thursday === true) {
            days.push(DaysEnum.Thursday);
        }
        if (triggerRequest.days.friday === true) {
            days.push(DaysEnum.Friday);
        }
        if (triggerRequest.days.saturday === true) {
            days.push(DaysEnum.Saturday);
        }
        if (triggerRequest.days.sunday === true) {
            days.push(DaysEnum.Sunday);
        }

        const triggerRequestData: AddNewTriggerType = {
            operator: triggerRequest.operator,
            triggerType: triggerRequest.triggerType,
            baseReadingTypeThatTriggers: triggerRequest.sensorThatTriggers !== 0 ? triggerRequest.sensorThatTriggers : null,
            baseReadingTypeThatIsTriggered: triggerRequest.sensorToBeTriggered !== 0 ? triggerRequest.sensorToBeTriggered : null,
            days: days,
            valueThatTriggers: triggerRequest.valueThatTriggers,
            startTime: triggerRequest.startTime,
            endTime: triggerRequest.endTime,
        }
        handleTriggerRequest(e, triggerRequestData);
    }

    const handleAddNewTriggerInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        if (Object.values(DaysEnum).includes(name as DaysEnum)) {
            setTriggerRequest((values: AddNewTriggerType) => ({...values, days: {...values.days, [name]: !values.days[name]}}));
        }
        if (name === 'valueThatTriggers') {
            if (value === 'true') {
                setTriggerRequest((values: AddNewTriggerType) => ({...values, valueThatTriggers: true}));
            } else if (value === 'false') {
                setTriggerRequest((values: AddNewTriggerType) => ({...values, valueThatTriggers: false}));
            } else {
                setTriggerRequest((values: AddNewTriggerType) => ({...values, valueThatTriggers: parseFloat(value)}));
            }
        }
        if (name === 'sensorThatTriggers' || name === 'sensorToBeTriggered' || name === 'triggerType' || name === 'operator') {
            setTriggerRequest((values: AddNewTriggerType) => ({...values, [name]: parseInt(value)}));
        }
        if (name === 'startTime' || name === 'endTime') {
            setTriggerRequest((values: AddNewTriggerType) => ({...values, [name]: value}));
        }
    }

    if (triggerFormInputs === null) {
        return (
            <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row hidden-scroll" />
        );
    }

    return (
        <>
            <form>
                <Label text="Trigger Type" htmlFor="triggerType" />    
                <br />        
                <span>Select what kind of trigger you want to create</span>
                <select defaultValue={triggerRequest.triggerType !== 0 ? triggerRequest.triggerType : ''} className="form-control" name="triggerType" id="triggerType" onChange={handleAddNewTriggerInput}>
                    {
                        triggerFormInputs?.triggerTypes.map((triggerType: TriggerTypeResponseInterface, index: number) => {
                            return (
                                <option key={index} value={triggerType.triggerTypeID}>{triggerType.triggerTypeName}</option>
                            )
                        })                                            
                    }
                </select>
                <br />
                <Label text='Sensor that triggers' htmlFor='sensorThatTriggers' />
                <br />
                <span>Select a sensor that triggers the notification/relay switch event</span>
                <select defaultValue={triggerRequest !== null ? triggerRequest.sensorThatTriggers : ''} className="form-control" name='sensorThatTriggers' id='sensorThatTriggers' onChange={handleAddNewTriggerInput}>
                    <option value="0">No Sensor That Triggers</option>
                    {
                        triggerFormInputs?.sensors.map((sensor: SensorResponseInterface) => {
                            let htmlElements: HTMLElement[] = [];
                            if (sensor.sensorReadingTypes.temperature) {
                                htmlElements.push(<option key={sensor.sensorReadingTypes.temperature.baseReadingTypeID} value={sensor.sensorReadingTypes.temperature.baseReadingTypeID}>{sensor.sensorReadingTypes.temperature.sensor.sensorName} temperature</option>);
                            }
                            if (sensor.sensorReadingTypes.humidity) {
                                htmlElements.push(<option key={sensor.sensorReadingTypes.humidity.baseReadingTypeID} value={sensor.sensorReadingTypes.humidity.baseReadingTypeID}>{sensor.sensorReadingTypes.humidity.sensor.sensorName} humidity</option>);
                            }
                            if (sensor.sensorReadingTypes.relay) {
                                htmlElements.push(<option key={sensor.sensorReadingTypes.relay.baseReadingTypeID} value={sensor.sensorReadingTypes.relay.baseReadingTypeID}>{sensor.sensorReadingTypes.relay.sensor.sensorName} relay</option>);
                            }
                            if (sensor.sensorReadingTypes.analog) {
                                htmlElements.push(<option key={sensor.sensorReadingTypes.analog.baseReadingTypeID} value={sensor.sensorReadingTypes.analog.baseReadingTypeID}>{sensor.sensorReadingTypes.analog.sensor.sensorName} analog</option>);
                            }
                            if (sensor.sensorReadingTypes.latitude) {
                                htmlElements.push(<option key={sensor.sensorReadingTypes.latitude.baseReadingTypeID} value={sensor.sensorReadingTypes.latitude.baseReadingTypeID}>{sensor.sensorReadingTypes.latitude.sensor.sensorName} latitude</option>);
                            }
                            if (sensor.sensorReadingTypes.motion) {
                                htmlElements.push(<option key={sensor.sensorReadingTypes.motion.baseReadingTypeID} value={sensor.sensorReadingTypes.motion.baseReadingTypeID}>{sensor.sensorReadingTypes.motion.sensor.sensorName} motion</option>);
                            }
                           
                            return htmlElements;
                    })
                }
                </select>
                <br />
                <Label text='Sensor to be triggered' htmlFor='sensorToBeTriggered' />
                <br />
                <span>Select a sensor that will be triggered typically a Relay</span>
                <select defaultValue={triggerRequest !== null ? triggerRequest.sensorToBeTriggered : ''} className="form-control" name='sensorToBeTriggered' id='sensorToBeTriggered' onChange={handleAddNewTriggerInput}>
                    <option value="0">No Sensor To Be Triggered</option>
                    {
                        triggerFormInputs?.relays.map((relay: RelayResponseInterface, index) => {
                            return (
                                <option key={index} value={relay.baseReadingTypeID}>{relay.sensor?.sensorName} relay</option>
                            )
                        })
                    }
                </select>
                <br />
                <Label text='Value that triggers' htmlFor='valueThatTriggers' />
                <br />
                <span>Enter the value that triggers the notification/relay switch event this can be 'true/false' for relays or a number for temperature/humidity</span>
                <Input value={triggerRequest !== null ? triggerRequest.valueThatTriggers : ''} required={true} type='text' name='valueThatTriggers' onChangeFunction={handleAddNewTriggerInput} />
                <br />
                <Label text='Choose an operator' htmlFor='operator' />
                <br />
                <span>Select the operator that will be used to compare the value that triggers against the reading it receives</span>
                <select defaultValue={triggerRequest !== null ? triggerRequest.operator : ''} className="form-control" name='operator' id='operator' onChange={handleAddNewTriggerInput}>
                    {
                        triggerFormInputs?.operators.map((operator: OperatorResponseInterface, index) => {
                            return (
                                <option key={index} value={operator.operatorID}>{operator.operatorSymbol}</option>
                            )
                        })
                    }
                </select>
                <br />
                <Label text='Start time' htmlFor='startTime' />
                <br />
                <span>Select the time the trigger will start if nothing is selected the trigger will occur everytime the value triggers</span>
                <Input value={triggerRequest.startTime !== null ? triggerRequest.startTime.length === 4 ? triggerRequest.startTime.match(/.{2,2}/g).join(':') : triggerRequest.startTime : '' } type='time' name='startTime' onChangeFunction={handleAddNewTriggerInput} />
                <br />
                <Label text='End time' htmlFor='endTime' />
                <br />
                <span>Select the time the trigger will end if nothing is selected the trigger will occur everytime the value triggers</span>
                <Input value={triggerRequest.endTime !== null ? triggerRequest.endTime.length === 4 ? triggerRequest.endTime.match(/.{2,2}/g).join(':') : triggerRequest.endTime : ''} type='time' name='endTime' onChangeFunction={handleAddNewTriggerInput} />
                <br />
                <span>Select the days you wish the trigger to be active on</span>
                <br />
                <input checked={triggerRequest.days.monday} type='checkbox' name='monday' id='monday' onChange={(e: Event) => handleAddNewTriggerInput(e)} />
                <Label text='Monday' htmlFor='monday' />
                <br />
                <input checked={triggerRequest.days.tuesday} type='checkbox' name='tuesday' id='tuesday' onChange={(e: Event) => handleAddNewTriggerInput(e)} />
                <Label text='Tuesday' htmlFor='tuesday' />
                <br />
                <input checked={triggerRequest.days.wednesday} type='checkbox' name='wednesday' id='wednesday' onChange={(e: Event) => handleAddNewTriggerInput(e)} />
                <Label text='Wednesday' htmlFor='wednesday' />
                <br />
                <input checked={triggerRequest.days.thursday} type='checkbox' name='thursday' id='thursday' onChange={(e: Event) => handleAddNewTriggerInput(e)} />
                <Label text='Thursday' htmlFor='thursday' />
                <br />
                <input checked={triggerRequest.days.friday} type='checkbox' name='friday' id='friday' onChange={(e: Event) => handleAddNewTriggerInput(e)} />
                <Label text='Friday' htmlFor='friday' />
                <br />
                <input checked={triggerRequest.days.saturday} type='checkbox' name='saturday' id='saturday' onChange={(e: Event) => handleAddNewTriggerInput(e)} />
                <Label text='Saturday' htmlFor='saturday' />
                <br />
                <input checked={triggerRequest.days.sunday} type='checkbox' name='sunday' id='sunday' onChange={(e: Event) => handleAddNewTriggerInput(e)} />
                <Label text='Sunday' htmlFor='sunday' />
                <br />
                <input type={"radio"} checked={triggerRequest.override} name="override" id="override" onChange={(e: Event) => handleAddNewTriggerInput(e)} />
                <br />
                <SubmitButton onClickFunction={(e) => handleSendNewTriggerRequest(e)} type="submit" text={`${operation} Trigger`} name='add-trigger' action='submit' classes='add-new-submit-button' />
                <CloseButton close={() => closeForm(false)} classes={"modal-cancel-button"} />
            </form>        
        </>
    )
}
