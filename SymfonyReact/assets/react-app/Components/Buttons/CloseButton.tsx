import * as React from 'react';

export default function CloseButton(props: {
    close: (show: boolean) => void;
    buttonText?: string|undefined
}) {
    const buttonText = props.buttonText ?? 'Cancel'
    return (
        <button className="btn btn-secondary" type="button" onClick={() => {props.close(false)}} data-dismiss="modal">{ buttonText }</button>
    );
}