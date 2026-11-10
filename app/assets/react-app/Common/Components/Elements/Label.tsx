import * as React from 'react';

export function Label(props: {
    text?: string;
    htmlFor?: string;
    classes?: string;
}) {
    const { text, htmlFor, classes } = props;

    return (
        <label className={`label-modern ${classes ?? ''}`} htmlFor={htmlFor}>{text}</label>
    )
}
