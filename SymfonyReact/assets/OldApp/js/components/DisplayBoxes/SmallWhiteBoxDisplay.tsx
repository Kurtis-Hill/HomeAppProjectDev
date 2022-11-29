import * as React from 'react';

export default function SmallWhiteBoxDisplay(props: { dropdownToggleClass: string; heading: string; content: any; }) {
    const dropDownToggleClass: string = props.dropdownToggleClass;

    const heading: string = props.heading;

    const content: React = props.content; 

    return (
        <div className={`collapse ${dropDownToggleClass}`} aria-labelledby="headingTwo">
            <div className="bg-white py-2 collapse-inner rounded">
                <h6 className="collapse-header">View {heading}:</h6>
                {content}
            </div>
        </div>
    );
}