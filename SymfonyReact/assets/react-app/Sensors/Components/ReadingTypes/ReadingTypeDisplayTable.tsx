import * as React from 'react';
import { SensorReadingTypeResponseInterface } from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/SensorReadingTypeResponseInterface';
import { standardReading, sensorType } from '../../../Common/SensorLanguage';
import { StandardReadingTypesDisplayTable } from './StandardReadingTypesDisplayTable';

export function ReadingTypeDisplayTable(props: {sensorReadingTypes: SensorReadingTypeResponseInterface, canEdit: boolean, refreshData?: () => void}) {
    const { sensorReadingTypes, canEdit, refreshData } = props;

    console.log('here', sensorReadingTypes)
    const sensorReadingTypesArray = Object.values(sensorReadingTypes);
    
    const standardReadingTypes = sensorReadingTypesArray.filter((readingType) => {
        return readingType.sensorType === standardReading;
    });

    // console.log('standardReadingTypes', sensorReadingTypes, sensorReadingTypesArray);
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