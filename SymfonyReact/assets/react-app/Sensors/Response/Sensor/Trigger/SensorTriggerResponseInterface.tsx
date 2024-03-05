import { OperatorResponseInterface } from "../../../../Common/Response/OperatorResponseInterface";
import { TriggerTypeResponseInterface } from "./TriggerTypeResponseInterface";
import UserResponseInterface from '../../../../User/Response/UserResponseInterface';
import { SensorReadingTypeResponseInterface } from '../../ReadingTypes/SensorReadingTypeResponseInterfaces/SensorReadingTypeResponseInterface';
import SensorResponseInterface from "../SensorResponseInterface";
import MotionResponseInterface from "../../ReadingTypes/SensorReadingTypeResponseInterfaces/MotionResponseInterface";
import RelayResponseInterface from "../../ReadingTypes/SensorReadingTypeResponseInterfaces/RelayResponseInterface";
import TemperatureResponseInterface from "../../ReadingTypes/SensorReadingTypeResponseInterfaces/TemperatureResponseInterface";
import HumidityResponseInterface from "../../ReadingTypes/SensorReadingTypeResponseInterfaces/HumidityResponseInterface";
import LatitudeResponseInterface from "../../ReadingTypes/SensorReadingTypeResponseInterfaces/LatitudeResponseInterface";
import AnalogResponseInterface from "../../ReadingTypes/SensorReadingTypeResponseInterfaces/AnalogResponseInterface";

export interface SensorTriggerResponseInterface {
    operator: OperatorResponseInterface,
    triggerType: TriggerTypeResponseInterface,
    valueThatTriggers: boolean|number,
    createdBy: UserResponseInterface,
    startTime: string|null,
    endTime: string|null,
    createdAt: string,
    updatedAt: string,
    monday: boolean,
    tuesday: boolean,
    wednesday: boolean,
    thursday: boolean,
    friday: boolean,
    saturday: boolean,
    sunday: boolean,
    baseReadingTypeThatTriggers?: MotionResponseInterface|RelayResponseInterface|TemperatureResponseInterface|HumidityResponseInterface|LatitudeResponseInterface|AnalogResponseInterface,
    baseReadingTypeThatIsTriggered?: RelayResponseInterface,
}