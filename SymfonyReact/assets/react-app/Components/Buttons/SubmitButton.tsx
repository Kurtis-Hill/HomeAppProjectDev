import * as React from 'react';

export default function SubmitButton(props: { name?: string|null; action?: string|null; size?: string|null; onClickFunction?: any; }) {
    const name: string = props.name ?? 'submit';
    const action: string = props.action ?? 'submit';
    const size: string = props.size ?? '';
    const onClickFunction: () => void = props.onClickFunction ?? function (){};

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
