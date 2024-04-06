import { ReadingTypesEnum } from "../../../../Enum/ReadingTypesEnum";
import { ConstRecordType } from "../../../Components/ReadingTypes/SensorReadingTypesOptionTypes";
import SensorResponseInterface from "../../Sensor/SensorResponseInterface";

export default interface MotionResponseInterface {
    boolID: number,
    baseReadingTypeID: number,
    constRecord: ConstRecordType,
    currentReading: boolean,
    expectedReading: boolean,
    requestedReading: boolean,
    readingType: ReadingTypesEnum.relay,
    sensor?: SensorResponseInterface,
    sensorType: string,
    updatedAt: string,
}
