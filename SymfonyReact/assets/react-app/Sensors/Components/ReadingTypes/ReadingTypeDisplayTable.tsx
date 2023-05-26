import * as React from 'react';
import { SensorReadingTypeResponseInterface } from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/SensorReadingTypeResponseInterface';
import { GeneralTableHeaders } from '../../../Common/Components/Table/General/GeneralTableHeaders';
import { GeneralTable } from '../../../Common/Components/Table/General/GeneralTable';
import { GeneralTableBody } from '../../../Common/Components/Table/General/GeneralTableBody';
import { GeneralTableRow } from '../../../Common/Components/Table/General/GeneralTableRow';

export function ReadingTypeDisplayTable(props: {sensorReadingTypes: SensorReadingTypeResponseInterface}) {
    const { sensorReadingTypes } = props;

    const sensorReadingTypesArray = Object.values(sensorReadingTypes);

    return (
        <>  
            <GeneralTable>
                <GeneralTableHeaders
                    headers={[
                        'Reading Type',
                        'High Reading',
                        'Low Reading',
                        'Constantly Record',
                    ]}
                />
                    {                            
                        sensorReadingTypesArray.map((readingType, index) => {
                            return (
                                <>  
                                    <GeneralTableBody>
                                        <GeneralTableRow><span>{readingType.hasOwnProperty('analogID') ? 'Analog' : readingType.hasOwnProperty('humidityID') ? 'Humidity' : readingType.hasOwnProperty('temperatureID') ? 'Temperature' : readingType.hasOwnProperty('latitudeID') ? 'Latitude' : ''}</span></GeneralTableRow>
                                        <GeneralTableRow><span>{readingType.highReading}</span></GeneralTableRow>
                                        <GeneralTableRow><span>{readingType.lowReading}</span></GeneralTableRow>
                                        <GeneralTableRow><span>{readingType.constRecord === true ? 'Yes' : 'No'}</span></GeneralTableRow>
                                    </GeneralTableBody>                                                
                                </>
                            )
                        })
                    }
            </GeneralTable>
        </>
    )
}