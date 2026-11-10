import * as React from 'react';

export default function SubmitButton(props: { 
    text: string;
    name?: string|null; 
    action?: string|null; 
    classes?: string|null;
    type?: "button"|"submit"|null;
    onClickFunction?: any;
}) {
    const text: string = props.text;
    const name: string = props.name ?? 'submit';
    const type: "button"|"submit" = props.type ?? 'button';
    const classes: string = props.classes ?? '';
    const onClickFunction: (e: Event) => void = props.onClickFunction ?? function (){};

    return (
        <React.Fragment>
            <button
                type={type}
                onClick={onClickFunction}
                name={name}
                className={`btn-modern-primary ${classes}`}
            >{text}
            </button>
        </React.Fragment>
    );
}
