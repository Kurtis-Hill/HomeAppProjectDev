import * as React from 'react';
import { SensorReadingTypeResponseInterface } from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/SensorReadingTypeResponseInterface';
import { standardReading } from '../../../Common/SensorLanguage';
import { StandardReadingTypesDisplayTable } from './StandardReadingTypesDisplayTable';

export function ReadingTypeDisplayTable(props: {sensorReadingTypes: SensorReadingTypeResponseInterface, canEdit: boolean}) {
    const { sensorReadingTypes } = props;

    const sensorReadingTypesArray = Object.values(sensorReadingTypes);

    const standardReadingTypes = sensorReadingTypesArray.filter((readingType) => {
        return readingType.type === standardReading;
    });

    return (
        <>  
            {
                standardReadingTypes.length > 0
                    ? <StandardReadingTypesDisplayTable standardReadingTypes={standardReadingTypes} />
                    : null
            }
        </>
    )
}