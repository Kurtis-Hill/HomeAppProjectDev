import { ReadingTypesEnum } from '../../../Enum/ReadingTypesEnum';
import { ConstRecordType } from '../../../Types/SensorReadingTypesOptionTypes';
import SensorResponseInterface from '../../Sensor/SensorResponseInterface';

export default interface RelayResponseInterface {
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
