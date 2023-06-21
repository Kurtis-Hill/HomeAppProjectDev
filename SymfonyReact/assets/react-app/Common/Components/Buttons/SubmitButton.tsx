import * as React from 'react';

export default function SubmitButton(props: { 
    text: string;
    name?: string|null; 
    action?: string|null; 
    classes?: string|null;
    type?: string|null 
    onClickFunction?: (e: Event) => void|null; 
}) {
    const text: string = props.text;
    const name: string = props.name ?? 'submit';
    const action: string = props.action ?? 'submit';
    const classes: string = props.classes ?? '';
    const type: string = props.type ?? 'button';
    const onClickFunction: (e: Event) => void = props.onClickFunction ?? function (){};

    return (
        <React.Fragment>
            <button
                type={type}
                onClick={onClickFunction}
                name={name}
                action={action}
                className={`btn btn-primary btn-user ${classes}`}
            >{text}
            </button>
        </React.Fragment>
    );

}
