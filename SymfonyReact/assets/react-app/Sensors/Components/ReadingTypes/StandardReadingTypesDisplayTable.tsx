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
import { readingType } from '../../../Common/SensorLanguage';
import { StandardSensorBoundaryReadingUpdateInputInterface, readingTypeBoundaryReadingUpdateRequest } from '../../Request/ReadingType/ReadingTypeBoundaryReadingUpdateRequest';
import { FormInlineSpan } from '../../../Common/Components/Elements/FormInlineSpan';
import { FormInlineSelectWLabel } from '../../../Common/Components/Selects/FormInlineSelectWLabel';
import { FormSelect } from '../../../Common/Components/Selects/FormSelect';
import { AcceptButton } from '../../../Common/Components/Buttons/AcceptButton';
import { DeclineButton } from '../../../Common/Components/Buttons/DeclineButton';
import { ConstRecordType } from './SensorReadingTypesOptionTypes';

export function StandardReadingTypesDisplayTable(props: {
    standardReadingTypes: Array<
        AnalogResponseInterface|HumidityResponseInterface|TemperatureResponseInterface|LatitudeResponseInterface
    >,
    canEdit: boolean,
}) {
    const { standardReadingTypes, canEdit } = props;
    const sensorReadingTypesArray = Object.values(standardReadingTypes);

    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
        temperatureHighReading: false,
        temperatureLowReading: false,
        temperatureConstRecord: false,
        humidityHighReading: false,
        humidityLowReading: false,
        humidityConstRecord: false,
        latitudeHighReading: false,
        latitudeLowReading: false,
        latitudeConstRecord: false,
        analogHighReading: false,
        analogLowReading: false,
        analogConstRecord: false,
    });

    const originalSensorReadingTypesData = useRef<Array<AnalogResponseInterface|HumidityResponseInterface|TemperatureResponseInterface|LatitudeResponseInterface>>(sensorReadingTypesArray);

    const [sensorReadingTypesUpdateFormInputs, setSensorReadingTypesUpdateFormInputs] = useState<Array<AnalogResponseInterface|HumidityResponseInterface|TemperatureResponseInterface|LatitudeResponseInterface>>(sensorReadingTypesArray);

    const toggleFormInput = (event: Event) => {
        const type = (event.target as HTMLElement|HTMLInputElement).dataset.name
        // console.log('hey', type);

        setActiveFormForUpdating({
            ...activeFormForUpdating,
            [type]: !activeFormForUpdating[type],
        });

        setSensorReadingTypesUpdateFormInputs(originalSensorReadingTypesData.current);
    }

    const handleUpdateSensorReadingTypeInput = (event: Event) => {
        const arrayIndex = (event.target as HTMLElement|HTMLInputElement).dataset.name 
        const nameParam = (event.target as HTMLInputElement).name;
        
        const value = (event.target as HTMLInputElement).value;
        console.log('arrayKeyValue', arrayIndex, 'nameParam', nameParam, 'value', value)

        if (nameParam === 'constRecord') {
            const booleanValue: ConstRecordType = parseInt(value) === 1 ? true : false;
            setSensorReadingTypesUpdateFormInputs({
                ...sensorReadingTypesUpdateFormInputs,
                [arrayIndex]: {
                    ...sensorReadingTypesUpdateFormInputs[arrayIndex],
                    [nameParam]: booleanValue,
                }
            });
            sendUpdateBoundaryReadingRequest(parseInt(arrayIndex));
        } else {
            setSensorReadingTypesUpdateFormInputs({
                ...sensorReadingTypesUpdateFormInputs,
                [arrayIndex]: {
                    ...sensorReadingTypesUpdateFormInputs[arrayIndex],
                    [nameParam]: parseInt(value),
                }
            });
        }
    }

    const sendUpdateBoundaryReadingRequest = (
        index: number
    ) => {
        // console.log('index', index);
        const sensorBoundaryUpdateToBeSent = sensorReadingTypesUpdateFormInputs[index];

        const requestData: StandardSensorBoundaryReadingUpdateInputInterface = {
            'readingType': sensorBoundaryUpdateToBeSent.readingType,
            'highReading': sensorBoundaryUpdateToBeSent.highReading,
            'lowReading': sensorBoundaryUpdateToBeSent.lowReading,
            'constRecord': sensorBoundaryUpdateToBeSent.constRecord,
        }

        const sensorBoundaryReadingResponse = readingTypeBoundaryReadingUpdateRequest(sensorReadingTypesUpdateFormInputs[index].sensor.sensorID, [requestData]);

        console.log('requestData', requestData);
    }

    // const workOutReadingType = (readingType: AnalogResponseInterface|HumidityResponseInterface|TemperatureResponseInterface|LatitudeResponseInterface) => {
    //     return readingType.hasOwnProperty('analogID') ? 'analog' : readingType.hasOwnProperty('humidityID') ? 'humidity' : readingType.hasOwnProperty('temperatureID') ? 'temperature' : readingType.hasOwnProperty('latitudeID') ? 'latitude' : '';
    // }
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
                    ]} 
                />
                {sensorReadingTypesArray.map((readingType, index) => {
                    return (
                        <React.Fragment key={index}>
                            <GeneralTableBody>
                                <GeneralTableRow><span>{capitalizeFirstLetter(readingType.readingType)}</span></GeneralTableRow>
                                <GeneralTableRow>
                                    {
                                            activeFormForUpdating[`${readingType.readingType}HighReading`] === true && canEdit === true 
                                                ? 
                                                    <>
                                                        <FormInlineInput
                                                            changeEvent={handleUpdateSensorReadingTypeInput}
                                                            nameParam={`highReading`}
                                                            value={sensorReadingTypesUpdateFormInputs.highReading}
                                                            dataName={`${readingType.readingType}HighReading`}
                                                            dataType={`${readingType.sensorType}`}
                                                            acceptClickEvent={() => sendUpdateBoundaryReadingRequest(index)}
                                                            declineClickEvent={(e:Event) => toggleFormInput(e)}
                                                            extraClasses='center-text'
                                                            inputDataName={`${index}`}
                                                        />
                                                    </>
                                                :
                                                    <>
                                                        <span 
                                                            className={canEdit === true ? 'hover': null} 
                                                            onClick={(e: Event) => toggleFormInput(e)} 
                                                            data-name={`${readingType.readingType}HighReading`}
                                                        >
                                                            {`${readingType.highReading}`}
                                                        </span>
                                                    </>
                                    }
                                </GeneralTableRow>
                                <GeneralTableRow>
                                    {
                                            activeFormForUpdating[`${readingType.readingType}LowReading`] === true && canEdit === true 
                                                ? 
                                                    <>
                                                        <FormInlineInput
                                                            changeEvent={handleUpdateSensorReadingTypeInput}
                                                            nameParam={`lowReading`}
                                                            value={sensorReadingTypesUpdateFormInputs.lowReading}
                                                            dataName={`${readingType.readingType}LowReading`}
                                                            dataType={`${readingType.sensorType}`}
                                                            acceptClickEvent={() => sendUpdateBoundaryReadingRequest(index)}
                                                            declineClickEvent={(e:Event) => toggleFormInput(e)}
                                                            extraClasses='center-text'
                                                            inputDataName={`${index}`}
                                                        />
                                                    </>
                                                :
                                                    <>
                                                        <span 
                                                            className={canEdit === true ? 'hover': null} 
                                                            onClick={(e: Event) => toggleFormInput(e)} 
                                                            data-name={`${readingType.readingType}LowReading`}
                                                        >
                                                            {`${readingType.lowReading}`}
                                                        </span>
                                                    </>
                                    }
                                </GeneralTableRow>
                                <GeneralTableRow>
                                    {
                                            activeFormForUpdating[`${readingType.readingType}ConstRecord`] === true && canEdit === true 
                                                ? 
                                                    <>
                                                        <FormSelect
                                                            selectName={`constRecord`}
                                                            changeEvent={handleUpdateSensorReadingTypeInput}
                                                            selectDefaultValue={sensorReadingTypesUpdateFormInputs.constRecord === true ? 1 : 0}
                                                            selectOptions={[
                                                                {
                                                                    value: 1,
                                                                    name: 'Yes',
                                                                },
                                                                {
                                                                    value: 0,
                                                                    name: 'No',
                                                                },
                                                            ]}
                                                            dataName={`${index}`}
                                                        />
                                                        <AcceptButton clickEvent={() => sendUpdateBoundaryReadingRequest(index)} dataName={`${index}`} />
                                                        <DeclineButton clickEvent={(e:Event) => toggleFormInput(e)} dataName={`${index}`} />
                                                        {/* <FormInlineInput
                                                            changeEvent={handleUpdateSensorReadingTypeInput}
                                                            nameParam={`constRecord`}
                                                            value={sensorReadingTypesUpdateFormInputs.constRecord}
                                                            dataName={`${readingType.readingType}ConstRecord`}
                                                            dataType={`${readingType.sensorType}`}
                                                            acceptClickEvent={() => sendUpdateBoundaryReadingRequest(index)}
                                                            declineClickEvent={(e:Event) => toggleFormInput(e)}
                                                            extraClasses='center-text'
                                                            inputDataName={`${index}`}
                                                        /> */}
                                                    </>
                                                :
                                                    <>
                                                        <span 
                                                            className={canEdit === true ? 'hover': null} 
                                                            onClick={(e: Event) => toggleFormInput(e)} 
                                                            data-name={`${readingType.readingType}ConstRecord`}
                                                        >
                                                            {readingType.constRecord === true ? 'Yes' : 'No'}
                                                        </span>
                                                    </>
                                    }
                                </GeneralTableRow>
                            </GeneralTableBody>     
                        </React.Fragment>
                    );
                })}
            </GeneralTable>
        </>
    );
}