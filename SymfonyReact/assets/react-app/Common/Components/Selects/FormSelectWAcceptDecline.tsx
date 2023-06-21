import * as React from 'react';
import { AcceptButton } from '../Buttons/AcceptButton';
import { DeclineButton } from '../Buttons/DeclineButton';
import { FormSelect } from './FormSelect';

export function FormSelectWAcceptDecline(props: {
    selectName: string;
    changeEvent: (e: Event) => void;
    selectDefaultValue?: number;
    selectOptions: {
        value: string|number;
        name: string;
    }[];
    dataName?: string;
    declineName?: string;
    acceptClickEvent: (e: Event) => void;
    declineClickEvent: (e: Event) => void;
}) {
    const { 
        selectName, 
        selectDefaultValue, 
        changeEvent, 
        selectOptions, 
        dataName, 
        declineName, 
        acceptClickEvent, 
        declineClickEvent 
    } = props;

    return (
        <>
            <FormSelect
                selectName={selectName}
                changeEvent={changeEvent}
                selectDefaultValue={selectDefaultValue}
                selectOptions={selectOptions}
                dataName={dataName}

            />
            <AcceptButton clickEvent={(e: Event) => acceptClickEvent(e)} dataName={`${dataName}`} />
            <DeclineButton clickEvent={(e:Event) => declineClickEvent(e)} dataName={`${declineName ?? dataName}`} />
        </>
    )
}