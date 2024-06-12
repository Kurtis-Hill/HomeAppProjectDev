import * as React from 'react';

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