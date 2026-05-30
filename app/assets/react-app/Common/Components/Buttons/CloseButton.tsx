import * as React from 'react';

export default function CloseButton(props: {
    close: (show: boolean) => void;
    buttonText?: string|undefined;
    classes?: string;
}) {
    const buttonText = props.buttonText ?? 'Cancel';
    const classes = props.classes ?? '';

    return (
        <button
            className={`btn-modern-secondary ${classes}`}
            type="button"
            onClick={() => props.close(false)}
        >
            {buttonText}
        </button>
    );
}
