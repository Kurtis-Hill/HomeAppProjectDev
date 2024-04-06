import * as React from 'react';
import { useContext } from 'react';
// import { SensorDataContext } from '../../../../react-app/Contexts/SensorData/SensorDataContext';
import SensorDataContext from '../../../../react-app/Sensors/Contexts/SensorDataContext';
import { SensorDataContextDataInterface } from '../../../../react-app/Sensors/DataProviders/SensorDataProvider';

export default function SmallWhiteBoxDisplay(props: { 
    classes: string; 
    heading: string; 
    children?: React.ReactNode;
 }) {
    const dropDownToggleClass: string = props.classes;
    const heading: string = props.heading;

    return (
        <>
            <div className={`collapse ${dropDownToggleClass}`} aria-labelledby="headingTwo">
                <div className="bg-white py-2 collapse-inner rounded">
                    <h6 className="collapse-header">{heading}:</h6>
                    { props.children }
                </div>
            </div>
        </>
    );
}