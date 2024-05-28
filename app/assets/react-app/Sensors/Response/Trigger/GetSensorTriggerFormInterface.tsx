import { OperatorResponseInterface } from '../../../Common/Response/OperatorResponseInterface';
import { TriggerTypeResponseInterface } from '../Sensor/Trigger/TriggerTypeResponseInterface';
import RelayResponseInterface from '../ReadingTypes/SensorReadingTypeResponseInterfaces/RelayResponseInterface';
import SensorResponseInterface from '../Sensor/SensorResponseInterface';

export default interface GetSensorTriggerFormInterface {
    operators: OperatorResponseInterface[];
    triggerTypes: TriggerTypeResponseInterface[];
    relays: RelayResponseInterface[];
    sensors: SensorResponseInterface[];
}