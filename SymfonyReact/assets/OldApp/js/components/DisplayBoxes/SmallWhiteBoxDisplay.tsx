import * as React from 'react';
import { useContext } from 'react';
// import { SensorDataContext } from '../../../../react-app/Contexts/SensorData/SensorDataContext';
import SensorDataContext from '../../../../react-app/Contexts/SensorData/SensorDataContext';

export default function SmallWhiteBoxDisplay(props: { classes: string; heading: string; content: React; }) {
    const dropDownToggleClass: string = props.classes;
    const heading: string = props.heading;
    const content: React = props.content; 

    // const sensorData = useSensorDataContext();

    // console.log('sensorData: ', sensorData);
    // const sensorData = useContext(SensorDataContext);

    // console.log('sensorData', sensorData !== undefined ? sensorData.sensorTypes : 'no sensor data');

//     const getData = () => {
//         <SensorDataContext.Consumer>
//         {sensorData => (
//             // console.log('sensorData', sensorData);
//             <>
//             <h1></h1>
//                 {/* { Object.keys(sensorData.sensorReadingTypeData).map(sensorTD => (<h1>h1</h1>) } */}

//         <div className={`collapse ${dropDownToggleClass}`} aria-labelledby="headingTwo">
//             {/* <h1>{sensorData.sensorReadingTypeData.readingTypeName}</h1> */}
//                 <div className="bg-white py-2 collapse-inner rounded">
//                     <h6 className="collapse-header">{heading}:</h6>
//                     {content}
//                 </div>
//             </div>
// </>
//     )}
// </SensorDataContext.Consumer>
//     }
    
    return (
        <SensorDataContext.Consumer>
                {sensorData => (
                    <>
                <div className={`collapse ${dropDownToggleClass}`} aria-labelledby="headingTwo">
                    <div className="bg-white py-2 collapse-inner rounded">
                        <h6 className="collapse-header">{heading}:</h6>
                            {sensorData.sensorReadingTypeData.map((value, index) => (
                                <React.Fragment key={index}>
                                
                                <h1 id="thisisNone">{value.readingTypeName}</h1>
                                </React.Fragment>
                            ))}
                        {content}
                    </div>
                </div>
    </>
            )}
        </SensorDataContext.Consumer>
    );
}