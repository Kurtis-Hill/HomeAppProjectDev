import * as React from 'react';

export default function DotCircleSpinner(props: { spinnerSize?: number; classes?: string; }) {
    const spinnerSize: number = props.spinnerSize ?? 2;
    const classes: string = props.classes ?? '';

    return (
        <React.Fragment>
            <div className={`center-item fa-${spinnerSize}x fas fa-spinner fa-spin ${classes}`}></div>
        </React.Fragment>
    );
}
