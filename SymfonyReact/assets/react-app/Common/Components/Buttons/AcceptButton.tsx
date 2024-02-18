import * as React from 'react';

export function AcceptButton(props: {
    clickEvent: (e: Event) => void;
    dataName?: string;
    dataType?: string;
}) {
    const { clickEvent, dataName, dataType } = props;

    return (
        <i className="fas fa-check-circle fa-2x hover accept-button" onClick={(e: Event) => clickEvent(e)} data-name={dataName} data-type={dataType}></i>
    )
}