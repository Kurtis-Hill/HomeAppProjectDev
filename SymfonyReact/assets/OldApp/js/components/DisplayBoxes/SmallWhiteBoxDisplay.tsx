import * as React from 'react';
import { useContext } from 'react';
import { SensorDataContext, useSensorDataContext } from '../../../../react-app/Contexts/SensorData/SensorDataContext';
// import SensorDataContext from '../../../../react-app/Contexts/SensorData/SensorDataContext';

export default function SmallWhiteBoxDisplay(props: { classes: string; heading: string; content: React; }) {
    const dropDownToggleClass: string = props.classes;
    const heading: string = props.heading;
    const content: React = props.content; 

    // const sensorData = useSensorDataContext();

    // console.log('sensorData: ', sensorData);
    const sensorData = useContext(SensorDataContext);

    console.log('sensorData', sensorData !== undefined ? sensorData.sensorTypes : 'no sensor data');

    return (
        <SensorDataContext.Consumer>
            {sensorData => (
                <div className={`collapse ${dropDownToggleClass}`} aria-labelledby="headingTwo">
                        <div className="bg-white py-2 collapse-inner rounded">
                            <h6 className="collapse-header">{heading}:</h6>
                            {content}
                        </div>
                    </div>
            )}
        </SensorDataContext.Consumer>
    );
}