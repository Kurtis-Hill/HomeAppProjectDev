import * as React from 'react';

export function FormSelect(props: {
    selectName: string;
    changeEvent: (e: Event) => void;
    selectDefaultValue?: number;
    selectOptions: {
        value: string|number;
        name: string;
    }[];
    dataName?: string;
}) {
    const { selectName, selectDefaultValue, changeEvent, selectOptions, dataName } = props;
    
    return (
        <>
            <select
                name={selectName}
                data-name={dataName}
                defaultValue={selectDefaultValue}
                className="select-modern"
                onChange={(e: React.ChangeEvent<HTMLSelectElement>) => changeEvent(e as unknown as Event)}
            >
                {selectOptions.map((option: {value: any, name: string}, index: number) => (
                    <option key={index} value={option.value}>{option.name}</option>
                ))}
            </select>
        </>
    )
}
