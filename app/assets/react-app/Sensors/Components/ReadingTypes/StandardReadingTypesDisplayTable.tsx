import * as React from 'react';
import { useState, useRef } from 'react';

import HumidityResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/HumidityResponseInterface';
import AnalogResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/AnalogResponseInterface';
import LatitudeResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/LatitudeResponseInterface';
import TemperatureResponseInterface from '../../Response/ReadingTypes/SensorReadingTypeResponseInterfaces/TemperatureResponseInterface';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import { FormInlineInput } from '../../../Common/Components/Inputs/FormInlineUpdate';
import { StandardSensorBoundaryReadingUpdateInputInterface, readingTypeBoundaryReadingUpdateRequest } from '../../Request/ReadingType/ReadingTypeBoundaryReadingUpdateRequest';
import { FormSelectWAcceptDecline } from '../../../Common/Components/Selects/FormSelectWAcceptDecline';
import { ConstRecordType } from '../../Types/SensorReadingTypesOptionTypes';

type ReadingType = AnalogResponseInterface | HumidityResponseInterface | TemperatureResponseInterface | LatitudeResponseInterface;

const activeFormDefaultValues = {
    temperatureHighReading: false,
    temperatureLowReading: false,
    temperatureConstRecord: false,
    temperatureOutOfBoundsAlertTimer: false,
    humidityHighReading: false,
    humidityLowReading: false,
    humidityConstRecord: false,
    humidityOutOfBoundsAlertTimer: false,
    latitudeHighReading: false,
    latitudeLowReading: false,
    latitudeConstRecord: false,
    latitudeOutOfBoundsAlertTimer: false,
    analogHighReading: false,
    analogLowReading: false,
    analogConstRecord: false,
    analogOutOfBoundsAlertTimer: false,
};

export function StandardReadingTypesDisplayTable(props: {
    standardReadingTypes: ReadingType[];
    canEdit: boolean;
    refreshData?: () => void;
}) {
    const { standardReadingTypes, canEdit, refreshData } = props;
    const sensorReadingTypesArray = Object.values(standardReadingTypes);

    const [activeForm, setActiveForm] = useState<Record<string, boolean>>({ ...activeFormDefaultValues });
    const originalData = useRef<ReadingType[]>(sensorReadingTypesArray);
    const [formInputs, setFormInputs] = useState<ReadingType[]>(sensorReadingTypesArray);

    const toggleFormInput = (key: string) => {
        setActiveForm(prev => ({ ...prev, [key]: !prev[key] }));
        // reset to a fresh array copy of the original so formInputs stays a proper array
        setFormInputs([...originalData.current]);
    };

    const handleUpdateInput = (event: Event) => {
        const arrayIndex = parseInt((event.target as HTMLElement).dataset.name, 10);
        const nameParam = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setFormInputs(prev => {
            const next = [...prev]; // keep it a proper array
            if (nameParam === 'constRecord') {
                const booleanValue: ConstRecordType = parseInt(value) === 1;
                next[arrayIndex] = { ...next[arrayIndex], [nameParam]: booleanValue };
            } else {
                next[arrayIndex] = {
                    ...next[arrayIndex],
                    [nameParam]: !value ? '' : isNaN(Number(value)) ? value : parseInt(value),
                };
            }
            return next;
        });
    };

    const sendUpdateRequest = async (index: number) => {
        const item = formInputs[index];
        const requestData: StandardSensorBoundaryReadingUpdateInputInterface = {
            readingType: item.readingType,
            highReading: item.highReading,
            lowReading: item.lowReading,
            constRecord: item.constRecord,
            outOfBoundsAlertTimer: item.outOfBoundsAlertTimer,
        };

        const response = await readingTypeBoundaryReadingUpdateRequest(item.sensor.sensorID, [requestData]);
        if (response.status === 200) {
            refreshData?.();
            // save updated values so subsequent edits start from the saved state
            originalData.current = [...formInputs];
            setActiveForm({ ...activeFormDefaultValues });
        }
    };

    return (
        <>
            <p className="reading-types-title">Standard Readings</p>
            <table className="reading-table">
                <thead>
                    <tr>
                        <th>Reading Type</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Always Record</th>
                        <th>Alert Timer (s)</th>
                    </tr>
                </thead>
                <tbody>
                    {sensorReadingTypesArray.map((readingType, index) => {
                        const typeLabel = capitalizeFirstLetter(readingType.readingType);
                        const highKey = `${readingType.readingType}HighReading`;
                        const lowKey = `${readingType.readingType}LowReading`;
                        const constKey = `${readingType.readingType}ConstRecord`;
                        const alertTimerKey = `${readingType.readingType}OutOfBoundsAlertTimer`;

                        return (
                            <tr key={index}>
                                <td><strong>{typeLabel}</strong></td>

                                {/* High Reading */}
                                <td>
                                    {activeForm[highKey] && canEdit ? (
                                        <FormInlineInput
                                            changeEvent={handleUpdateInput}
                                            nameParam="highReading"
                                            value={formInputs[index].highReading ?? '0'}
                                            dataName={highKey}
                                            dataType={readingType.sensorType}
                                            acceptClickEvent={() => sendUpdateRequest(index)}
                                            declineClickEvent={() => toggleFormInput(highKey)}
                                            extraClasses="center-text"
                                            inputDataName={`${index}`}
                                        />
                                    ) : (
                                        <span
                                            className={canEdit ? 'hover' : ''}
                                            data-name={highKey}
                                            onClick={() => canEdit && toggleFormInput(highKey)}
                                            title={canEdit ? 'Click to edit' : undefined}
                                        >
                                            {readingType.highReading}
                                        </span>
                                    )}
                                </td>

                                {/* Low Reading */}
                                <td>
                                    {activeForm[lowKey] && canEdit ? (
                                        <FormInlineInput
                                            changeEvent={handleUpdateInput}
                                            nameParam="lowReading"
                                            value={formInputs[index].lowReading}
                                            dataName={lowKey}
                                            dataType={readingType.sensorType}
                                            acceptClickEvent={() => sendUpdateRequest(index)}
                                            declineClickEvent={() => toggleFormInput(lowKey)}
                                            extraClasses="center-text"
                                            inputDataName={`${index}`}
                                        />
                                    ) : (
                                        <span
                                            className={canEdit ? 'hover' : ''}
                                            data-name={lowKey}
                                            onClick={() => canEdit && toggleFormInput(lowKey)}
                                            title={canEdit ? 'Click to edit' : undefined}
                                        >
                                            {readingType.lowReading}
                                        </span>
                                    )}
                                </td>

                                {/* Const Record */}
                                <td>
                                    {activeForm[constKey] && canEdit ? (
                                        <FormSelectWAcceptDecline
                                            selectName="constRecord"
                                            changeEvent={handleUpdateInput}
                                            selectDefaultValue={formInputs[index].constRecord === true ? 1 : 0}
                                            selectOptions={[
                                                { value: 1, name: 'Yes' },
                                                { value: 0, name: 'No' },
                                            ]}
                                            dataName={`${index}`}
                                            acceptClickEvent={() => sendUpdateRequest(index)}
                                            declineClickEvent={() => toggleFormInput(constKey)}
                                            declineName={constKey}
                                        />
                                    ) : (
                                        <span
                                            className={canEdit ? 'hover' : ''}
                                            data-name={constKey}
                                            onClick={() => canEdit && toggleFormInput(constKey)}
                                            title={canEdit ? 'Click to edit' : undefined}
                                        >
                                            {readingType.constRecord ? 'Yes' : 'No'}
                                        </span>
                                    )}
                                </td>

                                {/* Out Of Bounds Alert Timer */}
                                <td>
                                    {activeForm[alertTimerKey] && canEdit ? (
                                        <FormInlineInput
                                            changeEvent={handleUpdateInput}
                                            nameParam="outOfBoundsAlertTimer"
                                            value={formInputs[index].outOfBoundsAlertTimer ?? 3600}
                                            dataName={alertTimerKey}
                                            dataType={readingType.sensorType}
                                            acceptClickEvent={() => sendUpdateRequest(index)}
                                            declineClickEvent={() => toggleFormInput(alertTimerKey)}
                                            extraClasses="center-text"
                                            inputDataName={`${index}`}
                                        />
                                    ) : (
                                        <span
                                            className={canEdit ? 'hover' : ''}
                                            data-name={alertTimerKey}
                                            onClick={() => canEdit && toggleFormInput(alertTimerKey)}
                                            title={canEdit ? 'Click to edit' : undefined}
                                        >
                                            {readingType.outOfBoundsAlertTimer ?? 3600}
                                        </span>
                                    )}
                                </td>
                            </tr>
                        );
                    })}
                </tbody>
            </table>
        </>
    );
}
