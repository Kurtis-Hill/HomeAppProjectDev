import * as React from 'react';

import Input from '../Inputs/Input'
import { Label } from '../Elements/Label';

export default function InputWLabel(props: { 
    labelName: string; 
    name: string; 
    value?: object;
    type?: string; 
    placeHolder?: string; 
    autoComplete?: string; 
    autoFocus?: boolean;
    extraClasses?: string;
    labelExtraClasses?: string;
    inputOnClickFn?: (event: Event) => void;
    labelOnClickFn?: (event: Event) => void;
    onChangeFunction: (event: Event) => void; 
}) {
    const labelName = props.labelName ?? ''
    const name: string = props.name ?? ''
    const value: object = props.value
    const type: string = props.type ?? 'text'
    const placeHolder: string = props.placeHolder ?? ''
    const autoComplete: string = props.autoComplete ?? 'true'
    const autoFocus: boolean = props.autoFocus ?? false
    const labelExtraClasses: string = props.labelExtraClasses ?? ''
    const extraClasses: string = props.extraClasses ?? ''
    const inputOnClickFn: (event: Event) => void = props.inputOnClickFn ?? function (){}

    const onChangeFunction = props.onChangeFunction ?? function (){}

    return (
        <>
            <Label
                text={labelName}
                htmlFor={name}
                classes={labelExtraClasses}
            />
            <Input 
                type={type}
                name={name}
                autoComplete={autoComplete}
                placeHolder={placeHolder}
                onChangeFunction={onChangeFunction}
                autoFocus={autoFocus}
                extraClasses={extraClasses}
                value={value}
                inputOnClickFn={inputOnClickFn}
            />
        </>
    )
}
