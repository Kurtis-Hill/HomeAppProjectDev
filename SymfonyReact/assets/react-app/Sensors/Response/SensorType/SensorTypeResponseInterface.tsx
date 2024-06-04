import { SensorTypesEnum } from "../../../Enum/SensorTypesEnum";

export interface SensorTypeResponseInterface {
    sensorTypeID: number;
    sensorTypeName: SensorTypesEnum
    sensorTypeDescription: string
}