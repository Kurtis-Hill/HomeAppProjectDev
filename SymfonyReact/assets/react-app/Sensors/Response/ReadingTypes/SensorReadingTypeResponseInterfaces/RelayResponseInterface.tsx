import { ConstRecordType } from '../../../Components/ReadingTypes/SensorReadingTypesOptionTypes';
import { ReadingTypesEnum } from '../../../../Enum/ReadingTypesEnum';
import SensorResponseInterface from '../../Sensor/SensorResponseInterface';

export default interface RelayResponseInterface {
    boolID: number,
    constRecord: ConstRecordType,
    currentReading: boolean,
    expectedReading: boolean,
    requestedReading: boolean,
    readingType: ReadingTypesEnum.relay,
    sensor?: SensorResponseInterface,
    sensorType: string,
    updatedAt: string,
}