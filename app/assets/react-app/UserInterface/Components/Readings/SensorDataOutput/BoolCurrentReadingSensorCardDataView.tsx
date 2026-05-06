import * as React from 'react';
import { CurrentSensorDataTypeBoolCard } from '../../../Factories/CurrentReadingSensorDataOutputFactory';
import { capitalizeFirstLetter } from '../../../../Common/StringFormatter';

export function BoolCurrentReadingSensorCardDataView(props: { sensorData: CurrentSensorDataTypeBoolCard[]|undefined; }) {
    const { sensorData } = props;

    const sensorReadingAgainstExpectedReading = (expectedReading: boolean, currentReading: boolean) => {
        return (currentReading !== expectedReading) 
          ? 'text-red' 
          : 'text-gray-800';
    }
      
    if (sensorData !== undefined && sensorData.length > 0) {
        return (
            <React.Fragment>
                {
                    sensorData.map((data: CurrentSensorDataTypeBoolCard, index: number) => {
                        return (
                            <React.Fragment key={index}>
                                <div>
                                    <div className={`card-font mb-0 font-weight-bold ${sensorReadingAgainstExpectedReading(data.expectedReading, data.currentReading)}`}>
                                        {/* <div className="custom-control custom-switch"> */}
                                        { capitalizeFirstLetter(data.readingType)}: {data.currentReading === true ? 'ON' : 'OFF'}{data.readingSymbol }
                                            {/* <label className="custom-control-label" htmlFor="onOffControl">{ </label> */}
                                            {/* <input type="checkbox" className="custom-control-input" id="onOffControl"></input> */}
                                        {/* </div> */}
                                    </div>
                                    <div className="card-font mb-0 text-gray-400">updated@{data.updatedAt}</div>                            
                                </div>
                            </React.Fragment>
                        )
                    })  
                }          
            </React.Fragment>
        )
    }
}