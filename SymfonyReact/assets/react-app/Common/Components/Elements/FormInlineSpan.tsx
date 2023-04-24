import * as React from 'react';

export function FormInlineSpan(props: {
    spanOuterTag: string;
    spanInnerTag: string;
    clickEvent: (e: Event) => void;
    classesInner?: string;
    classesOuter?: string;
    dataName?: string;
}) {
    const {classesInner, classesOuter, dataName, spanOuterTag, spanInnerTag, clickEvent } = props;

    return (
        <span className={`form-inline-span large font-weight-bold form-inline font-size-1-5 padding-r-1 ${classesOuter}`}>{spanOuterTag} <span onClick={(e: Event) => clickEvent(e)} data-name={dataName} className={`padding-l-1 hover ${classesInner} `}>{spanInnerTag}</span></span>
    )
}