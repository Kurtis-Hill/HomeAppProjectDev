import * as React from 'react';
import { useState, useEffect, useRef } from 'react';

import HumidityResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/HumidityResponseInterface';
import AnalogResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/AnalogResponseInterface';
import LatitudeResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/LatitudeResponseInterface';
import TemperatureResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/TemperatureResponseInterface';
import { GeneralTable } from '../../../Common/Components/Table/General/GeneralTable';
import { GeneralTableHeaders } from '../../../Common/Components/Table/General/GeneralTableHeaders';
import { GeneralTableRow } from '../../../Common/Components/Table/General/GeneralTableRow';
import { GeneralTableBody } from '../../../Common/Components/Table/General/GeneralTableBody';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import { FormInlineInput } from '../../../Common/Components/Inputs/FormInlineUpdate';

export function StandardReadingTypesDisplayTable(props: {standardReadingTypes: 
    Array<AnalogResponseInterface|HumidityResponseInterface|TemperatureResponseInterface|LatitudeResponseInterface>,
    canEdit: boolean,
}) {
    const { standardReadingTypes, canEdit } = props;
    const sensorReadingTypesArray = Object.values(standardReadingTypes);

    // console.log('standardReadingTypes', sensorReadingTypesArray);

    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
        temperatureHighReading: false,
        humidityHighReading: false,
        latitudeHighReading: false,
        analogHighReading: false,
        temperatureLowReading: false,
        humidityLowReading: false,
        latitudeLowReading: false,
        analogLowReading: false,
        temperatureConstRecord: false,
        humidityConstRecord: false,
        latitudeConstRecord: false,
        analogConstRecord: false,
    });

    const originalSensorReadingTypesData = useRef<Array<AnalogResponseInterface|HumidityResponseInterface|TemperatureResponseInterface|LatitudeResponseInterface>>(sensorReadingTypesArray);

    const [sensorReadingTypesUpdateFormInputs, setSensorReadingTypesUpdateFormInputs] = useState<Array<AnalogResponseInterface|HumidityResponseInterface|TemperatureResponseInterface|LatitudeResponseInterface>>(sensorReadingTypesArray);

    const toggleFormInput = (event: Event) => {
        const type = (event.target as HTMLElement|HTMLInputElement).dataset.type !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;

        setActiveFormForUpdating({
            ...activeFormForUpdating,
            [type]: !activeFormForUpdating[type],
        });

        setSensorReadingTypesUpdateFormInputs(originalSensorReadingTypesData.current);
    }

    const handleUpdateSensorReadingTypeInput = (event: Event) => {
        const type = (event.target as HTMLElement|HTMLInputElement).dataset.type !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;
        
        const dataName = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
        ? (event.target as HTMLElement|HTMLInputElement).dataset.name
        : (event.target as HTMLInputElement).name;

        const nameParam = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
        ? (event.target as HTMLElement|HTMLInputElement).dataset.name
        : (event.target as HTMLInputElement).name;

        const value = (event.target as HTMLInputElement).value;

        setSensorReadingTypesUpdateFormInputs({
            ...sensorReadingTypesUpdateFormInputs,
            [`${dataName}.${nameParam}`]: value,
        });

        console.log('review', sensorReadingTypesUpdateFormInputs, standardReadingTypes)
    }

    const sendUpdateBoundaryReadingRequest = (event: Event) => {
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;
        
        const dataType = (event.target as HTMLElement|HTMLInputElement).dataset.type !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.type
            : (event.target as HTMLInputElement).name;

        console.log('name', name, dataType);
        let requestData = {
            [`${dataType}HighReading`]: sensorReadingTypesUpdateFormInputs[name],
        }
    }

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
                    const sensorReadingType = readingType.hasOwnProperty('analogID') ? 'analog' : readingType.hasOwnProperty('humidityID') ? 'humidity' : readingType.hasOwnProperty('temperatureID') ? 'temperature' : readingType.hasOwnProperty('latitudeID') ? 'latitude' : '';
                    return (
                        <React.Fragment key={index}>
                                <GeneralTableBody>
                                    <GeneralTableRow><span>{capitalizeFirstLetter(sensorReadingType)}</span></GeneralTableRow>
                                    <GeneralTableRow>
                                        <FormInlineInput
                                            changeEvent={handleUpdateSensorReadingTypeInput}
                                            nameParam={`highReading`}
                                            value={sensorReadingTypesUpdateFormInputs.highReading}
                                            dataName={`${sensorReadingType}`}
                                            dataType={`${sensorReadingType}HighReading`}
                                            acceptClickEvent={(e: Event) => sendUpdateBoundaryReadingRequest(e)}
                                            declineClickEvent={(e:Event) => toggleFormInput(e)}
                                            extraClasses='center-text'
                                        />
                                        </GeneralTableRow>
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