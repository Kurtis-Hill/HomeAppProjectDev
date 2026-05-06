import * as React from 'react';

export function Label(props: {
    text?: string;
    htmlFor?: string;
    classes?: string;
}) {
    const { text, htmlFor, classes } = props;

    return (
        <label className={`large font-weight-bold ${classes ?? ''}`} htmlFor={htmlFor}>{text}</label>
    )
}