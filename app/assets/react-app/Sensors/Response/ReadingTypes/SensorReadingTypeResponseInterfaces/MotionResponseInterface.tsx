import { ReadingTypesEnum } from "../../../Enum/ReadingTypesEnum";
import { ConstRecordType } from "../../../Types/SensorReadingTypesOptionTypes";
import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

export default interface MotionResponseInterface {
    boolID: number,
    baseReadingTypeID: number,
    constRecord: ConstRecordType,
    currentReading: boolean,
    expectedReading: boolean,
    requestedReading: boolean,
    readingType: ReadingTypesEnum.motion,
    sensor?: SensorResponseInterface,
    sensorType: ReadingTypesEnum,
    updatedAt: string,
}
