import * as React from 'react';
import { Label } from '../Elements/Label';
import { AcceptButton } from '../Buttons/AcceptButton';
import { DeclineButton } from '../Buttons/DeclineButton';

export function FormInlineSelectWLabel(
    props: {
        labelName: string;
        changeEvent: (e: Event) => void;
        selectName: string;
        selectOptions: {
            value: string|number;
            name: string;
        }[];
        acceptClickEvent: (e: Event) => void;
        declineClickEvent: (e: Event) => void;
        selectDefaultValue?: number;
        declineDataName? : string;
    }
) {

    const { 
        labelName,
        changeEvent,
        selectName,
        selectDefaultValue,
        selectOptions,
        acceptClickEvent: acceptClickEven,
        declineClickEvent,
        declineDataName: acceptDeclineDataName,
    } = props;

    return (
        <>
            <Label 
                classes='form-inline font-size-1-5 hover padding-r-1 display-block-important'
                text={labelName}
            />
            <div className="form-group">
            <select name={selectName} defaultValue={selectDefaultValue} className="form-control" onChange={(e: Event) => changeEvent(e)}>
                        {
                            selectOptions.map((option: {value: any, name:string}, index: number) => {
                                return (
                                    <option key={index} value={option.value}>{option.name}</option>
                                )
                            })
                        }
                    </select>
            </div>
            <AcceptButton clickEvent={(e: Event) => acceptClickEven(e)} dataName={acceptDeclineDataName} />
            <DeclineButton clickEvent={(e: Event) => declineClickEvent(e)} dataName={acceptDeclineDataName} />
        </>
    )
}