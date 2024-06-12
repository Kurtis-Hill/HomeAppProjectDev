import * as React from 'react';
import {
    IndividualSensorReadingTypeResponseInterface,
    SensorReadingTypeResponseInterface
} from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/SensorReadingTypeResponseInterface';
import { standardReading } from '../../../Sensors/SensorLanguage';
import { StandardReadingTypesDisplayTable } from './StandardReadingTypesDisplayTable';

export function ReadingTypeDisplayTable(props: {sensorReadingTypes: IndividualSensorReadingTypeResponseInterface, canEdit: boolean, refreshData?: () => void}) {
    const { sensorReadingTypes, canEdit, refreshData } = props;
    const sensorReadingTypesArray = Object.values(sensorReadingTypes);
    
    const standardReadingTypes = sensorReadingTypesArray.filter((readingType) => {
        return readingType.sensorType === standardReading;
    });

    return (
        <>  
            {
                standardReadingTypes.length > 0
                    ? <StandardReadingTypesDisplayTable standardReadingTypes={standardReadingTypes} canEdit={canEdit} refreshData={refreshData} />
                    : null
            }
        </>
    )
}
