import * as React from 'react';

export function DeclineButton(props: {
    clickEvent: (e: Event) => void;
    dataName?: string;
}) {

    const { clickEvent, dataName } = props;
    return (
        <i className="fas fa-times-circle fa-2x hover cancel-button" onClick={(e: Event) => clickEvent(e)} data-name={dataName}></i>
    )
}