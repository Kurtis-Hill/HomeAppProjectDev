import * as React from 'react';

import Input from '../Inputs/Input'

export default function InputWLabel(props: { 
    labelName: string; 
    name: string; 
    value: object;
    type?: string; 
    placeHolder?: string; 
    autoComplete?: string; 
    onChangeFunction: (event: { target: { name: string; value: string; }; }) => void; 
}) {
    const labelName = props.labelName ?? ''
    const name: string = props.name ?? ''
    const value: object = props.value
    const type: string = props.type ?? 'text'
    const placeHolder: string = props.placeHolder ?? ''
    const autoComplete: string = props.autoComplete ?? 'true'
    const onChangeFunction: (event: { 
        target: { 
            name: string; 
            value: string; 
        }; 
    }) => void = props.onChangeFunction ?? function (){}

    return (
        <>
            <label className="large font-weight-bold">{ labelName }</label>
            <Input 
                type={type}
                name={name}
                autoComplete={autoComplete}
                // className={"form-control form-control-user login-form-field"}
                placeHolder={placeHolder}
                onChangeFunction={onChangeFunction}
                { ...value }
            />
        </>
    )
}
