import * as React from 'react';

export function AcceptButton(props: {
    clickEvent: (e: Event) => void;
    dataName?: string;
    dataType?: string;
}) {
    const { clickEvent, dataName, dataType } = props;

    return (
        <button
            type="button"
            className="btn-action btn-action-accept"
            onClick={(e: React.MouseEvent<HTMLButtonElement>) => clickEvent(e as unknown as Event)}
            data-name={dataName}
            data-type={dataType}
            title="Accept"
        >
            <i className="fas fa-check" />
        </button>
    );
}
