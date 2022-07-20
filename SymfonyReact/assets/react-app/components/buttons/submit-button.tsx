import * as React from 'react';

export default function SubmitButton(props) {
    const name = props.name ?? 'submit';
    const action = props.action ?? 'submit'
    const size = props.size ?? '';
    const onClickFunction = props.onClickFunction ?? function ();

    return (
        <React.Fragment>
            <button
                onClick={onClickFunction}
                name={name}
                action={action}
                className={`btn btn-primary btn-user btn-block ${size}`}
            >Login
            </button>
        </React.Fragment>
    );
}
