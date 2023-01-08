import * as React from 'react';
import { useContext } from 'react';
// import { SensorDataContext } from '../../../../react-app/Contexts/SensorData/SensorDataContext';
import SensorDataContext from '../../../../react-app/Contexts/SensorData/SensorDataContext';
import { SensorDataContextDataInterface } from '../../../../react-app/Components/SensorDataProvider/SensorDataProvider';

export default function SmallWhiteBoxDisplay(props: { classes: string; heading: string; content: React; }) {
    const dropDownToggleClass: string = props.classes;
    const heading: string = props.heading;
    const content: React = props.content; 

    return (
        // <SensorDataContext.Consumer>
                // {(sensorData: SensorDataContextDataInterface) => (
                    <>
                <div className={`collapse ${dropDownToggleClass}`} aria-labelledby="headingTwo">
                    <div className="bg-white py-2 collapse-inner rounded">
                        <h6 className="collapse-header">{heading}:</h6>
                            {/* {sensorData.sensorReadingTypeData.map((value, index) => (
                                <React.Fragment key={index}>
                                
                                <h1 id="thisisNone">{value.readingTypeName}</h1>
                                </React.Fragment>
                            ))} */}
                        {content}
                    </div>
                </div>
    </>
        //     )}
        // </SensorDataContext.Consumer>
    );
}