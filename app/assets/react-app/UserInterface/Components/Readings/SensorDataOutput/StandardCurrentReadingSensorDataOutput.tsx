import * as React from 'react';

import { capitalizeFirstLetter } from '../../../../../Common/StringFormatter';

export function StandardCurrentReadingSensorDataOutput(props: { sensorData: CurrentSensorDataTypeStandardCard[]|undefined; }) {
    const sensorData: Array<CurrentSensorDataTypeStandardCard> = props.sensorData ?? undefined;

    const sensorReadingAgainstLowHighBoundary = (highReading: number, lowReading: number, currentReading: number) => {
        return (currentReading >= highReading) 
          ? 'text-red' 
          : (currentReading <= lowReading) 
            ? 'text-blue' 
            : 'text-gray-800';
      }

    if (sensorData !== undefined && sensorData.length > 0) {
        return (
            <>
                {
                    sensorData.map((data: CurrentSensorDataTypeStandardCard, index: number) => {
                        const lastStateIcon: string = (data.lastState !== 'same') ? `chevron-${data.lastState}` : 'horizontal-rule';   
                        const iconColour: string = (data.lastState === 'up') ? 'red' : (data.lastState === 'down') ? 'blue' : 'gray';
    
                        return (
                            <>
                                <div key={index}>
                                    <div className={`card-font mb-0 font-weight-bold ${sensorReadingAgainstLowHighBoundary(data.hightReading, data.lowReading, data.currentReading)}`}>
                                        { capitalizeFirstLetter(data.readingType)}: {data.currentReading}{data.readingSymbol } <i style={{ color:iconColour }} className={`fas fa-1x fa-${lastStateIcon}`}> </i>
                                    </div>
                                    <div className="card-font mb-0 text-gray-400">updated@{data.updatedAt}</div>                            
                                </div>
                            </>
                        )
                    })  
                }          
            </>
        )
    }
}