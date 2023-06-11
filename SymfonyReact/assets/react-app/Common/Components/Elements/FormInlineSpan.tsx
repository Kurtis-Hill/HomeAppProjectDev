import * as React from 'react';

export function FormInlineSpan(props: {
    clickEvent: (e: Event) => void;
    spanOuterTag?: string;
    spanInnerTag?: string;
    classesInner?: string;
    classesOuter?: string;
    dataName?: string;
    canEdit?: boolean;
}) {
    const {classesInner, classesOuter, dataName, spanOuterTag, spanInnerTag, clickEvent, canEdit } = props;

    return (
        <span className={`form-inline-span large font-weight-bold form-inline font-size-1-5 padding-r-1 ${classesOuter}`}>{spanOuterTag} <span onClick={(e: Event) => clickEvent(e)} data-name={dataName} className={`padding-l-1 ${canEdit ? 'hover' : null } ${classesInner} `}>{spanInnerTag}</span></span>
    )
}