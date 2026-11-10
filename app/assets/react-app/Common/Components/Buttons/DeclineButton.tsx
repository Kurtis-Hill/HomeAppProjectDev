import * as React from 'react';

export function DeclineButton(props: {
    clickEvent: (e: Event) => void;
    dataName?: string;
}) {
    const { clickEvent, dataName } = props;

    return (
        <button
            type="button"
            className="btn-action btn-action-decline"
            onClick={(e: React.MouseEvent<HTMLButtonElement>) => clickEvent(e as unknown as Event)}
            data-name={dataName}
            title="Cancel"
        >
            <i className="fas fa-times" />
        </button>
    );
}
