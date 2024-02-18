import * as React from 'react';

export function GeneralTableRow(props: {children?: React.ReactNode}) {
    return (
        <>
            <th scope="row">
                { props.children }
            </th>
        </>
    )
}