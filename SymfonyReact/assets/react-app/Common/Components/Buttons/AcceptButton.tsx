import * as React from 'react';

export function AcceptButton(props: {
    clickEvent: (e: Event) => void;
}) {
    const { clickEvent } = props;

    return (
        <i className="fas fa-check-circle fa-2x hover accept-button" onClick={(e: Event) => clickEvent(e)}></i>
    )
}