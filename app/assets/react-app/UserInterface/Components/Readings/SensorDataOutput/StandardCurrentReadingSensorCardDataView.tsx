import * as React from 'react';
import { CurrentSensorDataTypeStandardCard } from '../../../Factories/CurrentReadingSensorDataOutputFactory';
import { capitalizeFirstLetter } from '../../../../Common/StringFormatter';

export function StandardCurrentReadingSensorCardDataView(props: { sensorData: CurrentSensorDataTypeStandardCard[]; }) {
    const sensorData: CurrentSensorDataTypeStandardCard[] = props.sensorData ?? undefined;

    const sensorReadingAgainstLowHighBoundary = (highReading: number, lowReading: number, currentReading: number) => {
        return (currentReading >= highReading) 
          ? 'text-red' 
          : (currentReading <= lowReading) 
            ? 'text-blue' 
            : 'text-gray-800';
      }

    if (sensorData !== undefined && sensorData.length > 0) {
        return (
            <React.Fragment>
                {
                    sensorData.map((data: CurrentSensorDataTypeStandardCard, index: number) => {
                        return (
                            <>
                                <div key={index}>
                                    <div key={index} className={`card-font mb-0 font-weight-bold ${sensorReadingAgainstLowHighBoundary(data.hightReading, data.lowReading, data.currentReading)}`}>
                                        { capitalizeFirstLetter(data.readingType)}: {data.currentReading}{data.readingSymbol } <i style={{ color:(data.lastState === 'up') ? 'red' : (data.lastState === 'down') ? 'blue' : 'gray' }} className={`fas fa-1x fa-${(data.lastState !== 'same') ? `chevron-${data.lastState}` : 'horizontal-rule'}`}> </i>
                                    </div>
                                    <div className="card-font mb-0 text-gray-400">updated@{data.updatedAt}</div>                            
                                </div>
                            </>
                        )
                    })
                }


            </React.Fragment>
        )
    }
}
