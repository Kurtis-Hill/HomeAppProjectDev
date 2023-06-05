import * as React from 'react';
import InputWLabel from './InputWLabel';
import { AcceptButton } from '../Buttons/AcceptButton';
import { DeclineButton } from '../Buttons/DeclineButton';
import Input from './Input';

export function FormInlineInput(props: {
    nameParam: string;
    changeEvent: (event: Event) => void;
    value: object;
    acceptClickEvent: (e: Event) => void;
    declineClickEvent: (e: Event) => void;
    dataName?: string;
    dataType?: string;
    extraClasses?: string;
}) {

    const { 
        nameParam, 
        changeEvent, 
        value, 
        acceptClickEvent, 
        declineClickEvent, 
        dataName,
        dataType,
        extraClasses,
    } = props;
    return (
        <>
            <Input
                name={nameParam}
                type="text"
                onChangeFunction={(e: Event) => changeEvent(e)}
                autoFocus={true}
                value={value}
                extraClasses={extraClasses}
            />
            <span style={{ paddingLeft: "2%" }}>
                <AcceptButton clickEvent={(e: Event) => acceptClickEvent(e)} dataName={dataName} dataType={dataType} />
                <DeclineButton clickEvent={(e: Event) => declineClickEvent(e)} dataName={dataName} />
            </span>
        </>
    )
}