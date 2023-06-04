import * as React from 'react';
import HumidityResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/HumidityResponseInterface';
import AnalogResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/AnalogResponseInterface';
import LatitudeResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/LatitudeResponseInterface';
import TemperatureResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/TemperatureResponseInterface';
import { GeneralTable } from '../../../Common/Components/Table/General/GeneralTable';
import { GeneralTableHeaders } from '../../../Common/Components/Table/General/GeneralTableHeaders';
import { GeneralTableRow } from '../../../Common/Components/Table/General/GeneralTableRow';
import { GeneralTableBody } from '../../../Common/Components/Table/General/GeneralTableBody';

export function StandardReadingTypesDisplayTable(props: {standardReadingTypes: Array<AnalogResponseInterface
|HumidityResponseInterface
|TemperatureResponseInterface
|LatitudeResponseInterface>}) {
    const { standardReadingTypes } = props;
    const sensorReadingTypesArray = Object.values(standardReadingTypes);
    return (
        <>
            <h2>Standard Reading</h2>
            <GeneralTable>
                <GeneralTableHeaders
                    headers={[
                        'Reading Type',
                        'High Reading',
                        'Low Reading',
                        'Constantly Record',
                    ]} />
                {sensorReadingTypesArray.map((readingType, index) => {
                    return (
                        <React.Fragment key={index}>
                            <GeneralTableBody>
                                <GeneralTableRow><span>{readingType.hasOwnProperty('analogID') ? 'Analog' : readingType.hasOwnProperty('humidityID') ? 'Humidity' : readingType.hasOwnProperty('temperatureID') ? 'Temperature' : readingType.hasOwnProperty('latitudeID') ? 'Latitude' : ''}</span></GeneralTableRow>
                                <GeneralTableRow><span>{readingType.highReading}</span></GeneralTableRow>
                                <GeneralTableRow><span>{readingType.lowReading}</span></GeneralTableRow>
                                <GeneralTableRow><span>{readingType.constRecord === true ? 'Yes' : 'No'}</span></GeneralTableRow>
                            </GeneralTableBody>     
                        </React.Fragment>
                    );
                })}
            </GeneralTable>
        </>
    );
}